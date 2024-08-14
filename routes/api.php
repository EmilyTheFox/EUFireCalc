<?php

use App\Http\Controllers\Api\V1\TaxesController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(static function () {
    Route::get('taxes', [TaxesController::class, 'getTaxes']);
});