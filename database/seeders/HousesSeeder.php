<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HousesSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('database/data/houses.csv');
        if (!is_file($path)) {
            return;
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            return;
        }

        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            return;
        }

        $header = array_map('strtolower', $header);
        $batch = [];
        $now = now();

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row);
            if ($data === false) {
                continue;
            }

            $batch[] = [
                'name' => $data['name'],
                'price' => (int) $data['price'],
                'bedrooms' => (int) $data['bedrooms'],
                'bathrooms' => (int) $data['bathrooms'],
                'storeys' => (int) $data['storeys'],
                'garages' => (int) $data['garages'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($batch) >= 500) {
                DB::table('houses')->insert($batch);
                $batch = [];
            }
        }

        fclose($handle);

        if ($batch) {
            DB::table('houses')->insert($batch);
        }
    }
}
