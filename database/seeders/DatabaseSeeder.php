<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat admin utama jika belum ada
        User::firstOrCreate(
            ['email' => 'adminpgri@gmail.com'],
            [
                'name' => 'admin',
                'password' => Hash::make('admin'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'anggota' => 1
            ]
        );

        // Buat sample kabupaten users
        $kabupatens = [
            ['nama' => 'Pekanbaru', 'kode' => 'PKU', 'anggota' => 1500, 'tipe' => 'Kota'],
            ['nama' => 'Dumai', 'kode' => 'DUM', 'anggota' => 800, 'tipe' => 'Kota'],
            ['nama' => 'Bengkalis', 'kode' => 'BKL', 'anggota' => 1200, 'tipe' => 'Kabupaten'],
            ['nama' => 'Indragiri Hilir', 'kode' => 'INHIL', 'anggota' => 1000, 'tipe' => 'Kabupaten'],
            ['nama' => 'Indragiri Hulu', 'kode' => 'INHU', 'anggota' => 900, 'tipe' => 'Kabupaten'],
            ['nama' => 'Kampar', 'kode' => 'KMP', 'anggota' => 1300, 'tipe' => 'Kabupaten'],
            ['nama' => 'Kepulauan Meranti', 'kode' => 'KEPMER', 'anggota' => 600, 'tipe' => 'Kabupaten'],
            ['nama' => 'Kuantan Singingi', 'kode' => 'KUANSING', 'anggota' => 850, 'tipe' => 'Kabupaten'],
            ['nama' => 'Pelalawan', 'kode' => 'PLW', 'anggota' => 950, 'tipe' => 'Kabupaten'],
            ['nama' => 'Rokan Hilir', 'kode' => 'ROHIL', 'anggota' => 1100, 'tipe' => 'Kabupaten'],
            ['nama' => 'Rokan Hulu', 'kode' => 'ROHUL', 'anggota' => 1050, 'tipe' => 'Kabupaten'],
            ['nama' => 'Siak', 'kode' => 'SIAK', 'anggota' => 750, 'tipe' => 'Kabupaten'],
        ];

        foreach ($kabupatens as $index => $kab) {
            User::firstOrCreate(
                ['email' => strtolower($kab['kode']) . '@pgri.com'],
                [
                    'name' => 'Admin ' . $kab['nama'],
                    'password' => Hash::make('password123'),
                    'role' => 'kabupaten',
                    'nama_kabupaten' => $kab['nama'],
                    'kode_kabupaten' => $kab['kode'],
                    'anggota' => $kab['anggota'],
                    'jumlah_anggota' => $kab['anggota'],
                    'status' => 'aktif',
                    'email_verified_at' => now(),
                ]
            );
        }

        // Seed transaction data
        $this->call([
            TransactionSeeder::class,
        ]);
    }
}
