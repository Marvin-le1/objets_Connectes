<?php

namespace App\Http\Controllers;

use App\Http\Libraries\ApiResponse;
use App\Http\Requests\UtilisateurRequest;
use App\Http\Resources\UtilisateurResource;
use App\Models\Utilisateur;
use Illuminate\Http\Request;

class UtilisateurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $utilisateurs = Utilisateur::with('badge')->get();
            return ApiResponse::success('Liste des utilisateurs récupérée avec succès', UtilisateurResource::collection($utilisateurs));
        } catch (\Exception $e) {
            return ApiResponse::error('Erreur lors de la récupération des utilisateurs', null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UtilisateurRequest $request)
    {
        try {
            $utilisateur = Utilisateur::create($request->validated());
            $utilisateur->load('badge');

            return ApiResponse::created('Utilisateur créé avec succès', new UtilisateurResource($utilisateur));
        } catch (\Exception $e) {
            return ApiResponse::error('Erreur lors de la création de l\'utilisateur', null, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $utilisateur = Utilisateur::with('badge')->find($id);

            if (!$utilisateur) {
                return ApiResponse::notFound('Utilisateur non trouvé');
            }

            return ApiResponse::success('Utilisateur récupéré avec succès', new UtilisateurResource($utilisateur));
        } catch (\Exception $e) {
            return ApiResponse::error('Erreur lors de la récupération de l\'utilisateur', null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UtilisateurRequest $request, string $id)
    {
        try {
            $utilisateur = Utilisateur::find($id);

            if (!$utilisateur) {
                return ApiResponse::notFound('Utilisateur non trouvé');
            }

            $utilisateur->update($request->validated());
            $utilisateur->load('badge');

            return ApiResponse::success('Utilisateur mis à jour avec succès', new UtilisateurResource($utilisateur));
        } catch (\Exception $e) {
            return ApiResponse::error('Erreur lors de la mise à jour de l\'utilisateur', null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $utilisateur = Utilisateur::find($id);

            if (!$utilisateur) {
                return ApiResponse::notFound('Utilisateur non trouvé');
            }

            $utilisateur->delete();

            return ApiResponse::success('Utilisateur supprimé avec succès');
        } catch (\Exception $e) {
            return ApiResponse::error('Erreur lors de la suppression de l\'utilisateur', null, 500);
        }
    }
}
