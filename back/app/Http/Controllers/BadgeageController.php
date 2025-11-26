<?php

namespace App\Http\Controllers;

use App\Http\Libraries\ApiResponse;
use App\Models\Badge;
use App\Models\Heure;
use App\Models\Horaire;
use App\Models\Utilisateur;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BadgeageController extends Controller
{
    /**
     * Badgeage d'un utilisateur
     * Détermine automatiquement s'il s'agit d'une entrée ou sortie
     * 
     * @param Request $request (numero du badge)
     * @return JsonResponse
     */
    public function badger(Request $request)
    {
        $request->validate([
            'numero' => 'required|integer|exists:badges,numero',
        ]);

        try {
            // recup badge
            $badge = Badge::where('numero', $request->numero)->first();
            
            if (!$badge) {
                return ApiResponse::notFound('Badge non trouvé');
            }

            // recup l'user du badge
            $utilisateur = Utilisateur::where('badge_id', $badge->id)->first();
            
            if (!$utilisateur) {
                return ApiResponse::error('Aucun utilisateur associé à ce badge', null, 404);
            }

            // recup l'horaire de l'user
            $horaire = $utilisateur->horaire;
            
            if (!$horaire) {
                return ApiResponse::error('Aucun horaire défini pour cet utilisateur', null, 404);
            }

            // heure actuelle
            $now = Carbon::now('Europe/Paris');

            // recup le dernier badgeage du jour
            $dernierBadgeage = Heure::where('utilisateur_id', $utilisateur->id)
                ->whereDate('heure', $now->toDateString())
                ->orderBy('heure', 'desc')
                ->first();

            // entrée ou sortie ?
            $typeBadgeage = $this->determinerTypeBadgeage($now, $horaire, $dernierBadgeage);

            if ($typeBadgeage === null) {
                return ApiResponse::error('Badgeage hors horaires autorisés', [
                    'heure_actuelle' => $now->format('H:i'),
                    'message' => 'Vous ne pouvez pas badger à cette heure'
                ], 400);
            }

            // verif si on a badgé deux fois dans les 2 min
            $badgeageDuplique = Heure::where('utilisateur_id', $utilisateur->id)
                ->where('entree_sortie', $typeBadgeage)
                ->where('heure', '>=', $now->copy()->subMinutes(2))
                ->exists();

            if ($badgeageDuplique) {
                return ApiResponse::error('Badgeage déjà enregistré', null, 400);
            }

            // créer badgeage
            $heure = Heure::create([
                'entree_sortie' => $typeBadgeage,
                'heure' => $now,
                'utilisateur_id' => $utilisateur->id,
            ]);

            // calcul des heures travaillées du jour
            $heuresTravaillees = $this->calculerHeuresTravaillees($utilisateur->id, $now);

            return ApiResponse::success('Badgeage enregistré avec succès', [
                'utilisateur' => [
                    'nom' => $utilisateur->nom,
                    'prenom' => $utilisateur->prenom,
                    'service' => $utilisateur->service,
                ],
                'badgeage' => [
                    'type' => $typeBadgeage ? 'Entrée' : 'Sortie',
                    'heure' => $now->format('H:i:s'),
                    'date' => $now->format('d/m/Y'),
                ],
                'heures_travaillees_ojd' => $heuresTravaillees,
            ]);

        } catch (\Exception $e) {
            return ApiResponse::error('Erreur lors du badgeage: ' . $e->getMessage(), null, 500);
        }
    }

    /**
     * Détermine si le badgeage est une entrée ou sortie
     * 
     * @param Carbon $now
     * @param Horaire $horaire
     * @param Heure|null $dernierBadgeage
     * @return bool|null
     */
    private function determinerTypeBadgeage(Carbon $now, Horaire $horaire, $dernierBadgeage)
    {
        // convert horaires en Carbon pour ojd 
        $entreeMatin = Carbon::parse($horaire->entree_matin)->setTimezone('Europe/Paris')->setDate($now->year, $now->month, $now->day);
        $sortieMidi  = Carbon::parse($horaire->sortie_midi)->setTimezone('Europe/Paris')->setDate($now->year, $now->month, $now->day);
        $entreeMidi  = Carbon::parse($horaire->entree_midi)->setTimezone('Europe/Paris')->setDate($now->year, $now->month, $now->day);
        $sortieSoir  = Carbon::parse($horaire->sortie_soir)->setTimezone('Europe/Paris')->setDate($now->year, $now->month, $now->day);

        // marges
        $margeAvant = 30; // 30 min avant
        $margeApres = 30; // 30 min après
        $margeHeuresSup = 240; // 4 h pour heures sup

        // détermine si c'est une entrée ou une sortie en fonction des horaires de l'utilisateur
        // 30min avant à 30min après l'horaire d'entrée matin
        $debutEntreeMatin = $entreeMatin->copy()->subMinutes($margeAvant);
        $finEntreeMatin = $entreeMatin->copy()->addMinutes($margeApres);
        if ($now->between($debutEntreeMatin, $finEntreeMatin, true)) {
            return true;
        }
        
        // 30min avant à 30min après l'horaire de sortie midi
        $debutSortieMidi = $sortieMidi->copy()->subMinutes($margeAvant);
        $finSortieMidi = $sortieMidi->copy()->addMinutes($margeApres);
        if ($now->between($debutSortieMidi, $finSortieMidi, true)) {
            return false;
        }
        
        // 30min avant à 30min après l'horaire d'entrée midi
        $debutEntreeMidi = $entreeMidi->copy()->subMinutes($margeAvant);
        $finEntreeMidi = $entreeMidi->copy()->addMinutes($margeApres);
        if ($now->between($debutEntreeMidi, $finEntreeMidi, true)) {
            return true;
        }
        
        // 30min avant à 4h après l'horaire de sortie soir (heures sup)
        $debutSortieSoir = $sortieSoir->copy()->subMinutes($margeAvant);
        $finSortieSoir = $sortieSoir->copy()->addMinutes($margeHeuresSup);
        if ($now->between($debutSortieSoir, $finSortieSoir, true)) {
            return false;
        }

        return null; // hors horaire
    }

    /**
     * Calcule les heures travaillées (en un jour)
     * 
     * @param int $utilisateurId
     * @param Carbon $date
     * @return string
     */
    private function calculerHeuresTravaillees($utilisateurId, Carbon $date)
    {
        $utilisateur = Utilisateur::find($utilisateurId);
        if (!$utilisateur || !$utilisateur->horaire) {
            return '0h00';
        }

        $horaire = $utilisateur->horaire;
        
        $badgeages = Heure::where('utilisateur_id', $utilisateurId)
            ->whereDate('heure', $date->toDateString())
            ->orderBy('heure', 'asc')
            ->get();

        if ($badgeages->isEmpty()) {
            return '0h00';
        }

        // horaires théoriques pour compléter les badgeages oubliés
        $entreeMatin = Carbon::parse($horaire->entree_matin)->setTimezone('Europe/Paris')->setDate($date->year, $date->month, $date->day);
        $sortieMidi  = Carbon::parse($horaire->sortie_midi )->setTimezone('Europe/Paris')->setDate($date->year, $date->month, $date->day);
        $entreeMidi  = Carbon::parse($horaire->entree_midi )->setTimezone('Europe/Paris')->setDate($date->year, $date->month, $date->day);
        $sortieSoir  = Carbon::parse($horaire->sortie_soir )->setTimezone('Europe/Paris')->setDate($date->year, $date->month, $date->day);

        $totalMinutes = 0;
        $i = 0;
        $traites = []; // ta pour marquer les indices déjà traités

        while ($i < $badgeages->count()) {
            // déjà traité -> suivant
            if (in_array($i, $traites)) {
                $i++;
                continue;
            }

            $badgeageActuel = $badgeages[$i];
            
            // Cas 1 : entrée
            if ($badgeageActuel->entree_sortie == true) {
                $heureEntree = Carbon::parse($badgeageActuel->heure);
                
                // search prochaine sortie
                $sortie = null;
                $sortieIndex = null;
                for ($j = $i + 1; $j < $badgeages->count(); $j++) {
                    if ($badgeages[$j]->entree_sortie == false) {
                        $sortie = $badgeages[$j];
                        $sortieIndex = $j;
                        break;
                    }
                }
                
                // pas de sortie trouvée -> horaire théorique
                if (!$sortie) {
                    if ($heureEntree->lt($sortieMidi)) {
                        $heureSortie = $sortieMidi;
                    } else {
                        $heureSortie = $sortieSoir;
                    }
                } else {
                    $heureSortie = Carbon::parse($sortie->heure);
                    $traites[] = $sortieIndex; // marquer sortie comme traitée
                }
                
                $totalMinutes += $heureEntree->diffInMinutes($heureSortie);
                $traites[] = $i; // marquer entrée comme traitée
                $i++;
            }
            // Cas 2 : sortie sans entrée précédente
            else {
                $heureSortie = Carbon::parse($badgeageActuel->heure);
                
                // search quelle entrée théorique utiliser
                if ($heureSortie->lte($sortieMidi->addMinutes(30))) {
                    // sortie du matin -> utiliser entrée matin théorique
                    $heureEntree = $entreeMatin;
                } else {
                    // sortie de l'après-midi -> utiliser entrée midi théorique
                    $heureEntree = $entreeMidi;
                }
                
                $totalMinutes += $heureEntree->diffInMinutes($heureSortie);
                $traites[] = $i; // marquer sortie comme traitée
                $i++;
            }
        }

        $heures = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        return sprintf('%dh%02d', $heures, $minutes);
    }

    /**
     * Retourne l'historique des badgeages d'un user
     * 
     * @param int $utilisateurId
     * @return JsonResponse
     */
    public function historique($utilisateurId)
    {
        try {
            $utilisateur = Utilisateur::find($utilisateurId);
            
            if (!$utilisateur) {
                return ApiResponse::notFound('Utilisateur non trouvé');
            }

            $badgeages = Heure::where('utilisateur_id', $utilisateurId)
                ->orderBy('heure', 'desc')
                ->limit(50)
                ->get()
                ->map(function ($heure) {
                    return [
                        'id' => $heure->id,
                        'type' => $heure->entree_sortie ? 'Entrée' : 'Sortie',
                        'heure' => Carbon::parse($heure->heure)->format('H:i:s'),
                        'date' => Carbon::parse($heure->heure)->format('d/m/Y'),
                        'timestamp' => $heure->heure,
                    ];
                });

            return ApiResponse::success('Historique des badgeages récupéré avec succès', [
                'utilisateur' => [
                    'nom' => $utilisateur->nom,
                    'prenom' => $utilisateur->prenom,
                ],
                'badgeages' => $badgeages,
            ]);

        } catch (\Exception $e) {
            return ApiResponse::error('Erreur lors de la récupération de l\'historique', null, 500);
        }
    }

    /**
     * Retourne le rapport des heures travaillées dans une période
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function rapport(Request $request)
    {
        $request->validate([
            'utilisateur_id' => 'required|integer|exists:utilisateurs,id',
            'date_debut' => 'required|string',
            'date_fin' => 'required|string',
        ]);

        try {
            // convert date format (jj/mm/aaaa) -> Carbon
            $dateDebut = Carbon::createFromFormat('d/m/Y', $request->date_debut)->startOfDay();
            $dateFin   = Carbon::createFromFormat('d/m/Y', $request->date_fin  )->endOfDay  ();
            
            if ($dateFin->lt($dateDebut)) {
                return ApiResponse::error('La date de fin doit être postérieure ou égale à la date de début', 400);
            }

            $utilisateur = Utilisateur::find($request->utilisateur_id);

            $badgeages = Heure::where('utilisateur_id', $request->utilisateur_id)
                ->whereBetween('heure', [$dateDebut->startOfDay(), $dateFin->endOfDay()])
                ->orderBy('heure', 'asc')
                ->get();

            // regrouppe par jour
            $rapportParJour = [];
            $totalMinutes = 0;

            foreach ($badgeages->groupBy(function ($item) {
                return Carbon::parse($item->heure)->format('Y-m-d');
            }) as $date => $badgeagesDuJour) {
                
                // Utilise la fonction calculerHeuresTravaillees pour chaque jour
                $dateCarbon = Carbon::parse($date);
                $heuresTravailleesStr = $this->calculerHeuresTravaillees($request->utilisateur_id, $dateCarbon);
                
                preg_match('/(\d+)h(\d+)/', $heuresTravailleesStr, $matches);
                $heuresDuJour = isset($matches[1]) ? (int)$matches[1] : 0;
                $minutesDuJour = isset($matches[2]) ? (int)$matches[2] : 0;
                $minutesTotalesDuJour = ($heuresDuJour * 60) + $minutesDuJour;
                
                $totalMinutes += $minutesTotalesDuJour;

                $rapportParJour[] = [
                    'date' => Carbon::parse($date)->format('d/m/Y'),
                    'heures_travaillees' => $heuresTravailleesStr,
                    'nombre_badgeages' => $badgeagesDuJour->count(),
                ];
            }

            $totalHeures = floor($totalMinutes / 60);
            $totalMinutesRestantes = $totalMinutes % 60;

            return ApiResponse::success('Rapport généré avec succès', [
                'utilisateur' => [
                    'nom' => $utilisateur->nom,
                    'prenom' => $utilisateur->prenom,
                ],
                'periode' => [
                    'debut' => $dateDebut->format('d/m/Y'),
                    'fin' => $dateFin->format('d/m/Y'),
                ],
                'details_par_jour' => $rapportParJour,
                'total_heures_travaillees' => sprintf('%dh%02d', $totalHeures, $totalMinutesRestantes),
            ]);

        } catch (\Exception $e) {
            return ApiResponse::error('Erreur lors de la génération du rapport', null, 500);
        }
    }

    /**
     * Vérifie statut actuel de l'user (travail ?)
     * 
     * @param int $utilisateurId
     * @return JsonResponse
     */
    public function statut($utilisateurId)
    {
        try {
            $utilisateur = Utilisateur::find($utilisateurId);
            
            if (!$utilisateur) {
                return ApiResponse::notFound('Utilisateur non trouvé');
            }

            $dernierBadgeage = Heure::where('utilisateur_id', $utilisateurId)
                ->whereDate('heure', Carbon::today('Europe/Paris'))
                ->orderBy('heure', 'desc')
                ->first();

            $auTravail = $dernierBadgeage && $dernierBadgeage->entree_sortie;

            $heuresTravaillees = '0h00';
            if ($dernierBadgeage) {
                $heuresTravaillees = $this->calculerHeuresTravaillees($utilisateurId, Carbon::now());
            }

            return ApiResponse::success('Statut récupéré avec succès', [
                'utilisateur' => [
                    'nom' => $utilisateur->nom,
                    'prenom' => $utilisateur->prenom,
                ],
                'au_travail' => $auTravail,
                'dernier_badgeage' => $dernierBadgeage ? [
                    'type' => $dernierBadgeage->entree_sortie ? 'Entrée' : 'Sortie',
                    'heure' => Carbon::parse($dernierBadgeage->heure)->format('H:i:s'),
                ] : null,
                'heures_travaillees_ojd' => $heuresTravaillees,
            ]);

        } catch (\Exception $e) {
            return ApiResponse::error('Erreur lors de la récupération du statut', null, 500);
        }
    }
}
