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
            ['numero' => 1001],
            ['numero' => 1002],
            ['numero' => 1003],
            ['numero' => 1004],
            ['numero' => 1005],
            ['numero' => 1006],
            ['numero' => 1007],
            ['numero' => 1008],
            ['numero' => 1009],
            ['numero' => 1010],
        ];

        foreach ($badges as $badge) {
            Badge::create($badge);
        }
    }
}
