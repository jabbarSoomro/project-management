<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $specificUsers = [
            [
                'name' => 'User one',
                'email' => 'user1@example.com',
                'password' => 'password',
            ],
            [
                'name' => 'User two',
                'email' => 'user2@example.com',
                'password' => 'password',
            ],
            [
                'name' => 'user3',
                'email' => 'user3@example.com',
                'password' => 'password',
            ],
        ];

        foreach ($specificUsers as $userData) {
            User::factory()->create($userData);
        }

        // Create 7 more random Users to reach 10 total
        User::factory(7)->create();

        // Assign Projects and Tasks to ALL users
        User::all()->each(function ($user) {
            // Create 1-3 Projects for each User
            $projects = Project::factory(rand(1, 3))->create(['user_id' => $user->id]);

            foreach ($projects as $project) {
                // Create 3-5 Tasks for each Project, assigned to the user
                Task::factory(rand(3, 5))->create([
                    'project_id' => $project->id,
                    'assigned_user_id' => $user->id,
                ]);
            }
        });
    }
}
