<?php

namespace App\Service\Fire;

use App\DataTransferObjects\FireSimulation\ContributionData;
use App\DataTransferObjects\FireSimulation\FireSimulationData;
use App\DataTransferObjects\FireSimulation\SharesPositionData;
use App\DataTransferObjects\FireSimulation\WithdrawalData;
use App\DataTransferObjects\FireSimulation\WithdrawResultData;
use App\Enums\FrequencyEnum;
use App\Enums\IncreaseFrequencyEnum;
use App\Enums\TaxLotMatchingStrategyEnum;
use App\Enums\TaxSystemEnum;
use App\Service\Fire\FireServiceInterface;
use Exception;
use Illuminate\Support\Facades\DB;

class FireService implements FireServiceInterface
{
    /**
     * Array of all S&P500 share prices per month that we have data on in the format "1871-1" => 4.44
     * @var array
     */
    private array $sharePriceData;

    /**
     * Array of all S&P500 dividend yields we have data on in the format "1871-1" => "0.0216..." (extrapolated from actual quarterly data)
     * @var array
     */
    private array $dividendYieldData;

    /**
     * Array of US inflation that we have data on in the format "1871" => -6.87 (as percentages)
     * @var array
     */
    private array $inflationData;

    public function __construct() {
        $this->sharePriceData    = $this->getMonthlySharePricesData();
        $this->dividendYieldData = $this->getMonthlyDividendYieldsData();
        $this->inflationData     = $this->getYearlyInflationData();
    }

    /**
     * Backtest a given FIRE strategy against historic stock market data
     * 
     * @param FireSimulationData $fireSimulationData The settings with which to do the backtest
     * 
     * @return array
     */
    public function calculateFireCharts(FireSimulationData $fireSimulationData): array
    {
        $yearsOfData = intdiv(sizeof($this->sharePriceData), 12);
        $lastYearOfData = 1871 + $yearsOfData; // TODO: Gotta get rid of this hardcoded 1871, same for in the request, maybe some env var we set on boot? and on seeding so that doesnt desync it?

        $firstRun = $fireSimulationData->dataSince;
        $lastRun = $lastYearOfData - ($fireSimulationData->endAge - $fireSimulationData->startAge);

        $runs = [];
        for ($i = $firstRun; $i <= $lastRun; $i++) {
            $runs[$i] = $this->simulateFireStrategy($i, $fireSimulationData);
        }

        // Very bandaid fixy but idc tbh
        // replace all stock return data with our given flat returns instead
        // and then run the simulation one more time
        $sharePriceDataKeys = array_keys($this->sharePriceData);
        $theoraticalSharePrice = 1.0;
        for ($i = 0; $i < sizeof($sharePriceDataKeys); $i++) {
            $this->sharePriceData[$sharePriceDataKeys[$i]] = $theoraticalSharePrice;
            $theoraticalSharePrice *= pow(1 + $fireSimulationData->flatReturns / 100, 1/12);
        }

        $dividendYieldDataKeys = array_keys($this->dividendYieldData);
        $theoraticalDividendYield = 0;
        for ($i = 0; $i < sizeof($dividendYieldDataKeys); $i++) {
            $this->dividendYieldData[$dividendYieldDataKeys[$i]] = $theoraticalDividendYield;
        }
        $comparisonRun = $this->simulateFireStrategy($lastRun, $fireSimulationData);

        return [
            'settings'      => $fireSimulationData, 
            'runs'          => $runs,
            'comparisonRun' => $comparisonRun,
            'stockData'     => $this->sharePriceData,
            'dividendData'  => $this->dividendYieldData
        ];
    }

    /**
     * Retrieves stock price per month from the database
     * 
     * @return array
     */
    private function getMonthlySharePricesData(): array
    {
        $stockData = DB::table('stock_price_data')->get()->toArray();

        $monthlyData = [];
        for ($i = 0; $i < sizeof($stockData); $i++) {
            $monthlyData[$stockData[$i]->year.'-'.$stockData[$i]->month] = $stockData[$i]->price;
        }

        return $monthlyData;
    }

