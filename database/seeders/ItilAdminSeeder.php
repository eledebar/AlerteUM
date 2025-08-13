<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ItilAdminSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name'=>'Admin','password'=>bcrypt('password'),'role'=>'admin']
        );
    }
}
