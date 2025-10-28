<?php

namespace Database\Seeders;

use App\Models\Horaire;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HoraireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $horaires = [
            // Utilisateur 1 - Jean Dupont
            [
                'entree_matin' => Carbon::now()->setTime(8, 30, 0),
                'sortie_midi' => Carbon::now()->setTime(12, 0, 0),
                'entree_midi' => Carbon::now()->setTime(13, 30, 0),
                'sortie_soir' => Carbon::now()->setTime(17, 30, 0),
                'utilisateur_id' => 1,
            ],
            // Utilisateur 2 - Marie Martin
            [
                'entree_matin' => Carbon::now()->setTime(9, 0, 0),
                'sortie_midi' => Carbon::now()->setTime(12, 30, 0),
                'entree_midi' => Carbon::now()->setTime(14, 0, 0),
                'sortie_soir' => Carbon::now()->setTime(18, 0, 0),
                'utilisateur_id' => 2,
            ],
            // Utilisateur 3 - Pierre Durand
            [
                'entree_matin' => Carbon::now()->setTime(8, 0, 0),
                'sortie_midi' => Carbon::now()->setTime(12, 0, 0),
                'entree_midi' => Carbon::now()->setTime(13, 0, 0),
                'sortie_soir' => Carbon::now()->setTime(17, 0, 0),
                'utilisateur_id' => 3,
            ],
            // Utilisateur 4 - Sophie Lefebvre
            [
                'entree_matin' => Carbon::now()->setTime(8, 45, 0),
                'sortie_midi' => Carbon::now()->setTime(12, 15, 0),
                'entree_midi' => Carbon::now()->setTime(13, 45, 0),
                'sortie_soir' => Carbon::now()->setTime(17, 45, 0),
                'utilisateur_id' => 4,
            ],
            // Utilisateur 5 - Luc Moreau
            [
                'entree_matin' => Carbon::now()->setTime(7, 30, 0),
                'sortie_midi' => Carbon::now()->setTime(11, 30, 0),
                'entree_midi' => Carbon::now()->setTime(12, 30, 0),
                'sortie_soir' => Carbon::now()->setTime(16, 30, 0),
                'utilisateur_id' => 5,
            ],
            // Utilisateur 6 - Claire Simon
            [
                'entree_matin' => Carbon::now()->setTime(9, 0, 0),
                'sortie_midi' => Carbon::now()->setTime(13, 0, 0),
                'entree_midi' => Carbon::now()->setTime(14, 0, 0),
                'sortie_soir' => Carbon::now()->setTime(18, 0, 0),
                'utilisateur_id' => 6,
            ],
            // Utilisateur 7 - Thomas Laurent
            [
                'entree_matin' => Carbon::now()->setTime(8, 15, 0),
                'sortie_midi' => Carbon::now()->setTime(12, 0, 0),
                'entree_midi' => Carbon::now()->setTime(13, 15, 0),
                'sortie_soir' => Carbon::now()->setTime(17, 15, 0),
                'utilisateur_id' => 7,
            ],
            // Utilisateur 8 - Julie Bernard
            [
                'entree_matin' => Carbon::now()->setTime(9, 30, 0),
                'sortie_midi' => Carbon::now()->setTime(12, 30, 0),
                'entree_midi' => Carbon::now()->setTime(14, 0, 0),
                'sortie_soir' => Carbon::now()->setTime(18, 30, 0),
                'utilisateur_id' => 8,
            ],
            // Utilisateur 9 - Marc Petit
            [
                'entree_matin' => Carbon::now()->setTime(7, 0, 0),
                'sortie_midi' => Carbon::now()->setTime(11, 0, 0),
                'entree_midi' => Carbon::now()->setTime(12, 0, 0),
                'sortie_soir' => Carbon::now()->setTime(16, 0, 0),
                'utilisateur_id' => 9,
            ],
            // Utilisateur 10 - Emma Robert
            [
                'entree_matin' => Carbon::now()->setTime(8, 30, 0),
                'sortie_midi' => Carbon::now()->setTime(12, 30, 0),
                'entree_midi' => Carbon::now()->setTime(13, 30, 0),
                'sortie_soir' => Carbon::now()->setTime(17, 30, 0),
                'utilisateur_id' => 10,
            ],
        ];

        foreach ($horaires as $horaire) {
            Horaire::create($horaire);
        }
    }
}
