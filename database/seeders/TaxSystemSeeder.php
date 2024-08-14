<?php

namespace Database\Seeders;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use JsonException;

class TaxSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $json = File::json(base_path("resources/data/taxSystems.json"), JSON_THROW_ON_ERROR);
            $json = $json['countries'];
        } catch (FileNotFoundException $exception) {
            $this->command->error('Couldn\'t seed tax systems from json due to FileNotFoundException: ' . $exception->getMessage());
            return;
        } catch (JsonException $exception) {
            $this->command->error('Couldn\'t seed tax systems from json due to JsonException: ' . $exception->getMessage());
            return;
        }

        DB::table('tax_systems')->delete();
        
        for ($i = 0; $i < sizeof($json); $i++) {
            DB::table('tax_systems')->insert([
                "name"=> $json[$i]["name"],
                "capital_gains"=> $json[$i]["capitalGains"],
                "wealth_tax"=> $json[$i]["wealthTax"],
                "special_rules"=> $json[$i]["specialRules"],
                "created_at" => time()
            ]);
        }
    }
}
