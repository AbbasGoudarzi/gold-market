<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Ahmad',
            'email' => 'ahmad@taline.ir',
        ]);

        User::factory()->create([
            'name' => 'Reza',
            'email' => 'reza@taline.ir',
        ]);

        User::factory()->create([
            'name' => 'Akbar',
            'email' => 'akbar@taline.ir',
        ]);
    }
}
