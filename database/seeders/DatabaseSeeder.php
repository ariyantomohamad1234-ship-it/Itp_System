<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ProjectSeeder::class,
            ProjectUserSeeder::class,
            ModulSeeder::class,
            BlokSeeder::class,
            SubBlokSeeder::class,
            ItpSeeder::class,
            MiniLngTemplateSeeder::class,
        ]);
    }
}
