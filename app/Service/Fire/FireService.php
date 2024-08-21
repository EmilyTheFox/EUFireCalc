<?php

namespace App\Service\Fire;

use App\Service\Fire\FireServiceInterface;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class FireService implements FireServiceInterface
{
    /**
     * Backtest a given FIRE strategy against historic stock market data
     * 
     * @param array $settings The settings with which to do the backtest
     * 
     * @return array
     */
    public function calculateFireCharts(array $settings): array
    {
        $stockPriceData = $this->getMonthlyStockPrices($settings['dataSince']);
        $dividendYieldData = $this->getMonthlyDividendYields($settings['dataSince']);

        $firstRun = $settings['dataSince'];
        $lastRun = $firstRun + intdiv(sizeof($stockPriceData), 12) - ($settings['endAge'] - $settings['startAge']);

        $runs = [];
        for ($i = $firstRun; $i <= $lastRun; $i++) {
            $runs[$i] = $this->simulateFireStrategy($i, $settings, $stockPriceData, $dividendYieldData);
        }

        return [
            'settings'     => $settings, 
            'runs'         => $runs,
            'stockData'    => $stockPriceData,
            'dividendData' => $dividendYieldData
        ];
    }   

    /**
     * Retrieves stock price per month starting from january of the given year till the most recent datapoint
     * 
     * @param int $sinceYear The first year to get data from
     * 
     * @return array
     */
    private function getMonthlyStockPrices(int $sinceYear): array
    {
        $stockData = DB::table('stock_price_data')->where('year', '>=', $sinceYear)->get()->toArray();

        if ($stockData[0]->year !== $sinceYear || $stockData[0]->month !== 1) {
            // TODO: Add better exception handling. Not that it should be possible to reach this if the request went through form validation and the database is seeded
            throw new InvalidArgumentException('No stock price data available for year ' . $sinceYear .', try a later date');
        }

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
    private function getMonthlyDividendYields(int $sinceYear): array
    {
        $stockData = DB::table('stock_price_data')->where('year', '>=', $sinceYear)->get()->toArray();

        if ($stockData[0]->year !== $sinceYear || $stockData[0]->month !== 1) {
            // TODO: Add better exception handling. Not that it should be possible to reach this if the request went through form validation and the database is seeded
            throw new InvalidArgumentException('No dividend yield data available for year ' . $sinceYear .', try a later date');
        }

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
     * @param array $settings
     * 
     * @return array
     */
    private function simulateFireStrategy(int $startYear, array $settings, array &$stockPriceData, array &$dividendYieldData): array
    {
        $runFailed = false;
        $currentAge = $settings['startAge'];

        $stocksOwned = [];
        $cashOwned = 0; // TODO: Add cash buffer setting

        $totalContributedNominal = $settings['startBalance'];
        $totalContributedInflationAdjusted = $settings['startBalance'];
        $totalWithdrawnNominal = 0;
        $totalWithdrawnInflationAdjusted = 0;

        $startOfYearValue = $settings['startBalance']; // For the netherlands which taxes you based on fictional returns assumed over the total on jan 1st. The Netherlands suck... Tax reform in 2026 though!
        $capitalLossCredit = 0; // For tax systems that use Capital Loss Carryforward. Aka being able to offset today's capital gains with last year's capital loss.

        $inflationIndex = 1; // inflation of 1.0 means no inflation, 0.5 means we deflated by half, 2 means everything is now twice as expensive.

        $yearlyContributions = [$settings['startBalance']];
        $yearlyNetWorth = [$settings['startBalance']];

        $amountOfMonthsToSimulate = ($settings['endAge'] - $settings['startAge']) * 12;

        for ($month = 0; $month < $amountOfMonthsToSimulate; $month++) {
            $currentMonth = sprintf('%d-%d', intdiv($month, 12) + $startYear, $month % 12 + 1);
            // $stockPriceData[$currentMonth];

            if ($runFailed) {
                if ($month % 12 === 0) {
                    array_push($yearlyNetWorth, 0);
                    array_push($yearlyContributions, $yearlyContributions[sizeof($yearlyContributions) - 1]);
                }

                continue;
            }
        }

        return [];
    }
}