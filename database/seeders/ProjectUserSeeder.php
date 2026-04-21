<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectUserSeeder extends Seeder
{
    public function run(): void
    {
        // Assign all non-admin users to the first project
        $project = DB::table('projects')->first();
        if (!$project) return;

        $users = DB::table('users')->where('role', '!=', 'admin')->get();

        foreach ($users as $user) {
            DB::table('project_user')->insert([
                'project_id' => $project->id,
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
