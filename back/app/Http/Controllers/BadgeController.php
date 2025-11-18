<?php

namespace App\Http\Controllers;

use App\Http\Libraries\ApiResponse;
use App\Http\Requests\BadgeRequest;
use App\Http\Resources\BadgeResource;
use App\Models\Badge;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $badges = Badge::all();
            return ApiResponse::success('Liste des badges récupérée avec succès', BadgeResource::collection($badges));
        } catch (\Exception $e) {
            return ApiResponse::error('Erreur lors de la récupération des badges', null, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BadgeRequest $request)
    {
        try {
            $badge = Badge::create($request->validated());

            return ApiResponse::created('Badge créé avec succès', new BadgeResource($badge));
        } catch (\Exception $e) {
            return ApiResponse::error('Erreur lors de la création du badge', null, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $badge = Badge::find($id);

            if (!$badge) {
                return ApiResponse::notFound('Badge non trouvé');
            }

            return ApiResponse::success('Badge récupéré avec succès', new BadgeResource($badge));
        } catch (\Exception $e) {
            return ApiResponse::error('Erreur lors de la récupération du badge', null, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BadgeRequest $request, string $id)
    {
        try {
            $badge = Badge::find($id);

            if (!$badge) {
                return ApiResponse::notFound('Badge non trouvé');
            }

            $badge->update($request->validated());

            return ApiResponse::success('Badge mis à jour avec succès', new BadgeResource($badge));
        } catch (\Exception $e) {
            return ApiResponse::error('Erreur lors de la mise à jour du badge', null, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $badge = Badge::find($id);

            if (!$badge) {
                return ApiResponse::notFound('Badge non trouvé');
            }

            $badge->delete();

            return ApiResponse::success('Badge supprimé avec succès');
        } catch (\Exception $e) {
            return ApiResponse::error('Erreur lors de la suppression du badge', null, 500);
        }
    }
}
