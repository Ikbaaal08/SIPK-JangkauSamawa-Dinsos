<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'ikbalmindarsah22@gmail.com'],
            [
                'name' => 'Administrator Dinsos',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '6285338908773',
            ]
        );

        User::updateOrCreate(
            ['email' => 'penerima@jangkausamawa.id'],
            [
                'name' => 'Petugas Penerima Aduan',
                'password' => Hash::make('password'),
                'role' => 'petugas_penerima',
                'phone' => '6285338908773',
            ]
        );

        User::updateOrCreate(
            ['email' => 'kadissosial@gmail.com'],
            [
                'name' => 'Kepala Dinas Sosial',
                'password' => Hash::make('password'),
                'role' => 'kepala_dinas',
                'phone' => '6281234567892',
            ]
        );

        User::updateOrCreate(
            ['email' => 'pelaksana@jangkausamawa.id'],
            [
                'name' => 'Petugas Pelaksana',
                'password' => Hash::make('password'),
                'role' => 'petugas_pelaksana',
                'phone' => '6281234567893',
            ]
        );
    }
}
