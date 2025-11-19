<?php

namespace Database\Seeders;

use App\Models\Heure;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HeureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $heures = [
            // Utilisateur 1 - Jean Dupont
            [
                'entree_sortie' => true,
                'heure' => Carbon::now()->subDays(5)->setTime(8, 30, 0),
                'utilisateur_id' => 1,
            ],
            [
                'entree_sortie' => false,
                'heure' => Carbon::now()->subDays(5)->setTime(17, 45, 0),
                'utilisateur_id' => 1,
            ],
            // Utilisateur 2 - Marie Martin
            [
                'entree_sortie' => true,
                'heure' => Carbon::now()->subDays(4)->setTime(9, 0, 0),
                'utilisateur_id' => 2,
            ],
            [
                'entree_sortie' => false,
                'heure' => Carbon::now()->subDays(4)->setTime(18, 15, 0),
                'utilisateur_id' => 2,
            ],
            // Utilisateur 3 - Pierre Durand
            [
                'entree_sortie' => true,
                'heure' => Carbon::now()->subDays(3)->setTime(8, 15, 0),
                'utilisateur_id' => 3,
            ],
            [
                'entree_sortie' => false,
                'heure' => Carbon::now()->subDays(3)->setTime(17, 30, 0),
                'utilisateur_id' => 3,
            ],
        ];

        foreach ($heures as $heure) {
            Heure::create($heure);
        }
    }
}
