<?php

namespace App\Console\Commands;

use App\Http\Controllers\AlertController;
use Illuminate\Console\Command;

class GenerateAlerts extends Command
{
    protected $signature   = 'alerts:generate';
    protected $description = 'Auto-generate alerts for inactive members, overdue pastoral cases, and stale prayer requests';

    public function handle(AlertController $alertController): int
    {
        $this->info('Running alert generator…');

        $created = $alertController->generateAlerts();

        $this->info("Done. Created {$created} new alert(s).");

        return Command::SUCCESS;
    }
}
