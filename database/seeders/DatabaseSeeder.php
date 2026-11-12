<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Every demo account uses the password "password".
     */
    public function run(): void
    {
        User::factory()->student()->create([
            'fullname' => 'دانشجوی نمونه',
            'identification_code' => '40000001',
            'email' => 'student@example.com',
        ]);

        User::factory()->professor()->create([
            'fullname' => 'استاد نمونه',
            'identification_code' => '10000001',
            'email' => 'professor@example.com',
        ]);

        User::factory()->employee()->create([
            'fullname' => 'کارمند نمونه',
            'identification_code' => '20000001',
            'email' => 'employee@example.com',
        ]);
    }
}
