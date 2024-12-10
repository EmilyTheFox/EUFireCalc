<?php

namespace Database\Seeders;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use JsonException;

class InflationDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $json = File::json(base_path("resources/data/inflationData.json"), JSON_THROW_ON_ERROR);
            $json = $json['data'];
        } catch (FileNotFoundException $exception) {
            $this->command->error('Couldn\'t seed inflation data from json due to FileNotFoundException: ' . $exception->getMessage());
            return;
        } catch (JsonException $exception) {
            $this->command->error('Couldn\'t seed inflation data from json due to JsonException: ' . $exception->getMessage());
            return;
        }

        DB::table('inflation_data')->delete();
        
        for ($i = 0; $i < sizeof($json); $i++) {
            DB::table('inflation_data')->insert([
                "year"      => $json[$i]["year"],
                "inflation" => $json[$i]["inflation"],
            ]);
        }
    }
}
