<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Yard Inspector',
                'username' => 'yard',
                'password' => Hash::make('yard123'),
                'role' => 'yard',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Class Surveyor',
                'username' => 'class',
                'password' => Hash::make('class123'),
                'role' => 'class',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Owner Surveyor',
                'username' => 'os',
                'password' => Hash::make('os123'),
                'role' => 'os',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Statutory Inspector',
                'username' => 'stat',
                'password' => Hash::make('stat123'),
                'role' => 'stat',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}