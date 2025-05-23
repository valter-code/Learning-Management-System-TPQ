<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // Pastikan User di-import
use App\Enums\UserRole; // Import UserRole jika digunakan

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // MUNGKIN ADA BARIS SEPERTI INI YANG MENYEBABKAN ERROR:
        // \App\Models\User::factory(10)->create(); // Jika ada, factory harus diupdate

        // ATAU LEBIH SPESIFIK SEPERTI INI:
        User::factory()->create([ // Ganti \App\Models\User::factory() dengan User::factory() jika sudah di-import
            'name' => 'Test User',
            'email' => 'test@example.com',
            // 'role' => UserRole::SANTRI, // TAMBAHKAN ROLE DI SINI!
        ]);

        $this->call([
            UserInitialSeeder::class, // Seeder kita yang sudah benar
            // Seeder lain jika ada
        ]);
    }
}