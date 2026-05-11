<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@unisba.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'nip' => 'ADMIN001',
            'position' => 'admin'
        ]);
        
        // Petugas
        User::create([
            'name' => 'Petugas Surat',
            'email' => 'petugas@unisba.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'petugas',
            'nip' => 'PTG001',
            'position' => 'petugas'
        ]);
        
        // Mahasiswa contoh
        User::create([
            'name' => 'Mahasiswa Test',
            'email' => 'mahasiswa@unisba.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
            'nip' => null,
        ]);
    }
}