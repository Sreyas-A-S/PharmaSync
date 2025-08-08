<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'role' => 'admin',
            'password' => Hash::make('password'),
            'department_id' => null,
        ]);

        User::create([
            'name' => 'Sreyas',
            'email' => 'sreyasas25@gmail.com',
            'role' => 'staff',
            'password' => Hash::make('password'),
            'department_id' => 1,
        ]);

        User::create([
            'name' => 'Employee',
            'email' => 'employee@gmail.com',
            'role' => 'staff',
            'password' => Hash::make('password'),
            'department_id' => 1,
        ]);

        User::create([
            'name' => 'Head',
            'email' => 'head@gmail.com',
            'role' => 'head',
            'password' => Hash::make('password'),
            'department_id' => 1,
        ]);
    }
}
