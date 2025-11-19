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
            ['numero' => 2188779548],
            ['numero' => 2721857337],
            ['numero' => 3116338777],
        ];

        foreach ($badges as $badge) {
            Badge::create($badge);
        }
    }
}