    /**
     * Retrieves dividend yields per month from the database
     * 
     * @return array
     */
    private function getMonthlyDividendYieldsData(): array
    {
        $stockData = DB::table('stock_price_data')->get()->toArray();

        $monthlyData = [];
        for ($i = 0; $i < sizeof($stockData); $i++) {
            $monthlyData[$stockData[$i]->year.'-'.$stockData[$i]->month] = $stockData[$i]->dividend;
        }

        return $monthlyData;
    }

    /**
     * Retrieves yearly inflation data from the database
     * 
     * @return array
     */
    private function getYearlyInflationData(): array
    {
        $inflationData = DB::table('inflation_data')->get()->toArray();

        $yearlyData = [];
        for ($i = 0; $i < sizeof($inflationData); $i++) {
            $yearlyData[$inflationData[$i]->year] = $inflationData[$i]->inflation;
        }

        return $yearlyData;
    }

    /**
     * Simulate how a strategy holds up if it started on january 1st of the given year
     * 
     * @param int $startYear 
     * @param FireSimulationData $fireSimulationData
     * 
     * @return array
     */
    private function simulateFireStrategy(int $startYear, FireSimulationData $fireSimulationData): array
    {
        $currentAge = $fireSimulationData->startAge;

        // Our array of all the batches of shares we've ever bought both with dividends and with cash
        // We can then match tax lots based on either First In First Out or Last In First Out (FIFO & LIFO)
        /** @var SharesPositionData[] */
        $ownedShares = [];
        $ownedCash = 0; // TODO: Add cash buffer setting

        $startOfYearValue = $fireSimulationData->startBalance; // For the netherlands which taxes you based on fictional returns assumed over the total on jan 1st. The Netherlands suck... Tax reform in 2026 though!
        $capitalLossCredit = 0; // For tax systems that use Capital Loss Carryforward. Aka being able to offset today's capital gains with last year's capital loss.
        $inflationMultiplier = 1; // inflation of 1.0 means no inflation, 0.5 means we deflated by half, 2 means everything is now twice as expensive.

        $totalWithdrawnNominal = 0;
        $totalWithdrawnInflationAdjusted = 0;

        $yearlyContributionsTotalNominal = [$fireSimulationData->startBalance];
        $yearlyContributionsTotalInflationAdjusted = [$fireSimulationData->startBalance];

        $yearlyNetWorthNominal = [$fireSimulationData->startBalance];
        $yearlyNetWorthInflationAdjusted = [$fireSimulationData->startBalance];

        $monthDate = sprintf('%d-%d', $startYear, 1);

        // Invest our initial balance into stocks
        $this->investStartingBalance($monthDate, $ownedShares, $fireSimulationData->startBalance, $fireSimulationData->taxSystem);

        // Start on month 0, go for as many months as there are in the years we are simulating
        // So for 10 years its start on month 0, keep going till month 119 (aka 1-120 if not 0 indexed)
        $amountOfMonthsToSimulate = ($fireSimulationData->endAge - $fireSimulationData->startAge) * 12;
        for ($month = 0; $month < $amountOfMonthsToSimulate; $month++) {

            $currentAge = $fireSimulationData->startAge + ($month / 12);
            $currentYear = intdiv($month, 12) + $startYear;
            $monthDate = sprintf('%d-%d', $currentYear, $month % 12 + 1);

            // Increase inflation data

            // Reinvest Dividends
            if ($month !== 0) {
                $this->reinvestDividends($monthDate, $ownedShares, $fireSimulationData->taxSystem);
            }

            // Invest Contribution
            $contributedAmount = $this->investContributions($monthDate, $ownedShares, $currentAge, $month, $inflationMultiplier, $fireSimulationData->contributions, $fireSimulationData->taxSystem);
            $yearlyContributionsTotalNominal[sizeof($yearlyContributionsTotalNominal)-1]                     += $contributedAmount;
            $yearlyContributionsTotalInflationAdjusted[sizeof($yearlyContributionsTotalInflationAdjusted)-1] += $contributedAmount / $inflationMultiplier;

            // Handle Withdrawal
            if ($fireSimulationData->withdrawals !== null) {
                $withdrawResult = $this->handleWithdrawals($monthDate, $ownedShares, $currentAge, $month, $inflationMultiplier, $fireSimulationData->withdrawals, $fireSimulationData->taxSystem, $fireSimulationData->taxLotMatchingStrategy);
                $withdrawnAmount = $withdrawResult->withdrawAmount - $withdrawResult->outstandingBalance;
                
                $totalWithdrawnNominal           += $withdrawnAmount; 
                $totalWithdrawnInflationAdjusted += $withdrawnAmount / $inflationMultiplier;

                if ($withdrawResult->outstandingBalance > 0.0) {
                    // run failed, pad out the rest of the yearly stats with 0s
                    // Might make this actually not pad in the future depending on how front-end will work
                    $targetLength = intdiv($amountOfMonthsToSimulate, 12);

                    $yearlyNetWorthNominal                     = array_pad($yearlyNetWorthNominal, $targetLength, 0);
                    $yearlyNetWorthInflationAdjusted           = array_pad($yearlyNetWorthInflationAdjusted, $targetLength, 0);
                    $yearlyContributionsTotalNominal           = array_pad($yearlyContributionsTotalNominal, $targetLength, $yearlyContributionsTotalNominal[sizeof($yearlyContributionsTotalNominal) - 1]);
                    $yearlyContributionsTotalInflationAdjusted = array_pad($yearlyContributionsTotalInflationAdjusted, $targetLength, $yearlyContributionsTotalInflationAdjusted[sizeof($yearlyContributionsTotalInflationAdjusted) - 1]);

                    break;
                }
            }

            // End of year, do all the end of year things
            if ($month % 12 === 11) {
                // adjust inflation
                if ($fireSimulationData->useRealInflation) {
                    $inflationMultiplier *= $this->getInflation($currentYear);
                } else {
                    $inflationMultiplier *= 1 + $fireSimulationData->staticInflation / 100;
                }

                // Do all the math with the share price of next month because we want to pretend its jan 1st 12:00am
                $monthDate = sprintf('%d-%d', $currentYear + 1, 1);

                // Update start of year value
                $startOfYearValue = $this->getCurrentSharePrice($monthDate) * $this->getTotalOwnedShares($ownedShares);

                // Append all our arrays of yearly values
                array_push($yearlyNetWorthNominal, floor($this->getCurrentSharePrice($monthDate) * $this->getTotalOwnedShares($ownedShares))); // handle inflation adjustment later
                array_push($yearlyNetWorthInflationAdjusted, floor($this->getCurrentSharePrice($monthDate) * $this->getTotalOwnedShares($ownedShares) / $inflationMultiplier)); // handle inflation adjustment later

                array_push($yearlyContributionsTotalNominal, $yearlyContributionsTotalNominal[sizeof($yearlyContributionsTotalNominal) - 1]);
                $yearlyContributionsTotalInflationAdjusted[sizeof($yearlyContributionsTotalInflationAdjusted) - 1] = floor($yearlyContributionsTotalInflationAdjusted[sizeof($yearlyContributionsTotalInflationAdjusted) - 1]);
                array_push($yearlyContributionsTotalInflationAdjusted, $yearlyContributionsTotalInflationAdjusted[sizeof($yearlyContributionsTotalInflationAdjusted) - 1]);
            }
        }

        return $yearlyNetWorthInflationAdjusted;
    }

