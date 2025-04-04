<?php

namespace Database\Seeders;

use App\Models\TransactionHistory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TransactionHistory::factory()->count(5)->create();

        echo "Seeded 5 transactions for user ID: 1\n";
    }
}
