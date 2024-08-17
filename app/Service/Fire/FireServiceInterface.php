<?php

namespace App\Service\Fire;

interface FireServiceInterface
{
    public function calculateFireCharts(array $validated): array;
}