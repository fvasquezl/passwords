<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Group;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $groups = [
            [
                'name' => 'IT',
                'description' => 'IT Group Info'
            ],
            [
                'name' => 'Programming',
                'description' => 'Programming Group Info'
            ],
            [
                'name' => 'MI',
                'description' => 'MI Group Info'
            ],
        ];

        foreach ($groups as $category) {
            Group::create([
                'name' => $category['name'],
                'description' => $category['description']
            ]);
        }



        $users = [
            [
                'name' => 'Faustino Vasquez',
                'email' => 'fvasquez@local.com',
            ],
            [
                'name' => 'Sebastian Vasquez',
                'email' => 'svasquez@local.com',
            ],
        ];

        foreach ($users as $user) {
            User::factory()->create([
                'name' => $user['name'],
                'email' => $user['email']
            ]);
        }

        $categories = [
            [
                'name' => 'Personal',
                'description' => 'Personal Info'
            ],
            [
                'name' => 'Social',
                'description' => 'Social Info'
            ],
            [
                'name' => 'Work',
                'description' => 'Work Info'
            ],
            [
                'name' => 'Banking',
                'description' => 'Banking Info'
            ],
            [
                'name' => 'Entertainment',
                'description' => 'Entertainment Info'
            ],
            [
                'name' => 'Other',
                'description' => 'Other Info'
            ]
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'description' => $category['description']
            ]);
        }

    }
}
