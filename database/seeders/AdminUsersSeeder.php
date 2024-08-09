<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = [
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'role' => User::USER_ROLE_ADMIN,
                'email' => 'admin@test.com',
                'phone_number' => '0754125325',
                'date_of_birth' => '2000-04-18',
                'address' => 'Kalubowila',
                'gender' => 'male',
                'password' => Hash::make('12345678'),
            ],

            [
                'first_name' => 'Admin',
                'last_name' => 'User3',
                'role' => User::USER_ROLE_ADMIN,
                'email' => 'admin3@test.com',
                'phone_number' => '0754125325',
                'date_of_birth' => '2000-04-18',
                'address' => 'Kalubowila',
                'gender' => 'male',
                'password' => Hash::make('12345678'),
            ]

        ];

        DB::table('users')->insert($admin);
    }
}
