<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class GuestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Guest User',
            'email' => 'guest@futami.com',
            'password' => Hash::make('guest123'),
            'role' => 'guest',
            'note' => 'Akun guest untuk melihat dan export data saja'
        ]);
    }
}
