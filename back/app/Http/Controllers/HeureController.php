<?php

namespace App\Http\Controllers;

use App\Http\Libraries\ApiResponse;
use App\Http\Requests\HeureRequest;
use App\Http\Resources\HeureResource;
use App\Models\Heure;
use Illuminate\Http\Request;

class HeureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $heures = Heure::with('utilisateur')->get();
            return ApiResponse::success('Liste des heures récupérée avec succès', HeureResource::collection($heures));
        } catch (\Exception $e) {
            return ApiResponse::error('Erreur lors de la récupération des heures', null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HeureRequest $request)
    {
        try {
            $heure = Heure::create($request->validated());
            $heure->load('utilisateur');

            return ApiResponse::created('Heure créée avec succès', new HeureResource($heure));
        } catch (\Exception $e) {
            return ApiResponse::error('Erreur lors de la création de l\'heure', null, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $heure = Heure::with('utilisateur')->find($id);

            if (!$heure) {
                return ApiResponse::notFound('Heure non trouvée');
            }

            return ApiResponse::success('Heure récupérée avec succès', new HeureResource($heure));
        } catch (\Exception $e) {
            return ApiResponse::error('Erreur lors de la récupération de l\'heure', null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HeureRequest $request, string $id)
    {
        try {
            $heure = Heure::find($id);

            if (!$heure) {
                return ApiResponse::notFound('Heure non trouvée');
            }

            $heure->update($request->validated());
            $heure->load('utilisateur');

            return ApiResponse::success('Heure mise à jour avec succès', new HeureResource($heure));
        } catch (\Exception $e) {
            return ApiResponse::error('Erreur lors de la mise à jour de l\'heure', null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $heure = Heure::find($id);

            if (!$heure) {
                return ApiResponse::notFound('Heure non trouvée');
            }

            $heure->delete();

            return ApiResponse::success('Heure supprimée avec succès');
        } catch (\Exception $e) {
            return ApiResponse::error('Erreur lors de la suppression de l\'heure', null, 500);
        }
    }
}
