<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Dmitrii Derenko',
            'email' => 'dima.16.artemov@gmail.com',
            'password' => '$2y$10$rgXOQSii2/83/uOcA1/VduF1Js5h.wusrsjXWwwVwAxpQcopvIMVy', // ya_adm1n
        ]);

        Currency::factory()->create([
            'code' => 'USD',
        ]);

        Currency::factory()->create([
            'code' => 'RUB',
        ]);

        Currency::factory()->create([
            'code' => 'TRY',
        ]);
    }
}