    /**
     * Invest the starting balance into shares
     * 
     * @param string               $monthDate
     * @param SharesPositionData[] $ownedShares
     * @param float                $startBalance
     * @param TaxSystemEnum        $taxSystem
     * 
     * @return void
     */
    private function investStartingBalance(string $monthDate, array &$ownedShares, float $startBalance, TaxSystemEnum $taxSystem): void {
        // TODO: Ignore tax system & fees for now, thats a later ticket, normally we'd tax for certain countries (Belgium) and handle fees

        array_push($ownedShares, $this->buyShares($monthDate, $startBalance));
    }

    /**
     * Calculate dividend over owned shares and buy more shares from that
     * 
     * @param string               $monthDate
     * @param SharesPositionData[] $ownedShares
     * @param TaxSystemEnum        $taxSystem
     * 
     * @return void
     */
    private function reinvestDividends(string $monthDate, array &$ownedShares, TaxSystemEnum $taxSystem): void {
        $dividendPayout = $this->getDividendPayout($monthDate, $ownedShares);

        if ($dividendPayout === 0.0) {
            return;
        }

        // TODO: Ignore tax system for now, thats a later ticket, normally we'd tax the dividendPayout here.

        array_push($ownedShares, $this->buyShares($monthDate, $dividendPayout));
    }

