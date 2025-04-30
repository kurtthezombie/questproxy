<?php

namespace Database\Seeders;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['title' => 'Dota 2', 'game' => 'dota_2', 'category_type' => 'moba'],
            ['title' => 'League of Legends', 'game' => 'league_of_legends', 'category_type' => 'moba'],
            ['title' => 'Valorant', 'game' => 'valorant', 'category_type' => 'fps'],
            ['title' => 'Diablo IV', 'game' => 'diablo_4', 'category_type' => 'arpg'],
            ['title' => 'Marvel Rivals', 'game' => 'marvel_rivals', 'category_type' => 'shooter'],
            ['title' => 'Roblox', 'game' => 'roblox', 'category_type' => 'sandbox'],
            ['title' => 'Cabal Online', 'game' => 'cabal_online', 'category_type' => 'mmorpg'],
            ['title' => 'Dragon Nest', 'game' => 'dragon_nest', 'category_type' => 'mmorpg'],
            ['title' => 'Genshin Impact', 'game' => 'genshin_impact', 'category_type' => 'rpg'],
            ['title' => 'Crossfire', 'game' => 'crossfire', 'category_type' => 'fps'],
            ['title' => 'MU Online', 'game' => 'mu_online', 'category_type' => 'mmorpg'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
