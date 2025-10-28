<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HeureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'entree_sortie' => $this->entree_sortie,
            'entree_sortie_label' => $this->entree_sortie ? 'Entrée' : 'Sortie',
            'heure' => $this->heure->format('Y-m-d H:i:s'),
            'heure_formatee' => $this->heure->format('d/m/Y à H:i'),
            'utilisateur' => [
                'id' => $this->utilisateur->id ?? null,
                'nom' => $this->utilisateur->nom ?? null,
                'prenom' => $this->utilisateur->prenom ?? null,
                'email' => $this->utilisateur->email ?? null,
            ],
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