    /**
     * Buy shares according to given contributionSettings
     *
     * @param string               $monthDate
     * @param SharesPositionData[] $ownedShares
     * @param float                $currentAge
     * @param int                  $month
     * @param float                $inflationMultiplier
     * @param ContributionData[]   $contributionSettings
     * @param TaxSystemEnum        $taxSystem
     * 
     * @return float
     */
    private function investContributions(string $monthDate, array &$ownedShares, float $currentAge, int $month, float $inflationMultiplier, array &$contributionSettings, TaxSystemEnum $taxSystem): float {
        $investmentAmount = 0.0;

        for ($i = 0; $i < sizeof($contributionSettings); $i++) {
            $contributionSetting = &$contributionSettings[$i];

            // One-Off contribution
            if ($contributionSetting->frequency === FrequencyEnum::ONE_OFF) {
                if ($currentAge === $contributionSetting->startAge) {
                    $investmentAmount += $contributionSetting->amount;
                }

                continue;
            }

            // Periodic contributions
            if ($currentAge >= $contributionSetting->startAge && $currentAge < $contributionSetting->endAge) {
                // Handle periodic investment amount
                if ($contributionSetting->frequency === FrequencyEnum::MONTHLY
                    || ($contributionSetting->frequency === FrequencyEnum::QUARTERLY && ($month % 3 === 0))
                    || ($contributionSetting->frequency === FrequencyEnum::YEARLY && ($month % 12 === 0))
                ) {
                    $investmentAmount += $contributionSetting->amount;
                }

                // Handle periodic amount increase
                if ($month !== 0 && $contributionSetting->increaseFrequency !== null) {
                    if ($contributionSetting->increaseFrequency === IncreaseFrequencyEnum::MONTHLY
                        || ($contributionSetting->increaseFrequency === IncreaseFrequencyEnum::QUARTERLY && ($month % 3 === 0))
                        || ($contributionSetting->increaseFrequency === IncreaseFrequencyEnum::YEARLY && ($month % 12 === 0))
                    ) {
                        $contributionSetting->amount += $contributionSetting->increaseAmount; // I dont really like that we are editing a value in our DTO, but its the easiest way to do compounding changes for now
                    }
                }
            }
        }
        
        $investmentAmount *= $inflationMultiplier;

        if ($investmentAmount === 0.0) {
            return 0.0;
        }

        // TODO: Ignore tax system & fees for now, thats a later ticket, normally we'd tax for certain countries (Belgium) and handle fees

        array_push($ownedShares, $this->buyShares($monthDate, $investmentAmount));

        return $investmentAmount;
    }

