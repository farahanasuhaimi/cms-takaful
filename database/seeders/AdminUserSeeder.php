<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\User::create([
            'name'     => 'Hana',
            'email'    => 'admin@drtakaful.com',
            'password' => bcrypt('takaful2024!'),
        ]);
    }
}
