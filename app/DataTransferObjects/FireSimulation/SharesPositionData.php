<?php

namespace App\DataTransferObjects\FireSimulation;

use Spatie\LaravelData\Data;

class SharesPositionData extends Data
{
    public function __construct(
        public float $amountOfShares,
        public float $pricePerShare,
    ) {}
}