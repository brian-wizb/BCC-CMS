<?php

use App\Models\Member;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Member::query()
            ->where('share_partner_tithe_code', true)
            ->whereNotNull('partner_member_id')
            ->orderBy('id')
            ->chunkById(100, function ($members): void {
                foreach ($members as $member) {
                    $partner = Member::query()->find($member->partner_member_id);

                    if (! $partner) {
                        continue;
                    }

                    $anchor = $member;

                    if ($member->created_at !== null && $partner->created_at !== null) {
                        if ($partner->created_at->lt($member->created_at) || ($partner->created_at->eq($member->created_at) && $partner->id < $member->id)) {
                            $anchor = $partner;
                        }
                    } elseif ($member->created_at === null && $partner->created_at !== null) {
                        $anchor = $partner;
                    } elseif ($partner->created_at === null && $partner->id < $member->id) {
                        $anchor = $partner;
                    }

                    $sharedTitheCode = $anchor->tithe_code ?: ($member->tithe_code ?: ($partner->tithe_code ?: Member::nextTitheCode()));

                    Member::query()->whereKey($member->id)->update([
                        'tithe_code' => $sharedTitheCode,
                        'share_partner_tithe_code' => true,
                    ]);

                    Member::query()->whereKey($partner->id)->update([
                        'tithe_code' => $sharedTitheCode,
                        'share_partner_tithe_code' => true,
                    ]);
                }
            });
    }

    public function down(): void
    {
    }
};
