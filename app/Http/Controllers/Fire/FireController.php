<?php

namespace App\Http\Controllers\Fire;

use App\Http\Controllers\Controller;
use App\Http\Requests\Fire\FireRequest;
use App\Service\Fire\FireServiceInterface;

class FireController extends Controller
{
    /**
     * Get a list of countries and their capital gains, wealth tax & special rules
     * 
     * @return array
     */
    public function calculateFireCharts(FireRequest $fireRequest, FireServiceInterface $fireService): array
    {
        return $fireService->calculateFireCharts($fireRequest->validated());
    }
}
