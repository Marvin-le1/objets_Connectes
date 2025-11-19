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
            // Utilisateur 1 - Jean Dupont (3jours)
            // Jour 1
            [ 'entree_sortie' => true,  'heure' => Carbon::now()->subDays(10)->setTime(8, 30, 0), 'utilisateur_id' => 1 ],
            [ 'entree_sortie' => false, 'heure' => Carbon::now()->subDays(10)->setTime(12, 0, 0), 'utilisateur_id' => 1 ],
            [ 'entree_sortie' => true,  'heure' => Carbon::now()->subDays(10)->setTime(13, 0, 0), 'utilisateur_id' => 1 ],
            [ 'entree_sortie' => false, 'heure' => Carbon::now()->subDays(10)->setTime(17, 45, 0), 'utilisateur_id' => 1 ],
            // Jour 2
            [ 'entree_sortie' => true,  'heure' => Carbon::now()->subDays(9)->setTime(8, 30, 0), 'utilisateur_id' => 1 ],
            [ 'entree_sortie' => false, 'heure' => Carbon::now()->subDays(9)->setTime(12, 0, 0), 'utilisateur_id' => 1 ],
            [ 'entree_sortie' => true,  'heure' => Carbon::now()->subDays(9)->setTime(13, 0, 0), 'utilisateur_id' => 1 ],
            [ 'entree_sortie' => false, 'heure' => Carbon::now()->subDays(9)->setTime(17, 50, 0), 'utilisateur_id' => 1 ],
            // Jour 3
            [ 'entree_sortie' => true,  'heure' => Carbon::now()->subDays(8)->setTime(8, 30, 0), 'utilisateur_id' => 1 ],
            [ 'entree_sortie' => false, 'heure' => Carbon::now()->subDays(8)->setTime(12, 0, 0), 'utilisateur_id' => 1 ],
            [ 'entree_sortie' => true,  'heure' => Carbon::now()->subDays(8)->setTime(13, 0, 0), 'utilisateur_id' => 1 ],
            [ 'entree_sortie' => false, 'heure' => Carbon::now()->subDays(8)->setTime(17, 40, 0), 'utilisateur_id' => 1 ],

            // Utilisateur 2 - Marie Martin (2 jours)
            // Jour 1
            [ 'entree_sortie' => true,  'heure' => Carbon::now()->subDays(7)->setTime(9, 0, 0), 'utilisateur_id' => 2 ],
            [ 'entree_sortie' => false, 'heure' => Carbon::now()->subDays(7)->setTime(12, 0, 0), 'utilisateur_id' => 2 ],
            [ 'entree_sortie' => true,  'heure' => Carbon::now()->subDays(7)->setTime(13, 0, 0), 'utilisateur_id' => 2 ],
            [ 'entree_sortie' => false, 'heure' => Carbon::now()->subDays(7)->setTime(18, 15, 0), 'utilisateur_id' => 2 ],
            // Jour 2
            [ 'entree_sortie' => true,  'heure' => Carbon::now()->subDays(6)->setTime(9, 0, 0), 'utilisateur_id' => 2 ],
            [ 'entree_sortie' => false, 'heure' => Carbon::now()->subDays(6)->setTime(12, 0, 0), 'utilisateur_id' => 2 ],
            [ 'entree_sortie' => true,  'heure' => Carbon::now()->subDays(6)->setTime(13, 0, 0), 'utilisateur_id' => 2 ],
            [ 'entree_sortie' => false, 'heure' => Carbon::now()->subDays(6)->setTime(18, 20, 0), 'utilisateur_id' => 2 ],

            // Utilisateur 3 - Pierre Durand (1 jours)
            // Jour 1
            [ 'entree_sortie' => true,  'heure' => Carbon::now()->subDays(5)->setTime(8, 15, 0), 'utilisateur_id' => 3 ],
            [ 'entree_sortie' => false, 'heure' => Carbon::now()->subDays(5)->setTime(12, 0, 0), 'utilisateur_id' => 3 ],
            [ 'entree_sortie' => true,  'heure' => Carbon::now()->subDays(5)->setTime(13, 0, 0), 'utilisateur_id' => 3 ],
            [ 'entree_sortie' => false, 'heure' => Carbon::now()->subDays(5)->setTime(17, 30, 0), 'utilisateur_id' => 3 ],
        ];

        foreach ($heures as $heure) {
            Heure::create($heure);
        }
    }
}
