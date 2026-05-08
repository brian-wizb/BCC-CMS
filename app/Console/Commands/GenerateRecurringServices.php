<?php

namespace App\Console\Commands;

use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateRecurringServices extends Command
{
    protected $signature   = 'attendance:generate-recurring
                                {--weeks=4 : How many weeks ahead to generate}';

    protected $description = 'Generate upcoming service dates for recurring services';

    public function handle(): int
    {
        $weeksAhead = (int) $this->option('weeks');
        $horizon    = now()->addWeeks($weeksAhead);
        $generated  = 0;

        $recurring = Service::query()
            ->whereNotNull('recurrence_rule')
            ->where('recurrence_rule', '!=', 'none')
            ->get();

        foreach ($recurring as $service) {
            $nextDate = $this->nextDate($service->service_date, $service->recurrence_rule);

            while ($nextDate <= $horizon) {
                $exists = Service::query()
                    ->where('name', $service->name)
                    ->where('service_date', $nextDate->toDateString())
                    ->exists();

                if (! $exists) {
                    Service::query()->create([
                        'name'             => $service->name,
                        'service_type'     => $service->service_type,
                        'service_date'     => $nextDate->toDateString(),
                        'start_time'       => $service->start_time,
                        'end_time'         => $service->end_time,
                        'location'         => $service->location,
                        'description'      => $service->description,
                        'recurrence_rule'  => $service->recurrence_rule,
                        'attendance_mode'  => $service->attendance_mode,
                    ]);

                    $this->info("Created: {$service->name} on {$nextDate->format('d M Y')}");
                    $generated++;
                }

                $nextDate = $this->nextDate($nextDate, $service->recurrence_rule);
            }
        }

        $this->info("Done. {$generated} service(s) generated.");

        return self::SUCCESS;
    }

    private function nextDate(Carbon|string $from, string $rule): Carbon
    {
        $date = $from instanceof Carbon ? $from->copy() : Carbon::parse($from);

        return match ($rule) {
            'weekly'    => $date->addWeek(),
            'biweekly'  => $date->addWeeks(2),
            'monthly'   => $date->addMonth(),
            default     => $date->addWeek(),
        };
    }
}
