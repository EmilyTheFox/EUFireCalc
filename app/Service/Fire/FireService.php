<?php

namespace App\Service\Fire;

use App\Service\Fire\FireServiceInterface;

class FireService implements FireServiceInterface
{
    public function calculateFireCharts(array $validated): array
    {
        return ['test' => $validated];
    }   
}