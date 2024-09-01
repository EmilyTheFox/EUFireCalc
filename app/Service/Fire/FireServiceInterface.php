<?php

namespace App\Service\Fire;

use App\DataTransferObjects\FireSimulation\FireSimulationData;

interface FireServiceInterface
{
    public function calculateFireCharts(FireSimulationData $fireSimulationData): array;
}