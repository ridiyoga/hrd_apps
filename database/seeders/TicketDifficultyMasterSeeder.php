<?php

namespace Database\Seeders;

use App\Models\TicketDifficultyMaster;
use Illuminate\Database\Seeder;

class TicketDifficultyMasterSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['code' => 'P01', 'name' => 'Critical Risk', 'expected_hours' => 24.00],
            ['code' => 'P02', 'name' => 'High', 'expected_hours' => 12.00],
            ['code' => 'P03', 'name' => 'Medium', 'expected_hours' => 6.00],
            ['code' => 'P04', 'name' => 'Low', 'expected_hours' => 1.00],
        ];

        foreach ($defaults as $item) {
            TicketDifficultyMaster::updateOrCreate(
                ['code' => $item['code']],
                ['name' => $item['name'], 'expected_hours' => $item['expected_hours']]
            );
        }
    }
}