    /**
     * Sell shares according to given withdrawalSettings
     * 
     * @param string                     $monthDate
     * @param SharesPositionData[]       $ownedShares
     * @param float                      $currentAge
     * @param int                        $month
     * @param float                      $inflationMultiplier
     * @param WithdrawalData[]           $withdrawalSettings
     * @param TaxSystemEnum              $taxSystem
     * @param TaxLotMatchingStrategyEnum $taxLotMatchingStrategy
     * 
     * @return WithdrawResultData
     */
    private function handleWithdrawals(string $monthDate, array &$ownedShares, float $currentAge, int $month, float $inflationMultiplier, array &$withdrawalSettings, TaxSystemEnum $taxSystem, TaxLotMatchingStrategyEnum $taxLotMatchingStrategy): WithdrawResultData {
        $withdrawAmount = 0.0;

        for ($i = 0; $i < sizeof($withdrawalSettings); $i++) {
            $withdrawalSetting = &$withdrawalSettings[$i];

            // One-Off withdrawal
            if ($withdrawalSetting->frequency === FrequencyEnum::ONE_OFF) {
                if ($currentAge === $withdrawalSetting->startAge) {
                    $withdrawAmount += $withdrawalSetting->amount;
                }

                continue;
            }

            // Periodic withdrawals
            if ($currentAge >= $withdrawalSetting->startAge && $currentAge < $withdrawalSetting->endAge) {
                // Handle periodic withdrawal amount
                if ($withdrawalSetting->frequency === FrequencyEnum::MONTHLY
                    || ($withdrawalSetting->frequency === FrequencyEnum::QUARTERLY && ($month % 3 === 0))
                    || ($withdrawalSetting->frequency === FrequencyEnum::YEARLY && ($month % 12 === 0))
                ) {
                    $withdrawAmount += $withdrawalSetting->amount;
                }

                // Handle periodic amount increase
                if ($withdrawalSetting->increaseFrequency !== null) {
                    if ($withdrawalSetting->increaseFrequency === IncreaseFrequencyEnum::MONTHLY
                        || ($withdrawalSetting->increaseFrequency === IncreaseFrequencyEnum::QUARTERLY && ($month % 3 === 0))
                        || ($withdrawalSetting->increaseFrequency === IncreaseFrequencyEnum::YEARLY && ($month % 12 === 0))
                    ) {
                        $withdrawalSetting->amount += $withdrawalSetting->increaseAmount; // I dont really like that we are editing a value in our DTO, but its the easiest way to do compounding changes for now
                    }
                }      
            }
        }

        $withdrawAmount *= $inflationMultiplier;

        if ($withdrawAmount === 0.0) {
            return WithdrawResultData::from([
                'withdrawAmount'     => 0,
                'outstandingBalance' => 0
            ]);
        }

        return $this->sellShares($monthDate, $withdrawAmount, $ownedShares, $taxSystem, $taxLotMatchingStrategy);
    }

    private function getTaxAmount() {

    }

    /**
     * Get the inflation for the given year
     * 
     * @param int $year
     * 
     * @return float The inflation in comparison to the previous year as a number (1.00 = 100% of last month, so 0.95 would mean 5% deflation)
     */
    private function getInflation(int $year): float {
        if (!isset($this->inflationData[$year])) {
            throw new Exception("Tried to get yearly inflation but couldn't find any data for year: " . $year);
        }

        return 1.0 + ($this->inflationData[$year] / 100);
    }

    /**
     * Get the price of S&P500 shares for the given month
     * 
     * @param string $monthDate
     * 
     * @throws \Exception
     * @return float
     */
    private function getCurrentSharePrice(string $monthDate): float {
        if (!isset($this->sharePriceData[$monthDate])) {
            throw new Exception("Tried to get monthly share price but couldn't find any data for month: " . $monthDate);
        }

        return $this->sharePriceData[$monthDate];
    }

    /**
     * Get the dividend yield of the S&P500 for the given month
     * 
     * @param string $monthDate
     * 
     * @throws Exception
     * @return float
     */
    private function getCurrentDividendYield(string $monthDate): float {
        if (!isset($this->dividendYieldData[$monthDate])) {
            throw new Exception("Tried to get monthly dividend yield but couldn't find any data for month: " . $monthDate);
        }

        return $this->dividendYieldData[$monthDate];
    }

    /**
     * Get the cash amount paid out in dividends for the given month based on how many shares you own
     * 
     * @param string $monthDate
     * @param SharesPositionData[] $ownedShares
     * 
     * @return float
     */
    private function getDividendPayout(string $monthDate, array $ownedShares): float {
        $dividendYieldPerShare = $this->getCurrentDividendYield($monthDate);
        $ownedShares = $this->getTotalOwnedShares($ownedShares);

        return $ownedShares * $dividendYieldPerShare;
    }

