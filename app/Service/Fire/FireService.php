<?php

namespace App\Service\Fire;

use App\DataTransferObjects\FireSimulation\ContributionData;
use App\DataTransferObjects\FireSimulation\FireSimulationData;
use App\DataTransferObjects\FireSimulation\WithdrawalData;
use App\Enums\FrequencyEnum;
use App\Enums\IncreaseFrequencyEnum;
use App\Enums\TaxSystemEnum;
use App\Service\Fire\FireServiceInterface;
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

    public function __construct() {
        $this->sharePriceData = $this->getMonthlySharePrices();
        $this->dividendYieldData = $this->getMonthlyDividendYields();
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
            $runs[$i] = $this->simulateFireStrategy($i, $fireSimulationData, false);
        }

        $comparisonRun = $this->simulateFireStrategy($lastRun, $fireSimulationData, true);

        return [
            'settings'      => $fireSimulationData, 
            'runs'          => $runs,
            'comparisonRun' => $comparisonRun,
            'stockData'     => $this->sharePriceData,
            'dividendData'  => $this->dividendYieldData
        ];
    }   

    /**
     * Retrieves stock price per month starting from january of the given year till the most recent datapoint
     * 
     * @param int $sinceYear The first year to get data from
     * 
     * @return array
     */
    private function getMonthlySharePrices(): array
    {
        $stockData = DB::table('stock_price_data')->get()->toArray();

        $monthlyData = [];
        for ($i = 0; $i < sizeof($stockData); $i++) {
            $monthlyData[$stockData[$i]->year.'-'.$stockData[$i]->month] = $stockData[$i]->price;
        }

        return $monthlyData;
    }

    /**
     * Retrieves yearly dividend yields per month starting from january of the given year till the most recent datapoint
     * 
     * @param int $sinceYear The first year to get data from
     * 
     * @return array
     */
    private function getMonthlyDividendYields(): array
    {
        $stockData = DB::table('stock_price_data')->get()->toArray();

        $monthlyData = [];
        for ($i = 0; $i < sizeof($stockData); $i++) {
            $monthlyData[$stockData[$i]->year.'-'.$stockData[$i]->month] = $stockData[$i]->dividend;
        }

        return $monthlyData;
    }

    /**
     * Simulate how a strategy holds up if it started on january 1st of the given year
     * 
     * @param int $startYear 
     * @param FireSimulationData $fireSimulationData
     * 
     * @return array
     */
    private function simulateFireStrategy(int $startYear, FireSimulationData $fireSimulationData, bool $useComparisonRate): array
    {
        // $runFailed = false;
        $currentAge = $fireSimulationData->startAge;

        // Our array of all the batches of shares we've ever bought both with dividends and with cash
        // We can then match tax lots based on either First In First Out or First In Last Out (FIFO & FILO)
        $ownedShares = [];

        $ownedCash = 0; // TODO: Add cash buffer setting

        $totalContributedNominal = $fireSimulationData->startBalance;
        $totalContributedInflationAdjusted = $fireSimulationData->startBalance;
        $totalWithdrawnNominal = 0;
        $totalWithdrawnInflationAdjusted = 0;

        $startOfYearValue = $fireSimulationData->startBalance; // For the netherlands which taxes you based on fictional returns assumed over the total on jan 1st. The Netherlands suck... Tax reform in 2026 though!
        $capitalLossCredit = 0; // For tax systems that use Capital Loss Carryforward. Aka being able to offset today's capital gains with last year's capital loss.

        $inflationMultiplier = 1; // inflation of 1.0 means no inflation, 0.5 means we deflated by half, 2 means everything is now twice as expensive.

        $yearlyContributions = [$fireSimulationData->startBalance];
        $yearlyNetWorth = [$fireSimulationData->startBalance];

        $currentMonth = sprintf('%d-%d', $startYear, 1);
        $this->investStartingBalance($currentMonth, $ownedShares, $fireSimulationData->startBalance, $fireSimulationData->taxSystem);

        // Start on month 0, go for as many months as there are in the years we are simulating
        // So for 10 years its start on month 0, keep going till month 119 (aka 1-120 if not 0 indexed)
        $amountOfMonthsToSimulate = ($fireSimulationData->endAge - $fireSimulationData->startAge) * 12;
        for ($month = 0; $month < $amountOfMonthsToSimulate; $month++) {

            // TBH this should just be done better when we hit this, like just find how many years are left and fill the array
            // if ($runFailed) {
            //     if ($month % 12 === 0) {
            //         array_push($yearlyNetWorth, 0);
            //         array_push($yearlyContributions, $yearlyContributions[sizeof($yearlyContributions) - 1]);
            //     }

            //     continue;
            // }

            $currentAge = $fireSimulationData->startAge + ($month / 12);
            $currentMonth = sprintf('%d-%d', intdiv($month, 12) + $startYear, $month % 12 + 1);

            // Reinvest Dividends
            $this->reinvestDividends($currentMonth, $ownedShares, $fireSimulationData->taxSystem);

            // Invest Contribution
            $this->investContributions($currentMonth, $ownedShares, $currentAge, $month, $inflationMultiplier, $fireSimulationData->contributions, $fireSimulationData->taxSystem);

            // Handle Withdrawal
            if ($fireSimulationData->withdrawals !== null) {
                $this->handleWithdrawals($currentMonth, $ownedShares, $currentAge, $month, $inflationMultiplier, $fireSimulationData->withdrawals, $fireSimulationData->taxSystem);
            }

            // December, do end of year things
            if ($month % 12 === 11) {
                array_push($yearlyNetWorth, floor($this->getCurrentSharePrice($currentMonth) * $this->getTotalOwnedShares($ownedShares)));
            }
        }

        return $yearlyNetWorth;
    }

    private function investStartingBalance(string $monthDate, array &$ownedShares, float $startBalance, TaxSystemEnum $taxSystem): void {
        // TODO: Ignore tax system & fees for now, thats a later ticket, normally we'd tax for certain countries (Belgium) and handle fees

        array_push($ownedShares, $this->buyShares($monthDate, $startBalance));
    }

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
     * @param string             $monthDate
     * @param array              $ownedShares
     * @param float              $currentAge
     * @param int                $month
     * @param float              $inflationMultiplier
     * @param ContributionData[] $contributionSettings
     * @param TaxSystemEnum      $taxSystem
     * 
     * @return void
     */
    private function investContributions(string $monthDate, array &$ownedShares, float $currentAge, int $month, float $inflationMultiplier, array &$contributionSettings, TaxSystemEnum $taxSystem): void {
        $investmentAmount = 0.0;

        for ($i = 0; $i < sizeof($contributionSettings); $i++) {
            $contributionSetting = &$contributionSettings[$i];

            // One-Off contribution
            if ($contributionSetting->frequency === FrequencyEnum::ONE_OFF) {
                if ($currentAge === $contributionSetting->startAge) {
                    $investmentAmount += $contributionSetting->amount * $inflationMultiplier;
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
                    $matchInflation = $contributionSetting->increaseFrequency === IncreaseFrequencyEnum::MATCH_INFLATION;

                    $investmentAmount += $contributionSetting->amount * ($matchInflation ? $inflationMultiplier : 1);
                }  

                // Handle periodic amount increase
                if ($contributionSetting->increaseFrequency !== null) {
                    if ($contributionSetting->increaseFrequency === IncreaseFrequencyEnum::MONTHLY
                        || ($contributionSetting->increaseFrequency === IncreaseFrequencyEnum::QUARTERLY && ($month % 3 === 0))
                        || ($contributionSetting->increaseFrequency === IncreaseFrequencyEnum::YEARLY && ($month % 12 === 0))
                    ) {
                        $contributionSetting->amount += $contributionSetting->increaseAmount; // I dont really like that we are editing a value in our DTO, but its the easiest way to do compounding changes for now
                    }
                }              
            }
        }

        if ($investmentAmount === 0.0) {
            return;
        }

        // TODO: Ignore tax system & fees for now, thats a later ticket, normally we'd tax for certain countries (Belgium) and handle fees

        array_push($ownedShares, $this->buyShares($monthDate, $investmentAmount));
    }

    /**
     * Sell shares according to given withdrawalSettings
     * 
     * @param string           $monthDate
     * @param array            $ownedShares
     * @param float            $currentAge
     * @param int              $month
     * @param float            $inflationMultiplier
     * @param WithdrawalData[] $withdrawalSettings
     * @param TaxSystemEnum    $taxSystem
     * 
     * @return void
     */
    private function handleWithdrawals(string $monthDate, array &$ownedShares, float $currentAge, int $month, float $inflationMultiplier, array &$withdrawalSettings, TaxSystemEnum $taxSystem) {

    }

    private function getTaxAmount() {

    }

    private function getInflation() {

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
            throw new \Exception("Tried to get monthly share price but couldn't find any data for month: " . $monthDate);
        }

        return $this->sharePriceData[$monthDate];
    }

    /**
     * Get the dividend yield of the S&P500 for the given month
     * 
     * @param string $monthDate
     * 
     * @throws \Exception
     * @return float
     */
    private function getCurrentDividendYield(string $monthDate): float {
        if (!isset($this->dividendYieldData[$monthDate])) {
            throw new \Exception("Tried to get monthly dividend yield but couldn't find any data for month: " . $monthDate);
        }

        return $this->dividendYieldData[$monthDate];
    }

    /**
     * Get the cash amount paid out in dividends for the given month based on how many shares you own
     * 
     * @param string $monthDate
     * @param array $ownedShares
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
     * @param array $ownedShares
     * @return float
     */
    private function getTotalOwnedShares(array $ownedShares): float {
        $reducedValue = array_reduce($ownedShares, function ($carry, $item) {
            $carry += $item['sharesAmount'];
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
    private function buyShares(string $monthDate, float $investmentAmount): array {
        $sharePrice = $this->getCurrentSharePrice($monthDate);

        // TODO: Allow for setting fees on buying shares
        return [
            "sharesAmount" => $investmentAmount / $sharePrice,
            "sharesPrice" => $sharePrice
        ];
    }
}