<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'username' => 'buyer1578',
                'password' => bcrypt('password'),
                'role' => User::ROLE_BUYER,
            ],

            [
                'username' => 'buyer2578',
                'password' => bcrypt('password'),
                'role' => User::ROLE_BUYER,
            ],
        ];

        foreach ($users as $userArr) {
            User::query()->firstOrCreate([
                'username' => $userArr['username'],
            ], [
                'role' => $userArr['role'],
                'password' => $userArr['password'],
            ]);
        }
    }
}
