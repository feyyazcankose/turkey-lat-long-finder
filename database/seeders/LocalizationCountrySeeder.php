<?php


namespace Database\Seeders;

use App\Models\LocalizationCountry;
use Illuminate\Database\Seeder;


class LocalizationCountrySeeder extends Seeder
{
    public function run()
    {
        foreach ($this->items() as $item) {
            $user = LocalizationCountry::where("id", $item["id"])->first();

            if (!$user) {
                LocalizationCountry::create([
                    'id' => $item["id"],
                    'binary_code' => $item["binary_code"],
                    'triple_code' => $item["triple_code"],
                    'country_name' => $item["country_name"],
                    'phone_code' => $item["phone_code"],
                    'status' => $item["status"],
                ]);
            }
        }
    }

    private function items()
    {
        return [
            [
                "id" => 1,
                "binary_code" => 'TR',
                "triple_code" => 'TUR',
                "country_name" => 'Türkiye',
                "phone_code" => '90',
                "status" => 1,
            ]
        ];
    }
}
