<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::insert([
            ['title'=> 'Dota 2','game' => 'dota_2','category_type' => 'moba'],
            ['title'=> 'League of Legends','game' => 'league_of_legends','category_type' => 'moba'],
            ['title'=> 'Valorant','game' => 'valorant','category_type' => 'fps'],
            ['title'=> 'Diablo IV','game' => 'diablo_4','category_type' => 'arpg'],
            ['title'=> 'Torchlight Infinite','game' => 'torchlight_infinite','category_type' => 'arpg'],
            ['title'=> 'Roblox','game' => 'roblox','category_type' => 'sandbox'],
        ]);
    }
}
