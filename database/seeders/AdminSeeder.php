<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'annakant37@gmail.com',
            'password' => Hash::make('Admin'), 
            'is_admin' => true,
        ]);
    }
}
