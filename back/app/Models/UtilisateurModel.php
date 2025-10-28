<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Utilisateur extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'utilisateurs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'service',
        'badge_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'badge_id' => 'integer',
    ];

    /**
     * Get the badge that belongs to the utilisateur.
     */
    public function badge()
    {
        return $this->belongsTo(Badge::class, 'badge_id');
    }

    /**
     * Get the heures for the utilisateur.
     */
    public function heures()
    {
        return $this->hasMany(Heure::class, 'utilisateur_id');
    }

    /**
     * Get the horaires for the utilisateur.
     */
    public function horaires()
    {
        return $this->hasMany(Horaire::class, 'utilisateur_id');
    }
}
