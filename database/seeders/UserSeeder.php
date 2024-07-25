<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Создание обычного пользователя
        User::create([
            'name' => 'Test User',
            'email' => 'ania233237@gmail.com',
            'password' => Hash::make('12345678'),
        ]);
    }
}
