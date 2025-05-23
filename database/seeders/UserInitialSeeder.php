<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SantriProfile;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserInitialSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@tpq.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN,
            'email_verified_at' => now(),
        ]);

        // Akademik
        User::create([
            'name' => 'Staf Akademik',
            'email' => 'akademik@tpq.com',
            'password' => Hash::make('password'),
            'role' => UserRole::AKADEMIK,
            'email_verified_at' => now(),
        ]);

        // Pengajar
        User::create([
            'name' => 'Ustadz Bahar',
            'email' => 'pengajar1@tpq.com',
            'password' => Hash::make('password'),
            'role' => UserRole::PENGAJAR,
            'email_verified_at' => now(),
        ]);

        // Pengajar 2
        User::create([
            'name' => 'Ustadz Walid',
            'email' => 'pengajar2@tpq.com',
            'password' => Hash::make('password'),
            'role' => UserRole::PENGAJAR,
            'email_verified_at' => now(),
        ]);

        // Santri 1
        $santri1 = User::create([
            'name' => 'Santri Cerdas',
            'email' => 'santri1@tpq.com',
            'password' => Hash::make('password'),
            'role' => UserRole::SANTRI,
            'email_verified_at' => now(),
        ]);

        SantriProfile::create([
            'user_id' => $santri1->id,
            'alamat' => 'Jl. Belajar No. 1, Desa Ilmu',
            'tanggal_lahir' => '2015-07-20',
            'nama_wali' => 'Bapak Hidayat',
        ]);

        // Santri 2
        $santri2 = User::create([
            'name' => 'Santri Rajin',
            'email' => 'santri2@tpq.com',
            'password' => Hash::make('password'),
            'role' => UserRole::SANTRI,
            'email_verified_at' => now(),
        ]);

        SantriProfile::create([
            'user_id' => $santri2->id,
            'alamat' => 'Jl. Mengaji No. 10, Kampung Sholih',
            'tanggal_lahir' => '2016-02-10',
            'nama_wali' => 'Ibu Aminah',
        ]);
    }
}