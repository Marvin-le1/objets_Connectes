<?php

use App\Http\Controllers\BadgeController;
use App\Http\Controllers\BadgeageController;
use App\Http\Controllers\HeureController;
use App\Http\Controllers\HoraireController;
use App\Http\Controllers\UtilisateurController;
use Illuminate\Support\Facades\Route;

// Routes Badges
Route::prefix('badges')->group(function () {
    Route::get   ('/'    , [BadgeController::class, 'index'  ]);
    Route::post  ('/'    , [BadgeController::class, 'store'  ]);
    Route::get   ('/{id}', [BadgeController::class, 'show'   ]);
    Route::put   ('/{id}', [BadgeController::class, 'update' ]);
    Route::delete('/{id}', [BadgeController::class, 'destroy']);
});

// Routes Utilisateurs
Route::prefix('utilisateurs')->group(function () {
    Route::get   ('/'    , [UtilisateurController::class, 'index'  ]);
    Route::post  ('/'    , [UtilisateurController::class, 'store'  ]);
    Route::get   ('/{id}', [UtilisateurController::class, 'show'   ]);
    Route::put   ('/{id}', [UtilisateurController::class, 'update' ]);
    Route::delete('/{id}', [UtilisateurController::class, 'destroy']);
});

// Routes Heures
Route::prefix('heures')->group(function () {
    Route::get   ('/'    , [HeureController::class, 'index'  ]);
    Route::post  ('/'    , [HeureController::class, 'store'  ]);
    Route::get   ('/{id}', [HeureController::class, 'show'   ]);
    Route::put   ('/{id}', [HeureController::class, 'update' ]);
    Route::delete('/{id}', [HeureController::class, 'destroy']);
});

// Routes Horaires
Route::prefix('horaires')->group(function () {
    Route::get   ('/'    , [HoraireController::class, 'index'  ]);
    Route::post  ('/'    , [HoraireController::class, 'store'  ]);
    Route::get   ('/{id}', [HoraireController::class, 'show'   ]);
    Route::put   ('/{id}', [HoraireController::class, 'update' ]);
    Route::delete('/{id}', [HoraireController::class, 'destroy']);
});

// Routes Badgeage
Route::prefix('badgeage')->group(function () {
    Route::post('/badger'                    , [BadgeageController::class, 'badger'    ]);
    Route::get ('/historique/{utilisateurId}', [BadgeageController::class, 'historique']);
    Route::post('/rapport'                   , [BadgeageController::class, 'rapport'   ]);
    Route::get ('/statut/{utilisateurId}'    , [BadgeageController::class, 'statut'    ]);
});
