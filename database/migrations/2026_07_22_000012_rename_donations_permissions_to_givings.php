<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $map = [
            'donations.read' => 'givings.read',
            'donations.create' => 'givings.create',
            'donations.update' => 'givings.update',
            'donations.delete' => 'givings.delete',
            'donations.export' => 'givings.export',
            'donations.approve' => 'givings.approve',
        ];

        foreach ($map as $old => $new) {
            DB::table('permissions')
                ->where('key', $old)
                ->update([
                    'key' => $new,
                    'label' => str_replace('Donation', 'Giving', str_replace('donation', 'giving', ucfirst(str_replace('.', ' ', $new)))),
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        $map = [
            'givings.read' => 'donations.read',
            'givings.create' => 'donations.create',
            'givings.update' => 'donations.update',
            'givings.delete' => 'donations.delete',
            'givings.export' => 'donations.export',
            'givings.approve' => 'donations.approve',
        ];

        foreach ($map as $old => $new) {
            DB::table('permissions')
                ->where('key', $old)
                ->update([
                    'key' => $new,
                    'label' => str_replace('Giving', 'Donation', str_replace('giving', 'donation', ucfirst(str_replace('.', ' ', $new)))),
                    'updated_at' => now(),
                ]);
        }
    }
};
