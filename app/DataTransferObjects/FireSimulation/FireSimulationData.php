<?php

namespace App\DataTransferObjects\FireSimulation;

use App\Enums\TaxLotMatchingStrategyEnum;
use Spatie\LaravelData\Data;
use App\Enums\TaxSystemEnum;

class FireSimulationData extends Data
{
    /**
    * @param array<int, ContributionData> $contributions
    * @param array<int, WithdrawalData> $withdrawals
    */
    public function __construct(
          public int $startAge,
          public int $endAge,
          public bool $useRealInflation,
          public ?float $staticInflation,
          public ?float $flatReturns,
          public TaxSystemEnum $taxSystem,
          public TaxLotMatchingStrategyEnum $taxLotMatchingStrategy,
          public int $dataSince,
          public int $startBalance,

          public array $contributions,
          public ?array $withdrawals
    ) {}
}