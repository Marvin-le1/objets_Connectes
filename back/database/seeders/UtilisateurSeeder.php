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
            ],
            [
                'nom' => 'Lefebvre',
                'prenom' => 'Sophie',
                'service' => 'Marketing',
                'badge_id' => 4,
                'horaire_id' => 2,
            ],
            [
                'nom' => 'Moreau',
                'prenom' => 'Luc',
                'service' => 'Production',
                'badge_id' => 5,
                'horaire_id' => 2,
            ],
            [
                'nom' => 'Simon',
                'prenom' => 'Claire',
                'service' => 'Commercial',
                'badge_id' => 6,
                'horaire_id' => 2,
            ],
            [
                'nom' => 'Laurent',
                'prenom' => 'Thomas',
                'service' => 'Informatique',
                'badge_id' => 7,
                'horaire_id' => 1,
            ],
            [
                'nom' => 'Bernard',
                'prenom' => 'Julie',
                'service' => 'Direction',
                'badge_id' => 8,
                'horaire_id' => 2,
            ],
            [
                'nom' => 'Petit',
                'prenom' => 'Marc',
                'service' => 'Logistique',
                'badge_id' => 9,
                'horaire_id' => 2,
            ],
            [
                'nom' => 'Robert',
                'prenom' => 'Emma',
                'service' => 'Service Client',
                'badge_id' => 10,
                'horaire_id' => 1,
            ],
        ];

        foreach ($utilisateurs as $utilisateur) {
            Utilisateur::create($utilisateur);
        }
    }
}
