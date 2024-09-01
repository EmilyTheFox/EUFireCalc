<?php

namespace App\DataTransferObjects\FireSimulation;

use App\Enums\FrequencyEnum;
use App\Enums\IncreaseFrequencyEnum;
use Spatie\LaravelData\Data;

class ContributionData extends Data
{
    public function __construct(
        public int $startAge,
        public ?int $endAge,
        public float $amount,
        public FrequencyEnum $frequency,
        public ?IncreaseFrequencyEnum $increaseFrequency,
        public ?float $increaseAmount
    ) {}
}