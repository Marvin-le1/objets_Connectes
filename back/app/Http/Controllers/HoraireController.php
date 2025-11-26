<?php

namespace App\Http\Controllers;

use App\Http\Libraries\ApiResponse;
use App\Http\Requests\HoraireRequest;
use App\Http\Resources\HoraireResource;
use App\Models\Horaire;
use Illuminate\Http\Request;

class HoraireController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $horaires = Horaire::with('utilisateurs')->get();
            return ApiResponse::success('Liste des horaires récupérée avec succès', HoraireResource::collection($horaires));
        } catch (\Exception $e) {
            return ApiResponse::error('Erreur lors de la récupération des horaires', null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HoraireRequest $request)
    {
        try {
            $horaire = Horaire::create($request->validated());
            $horaire->load('utilisateurs');

            return ApiResponse::created('Horaire créé avec succès', new HoraireResource($horaire));
        } catch (\Exception $e) {
            return ApiResponse::error('Erreur lors de la création de l\'horaire', null, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $horaire = Horaire::with('utilisateurs')->find($id);

            if (!$horaire) {
                return ApiResponse::notFound('Horaire non trouvé');
            }

            return ApiResponse::success('Horaire récupéré avec succès', new HoraireResource($horaire));
        } catch (\Exception $e) {
            return ApiResponse::error('Erreur lors de la récupération de l\'horaire', null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HoraireRequest $request, string $id)
    {
        try {
            $horaire = Horaire::find($id);

            if (!$horaire) {
                return ApiResponse::notFound('Horaire non trouvé');
            }

            $horaire->update($request->validated());
            $horaire->load('utilisateurs');

            return ApiResponse::success('Horaire mis à jour avec succès', new HoraireResource($horaire));
        } catch (\Exception $e) {
            return ApiResponse::error('Erreur lors de la mise à jour de l\'horaire', null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $horaire = Horaire::find($id);

            if (!$horaire) {
                return ApiResponse::notFound('Horaire non trouvé');
            }

            $horaire->delete();

            return ApiResponse::success('Horaire supprimé avec succès');
        } catch (\Exception $e) {
            return ApiResponse::error('Erreur lors de la suppression de l\'horaire', null, 500);
        }
    }
}
