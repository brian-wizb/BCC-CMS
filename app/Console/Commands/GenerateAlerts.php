<?php

namespace App\Console\Commands;

use App\Services\AlertService;
use Illuminate\Console\Command;

class GenerateAlerts extends Command
{
    protected $signature   = 'alerts:generate';
    protected $description = 'Auto-generate alerts for inactive members, overdue pastoral cases, and stale prayer requests';

    public function handle(AlertService $alertService): int
    {
        $this->info('Generating alerts…');

        $created = $alertService->generateAlerts();

        $this->info("Done. Created {$created} new alert(s).");

        return Command::SUCCESS;
    }
}
