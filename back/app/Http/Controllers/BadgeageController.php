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
        $badgeages = Heure::where('utilisateur_id', $utilisateurId)
            ->whereDate('heure', $date->toDateString())
            ->orderBy('heure', 'asc')
            ->get();

        $totalMinutes = 0;

        // calcul entre entrées et sorties en vérifiant l'alternance
        for ($i = 0; $i < $badgeages->count() - 1; $i++) {
            $badgeageActuel = $badgeages[$i];
            $badgeageSuivant = $badgeages[$i + 1];
            
            if ($badgeageActuel->entree_sortie == true && $badgeageSuivant->entree_sortie == false) {
                $entree = Carbon::parse($badgeageActuel->heure);
                $sortie = Carbon::parse($badgeageSuivant->heure);
                $totalMinutes += $entree->diffInMinutes($sortie);
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
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
        ]);

        try {
            $utilisateur = Utilisateur::find($request->utilisateur_id);
            $dateDebut = Carbon::parse($request->date_debut);
            $dateFin = Carbon::parse($request->date_fin);

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
                
                $minutesDuJour = 0;
                
                for ($i = 0; $i < $badgeagesDuJour->count(); $i += 2) {
                    if (isset($badgeagesDuJour[$i + 1])) {
                        $entree = Carbon::parse($badgeagesDuJour[$i]->heure);
                        $sortie = Carbon::parse($badgeagesDuJour[$i + 1]->heure);
                        $minutesDuJour += $entree->diffInMinutes($sortie);
                    }
                }

                $totalMinutes += $minutesDuJour;
                
                $heures = floor($minutesDuJour / 60);
                $minutes = $minutesDuJour % 60;

                $rapportParJour[] = [
                    'date' => Carbon::parse($date)->format('d/m/Y'),
                    'heures_travaillees' => sprintf('%dh%02d', $heures, $minutes),
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
