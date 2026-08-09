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
        // Akun demo/test punya kredensial yang diketahui dan tertulis
        // di source code -- jangan pernah dibuat di luar environment
        // local, supaya tidak ada akun dengan password publik kalau
        // seeder ini tidak sengaja dijalankan di staging/production.
        if (! app()->environment('local')) {
            return;
        }

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call(DemoAccountSeeder::class);
    }
}
