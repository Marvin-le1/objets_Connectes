<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            ['numero' => 8271219C],
            ['numero' => A27F6C39],
            ['numero' => B9C37359],
        ];

        foreach ($badges as $badge) {
            Badge::create($badge);
        }
    }
}
