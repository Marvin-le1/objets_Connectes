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
            // Horaire 1 - 9 - 17h
            [
                'entree_matin' => Carbon::now()->setTime(9, 0, 0),
                'sortie_midi' => Carbon::now()->setTime(12, 30, 0),
                'entree_midi' => Carbon::now()->setTime(13, 30, 0),
                'sortie_soir' => Carbon::now()->setTime(17, 00, 0),
            ],
            // Horaire 2 - 7h-16h
            [
                'entree_matin' => Carbon::now()->setTime(7, 0, 0),
                'sortie_midi' => Carbon::now()->setTime(11, 0, 0),
                'entree_midi' => Carbon::now()->setTime(12, 0, 0),
                'sortie_soir' => Carbon::now()->setTime(16, 0, 0),
            ],
        ];

        foreach ($horaires as $horaire) {
            Horaire::create($horaire);
        }
    }
}
