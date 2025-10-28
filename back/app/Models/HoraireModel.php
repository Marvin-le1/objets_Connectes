<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horaire extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'horaires';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'entree_matin',
        'sortie_midi',
        'entree_midi',
        'sortie_soir',
        'utilisateur_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'entree_matin' => 'datetime',
        'sortie_midi' => 'datetime',
        'entree_midi' => 'datetime',
        'sortie_soir' => 'datetime',
    ];

    /**
     * Get the utilisateur that owns the horaire.
     */
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }
}
