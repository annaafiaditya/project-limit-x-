<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'technician@example.com'],
            [
                'name' => 'QA Lab Technician',
                'password' => Hash::make('password'),
                'role' => 'technician',
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name' => 'QA Staff',
                'password' => Hash::make('password'),
                'role' => 'staff',
            ]
        );

        User::updateOrCreate(
            ['email' => 'supervisor@example.com'],
            [
                'name' => 'QA Supervisor',
                'password' => Hash::make('password'),
                'role' => 'supervisor',
            ]
        );
    }
}
