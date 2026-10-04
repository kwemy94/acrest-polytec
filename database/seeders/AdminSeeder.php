<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@acrest-polytechnique.cm')],
            ['name' => env('ADMIN_NAME', 'Administrateur'), 'password' => env('ADMIN_PASSWORD', 'ChangezMoi2026')],
        );
    }
}