    /**
     * Reduces the ownedShares array to get the total sharesAmount of all items
     * 
     * @param SharesPositionData[] $ownedShares
     * 
     * @return float
     */
    private function getTotalOwnedShares(array $ownedShares): float {
        $reducedValue = array_reduce($ownedShares, function ($carry, $item) {
            $carry += $item->amountOfShares;
            return $carry;
        }, 0.0);

        return $reducedValue;
    }
    
    /**
     * Take an amount of money and buy shares for it based on the current monthly share price
     * 
     * @param string $monthDate
     * @param float $investmentAmount
     * 
     * @return array
     */
    private function buyShares(string $monthDate, float $investmentAmount): SharesPositionData {
        $sharePrice = $this->getCurrentSharePrice($monthDate);

        // TODO: Allow for setting fees on buying shares
        return SharesPositionData::from([
            'amountOfShares' => $investmentAmount / $sharePrice,
            'pricePerShare'  => $sharePrice
        ]);
    }

    /**
     * Sell shares for the current monthly share price until the target amount is met
     * Does so according to the tax lot matching strategy
     * 
     * Returns the outstanding amount after selling shares (0.0 if there were enough shares to sell)
     * 
     * @param string                     $monthDate
     * @param float                      $withdrawAmount
     * @param SharesPositionData[]       $ownedShares
     * @param TaxSystemEnum              $taxSystem
     * @param TaxLotMatchingStrategyEnum $taxLotMatchingStrategy
     * 
     * @return WithdrawResultData
     */
    private function sellShares(string $monthDate, float $withdrawAmount, array &$ownedShares, TaxSystemEnum $taxSystem, TaxLotMatchingStrategyEnum $taxLotMatchingStrategy): WithdrawResultData {
        
        $sharePrice = $this->getCurrentSharePrice($monthDate);
        $amountNeededToBeWithdrawn = $withdrawAmount;

        // checking if > 0.1 just so we dont get tiny tiny fractional amounts causing infinite loops
        while ($withdrawAmount > 0.1 && sizeof($ownedShares) > 0) {
            $stockToSellIndex = null;
            $stockToSell = null;
            if ($taxLotMatchingStrategy === TaxLotMatchingStrategyEnum::FIFO) {
                $stockToSellIndex = 0;
                $stockToSell = array_shift($ownedShares);
            } else if ($taxLotMatchingStrategy === TaxLotMatchingStrategyEnum::LIFO) {
                $stockToSellIndex = sizeof($ownedShares) - 1;
                $stockToSell = array_pop($ownedShares);
            } else {
                throw new Exception('Tried to sell shares but found unknown tax lot matching strategy: ' . $taxLotMatchingStrategy);
            }

            // TODO: Ignoring tax system & fees for now, thats a later ticket, normally we'd capital gains tax for certain countries and handle fees
            // need to do some fancy math to take into account capital gains here so we get the amount we need to ACTUALLY withdraw
            if (($stockToSell->amountOfShares * $sharePrice) < $withdrawAmount) {
                // sell all
                $withdrawAmount -= $stockToSell->amountOfShares * $sharePrice;
            } else {
                $amountToSell = $withdrawAmount / $sharePrice;
                $stockToSell->amountOfShares -= $amountToSell;
                $withdrawAmount -= $amountToSell * $sharePrice; // aka should be everything

                // Put the remaining stock back into the list where it was found
                array_splice($ownedShares, $stockToSellIndex, 0, [$stockToSell]);
            }
        }

        // checking if > 0.1 just so we dont get tiny tiny fractional amounts causing infinite loops
        return WithdrawResultData::from([
            'withdrawAmount'     => $amountNeededToBeWithdrawn,
            'outstandingBalance' => ($withdrawAmount > 0.1) ? $withdrawAmount : 0
        ]);
    }
}