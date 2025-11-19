<?php

namespace Database\Seeders;

use App\Models\Utilisateur;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UtilisateurSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $utilisateurs = [
            [
                'nom' => 'Dupont',
                'prenom' => 'Jean',
                'service' => 'Informatique',
                'badge_id' => 1,
                'horaire_id' => 2,
            ],
            [
                'nom' => 'Martin',
                'prenom' => 'Marie',
                'service' => 'Ressources Humaines',
                'badge_id' => 2,
                'horaire_id' => 2,
            ],
            [
                'nom' => 'Durand',
                'prenom' => 'Pierre',
                'service' => 'Comptabilité',
                'badge_id' => 3,
                'horaire_id' => 2,
            ]
        ];

        foreach ($utilisateurs as $utilisateur) {
            Utilisateur::create($utilisateur);
        }
    }
}
