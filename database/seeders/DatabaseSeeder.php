<?php

namespace Database\Seeders;

use App\Models\Repository;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Repository::factory(50)->create();
    }
}
