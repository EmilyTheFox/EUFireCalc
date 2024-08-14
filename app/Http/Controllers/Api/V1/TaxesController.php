<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\TaxSystemCollection;
use App\Models\TaxSystem;

class TaxesController
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
