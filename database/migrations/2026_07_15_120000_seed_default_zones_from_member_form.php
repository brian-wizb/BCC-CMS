<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $zones = [
            'Diaspora outside tz',
            'Diaspora inside tz',
            'Kigamboni',
            'Kigamboni-Sinza',
            'Ubungo-Kimara Mbezi',
            'Mgomeni-External',
            'Temeke-Mbagala',
            'Tabata-Kinyerezi',
            'City Center-Mikocheni',
            'Vingunguti-Pugu',
        ];

        foreach ($zones as $zoneName) {
            $exists = DB::table('zones')->where('name', $zoneName)->exists();

            if (! $exists) {
                DB::table('zones')->insert([
                    'name' => $zoneName,
                    'leader_id' => null,
                    'description' => null,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        // No-op to avoid removing user-managed zone data.
    }
};
