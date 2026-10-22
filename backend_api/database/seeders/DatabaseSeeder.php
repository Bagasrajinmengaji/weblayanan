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
     */
    public function run(): void
    {
        // Admin Account
        User::updateOrCreate(
            ['email' => 'admin@umpku.ac.id'],
            [
                'name' => 'Administrator BAAK',
                'role' => 'admin',
                'password' => bcrypt('password123'),
                'avatar' => 'https://api.dicebear.com/7.x/bottts/svg?seed=admin',
            ]
        );

        // Demo Mahasiswa Account
        User::updateOrCreate(
            ['email' => 'mahasiswa@umpku.ac.id'],
            [
                'name' => 'Bagas Pramono',
                'role' => 'mahasiswa',
                'nim' => '1202407009',
                'password' => bcrypt('password123'),
                'avatar' => 'https://github.com/shadcn.png',
            ]
        );

        $this->call(LayananSeeder::class);
    }
}
