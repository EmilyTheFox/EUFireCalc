<?php

namespace App\DataTransferObjects\FireSimulation;

use Spatie\LaravelData\Data;

class WithdrawResultData extends Data
{
    public function __construct(
        public float $withdrawAmount,
        public float $outstandingBalance
    ) {}
}