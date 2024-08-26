<?php

namespace Database\Seeders;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use JsonException;

class StockPriceDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $json = File::json(base_path("resources/data/stockPriceData.json"), JSON_THROW_ON_ERROR);
            $json = $json['data'];
        } catch (FileNotFoundException $exception) {
            $this->command->error('Couldn\'t seed stock price data from json due to FileNotFoundException: ' . $exception->getMessage());
            return;
        } catch (JsonException $exception) {
            $this->command->error('Couldn\'t seed stock price data from json due to JsonException: ' . $exception->getMessage());
            return;
        }

        DB::table('stock_price_data')->delete();
        
        for ($i = 0; $i < sizeof($json); $i++) {
            DB::table('stock_price_data')->insert([
                "year"     => $json[$i]["year"],
                "month"    => $json[$i]["month"],
                "price"    => $json[$i]["price"],
                "dividend" => $json[$i]["dividend"] / 12,
            ]);
        }
    }
}
