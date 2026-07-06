<?php

namespace App\Console\Commands;

use App\Models\Member;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NormalizeMemberTitheCodes extends Command
{
    protected $signature = 'members:normalize-tithe-codes {--dry-run : Show the changes without saving them}';

    protected $description = 'Rename non-TC member tithe codes into the TC0XXX format.';

    public function handle(): int
    {
        $members = Member::query()
            ->orderBy('id')
            ->get(['id', 'full_name', 'tithe_code'])
            ->reject(function (Member $member): bool {
                return is_string($member->tithe_code) && preg_match('/^TC0\d{3}$/', $member->tithe_code) === 1;
            })
            ->values();

        if ($members->isEmpty()) {
            $this->info('No member tithe codes need normalization.');

            return self::SUCCESS;
        }

        $nextSequence = $this->nextSequenceFromCurrentData();
        $dryRun = (bool) $this->option('dry-run');

        $this->info('Found '.$members->count().' member(s) to normalize.');
        $this->line('Starting from TC0'.str_pad((string) $nextSequence, 3, '0', STR_PAD_LEFT));

        $updated = 0;

        DB::transaction(function () use ($members, &$nextSequence, $dryRun, &$updated): void {
            foreach ($members as $member) {
                $newCode = sprintf('TC0%03d', $nextSequence++);

                if ($dryRun) {
                    $this->line($member->id.' | '.($member->tithe_code ?: 'NULL').' => '.$newCode.' | '.$member->full_name);
                    continue;
                }

                $member->update(['tithe_code' => $newCode]);
                $updated++;
                $this->line('Updated '.$member->full_name.' to '.$newCode);
            }
        });

        if ($dryRun) {
            $this->info('Dry run completed. No changes were saved.');
        } else {
            $this->info('Normalization completed. Updated '.$updated.' member(s).');
        }

        return self::SUCCESS;
    }

    private function nextSequenceFromCurrentData(): int
    {
        $highest = Member::query()
            ->where('tithe_code', 'like', 'TC0%')
            ->pluck('tithe_code')
            ->map(function (?string $code): int {
                if (! is_string($code) || ! preg_match('/^TC0(\d{3})$/', $code, $matches)) {
                    return 0;
                }

                return (int) $matches[1];
            })
            ->max() ?? 0;

        return $highest + 1;
    }
}
