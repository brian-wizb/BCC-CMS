<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            [
                'name'        => 'Praise and Worship',
                'description' => 'The music, choir, and worship team responsible for leading the congregation in praise and adoration during services.',
                'icon'        => 'fa-music',
                'color'       => '#a855f7',
            ],
            [
                'name'        => 'Ushers',
                'description' => 'Welcome and guide congregation members, manage seating, assist with offering collection, and maintain order during services.',
                'icon'        => 'fa-door-open',
                'color'       => '#3b82f6',
            ],
            [
                'name'        => 'Protocol',
                'description' => 'Handles official procedures, guests of honour, VIP seating, and ceremonial protocols during church events and services.',
                'icon'        => 'fa-scroll',
                'color'       => '#f59e0b',
            ],
            [
                'name'        => 'Prayer Warriors',
                'description' => 'Dedicated intercessors committed to prayer ministry, prayer chains, and spiritual warfare on behalf of the congregation.',
                'icon'        => 'fa-pray',
                'color'       => '#6366f1',
            ],
            [
                'name'        => 'Hospitality',
                'description' => 'Responsible for welcoming guests, preparing refreshments, coordinating meals, and creating a warm environment for all visitors.',
                'icon'        => 'fa-utensils',
                'color'       => '#ec4899',
            ],
            [
                'name'        => 'Counsellors',
                'description' => 'Trained counsellors providing emotional support, spiritual guidance, and pastoral counselling to individuals and families in need.',
                'icon'        => 'fa-hand-holding-heart',
                'color'       => '#ef4444',
            ],
            [
                'name'        => 'Church Environment',
                'description' => 'Maintains the cleanliness, organization, decoration, and general upkeep of the church premises and facilities.',
                'icon'        => 'fa-broom',
                'color'       => '#10b981',
            ],
            [
                'name'        => 'Media',
                'description' => 'Manages sound systems, live streaming, recording, social media, projections, and all audiovisual technology during services and events.',
                'icon'        => 'fa-video',
                'color'       => '#0ea5e9',
            ],
        ];

        foreach ($groups as $data) {
            Group::firstOrCreate(
                ['slug' => Str::slug($data['name'])],
                array_merge($data, [
                    'slug'         => Str::slug($data['name']),
                    'is_predefined' => true,
                ])
            );
        }
    }
}
