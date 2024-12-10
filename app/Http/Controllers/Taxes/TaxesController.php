<?php

namespace App\Http\Controllers\Taxes;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaxSystemCollection;
use App\Models\Taxes\TaxSystem;

class TaxesController extends Controller
{
    /**
     * Get a list of countries and their capital gains, wealth tax & special rules
     * 
     * @return TaxSystemCollection
     */
    public function getTaxes(): TaxSystemCollection
    {
        return new TaxSystemCollection(TaxSystem::all());
    }
}
