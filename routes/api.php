<?php

use App\Http\Controllers\Fire\FireController;
use App\Http\Controllers\Taxes\TaxesController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(static function () {
    Route::prefix('taxes')->group(static function () {
        Route::get('/', [TaxesController::class, 'getTaxes']);
    });

    Route::prefix('fire')->group(static function () {
        Route::post('/', [FireController::class, 'calculateFireCharts']);
    });
});
