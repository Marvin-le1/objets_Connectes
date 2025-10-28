<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HoraireResource extends JsonResource
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
            'entree_matin' => $this->entree_matin?->format('Y-m-d H:i:s'),
            'entree_matin_formatee' => $this->entree_matin?->format('d/m/Y à H:i'),
            'sortie_midi' => $this->sortie_midi?->format('Y-m-d H:i:s'),
            'sortie_midi_formatee' => $this->sortie_midi?->format('d/m/Y à H:i'),
            'entree_midi' => $this->entree_midi?->format('Y-m-d H:i:s'),
            'entree_midi_formatee' => $this->entree_midi?->format('d/m/Y à H:i'),
            'sortie_soir' => $this->sortie_soir?->format('Y-m-d H:i:s'),
            'sortie_soir_formatee' => $this->sortie_soir?->format('d/m/Y à H:i'),
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
