<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Crear el usuario Administrador
        User::create([
            'name' => 'Administrador',
            'username' => 'admin', 
            'password' => Hash::make('Info.2024*'),
            'last_name' => 'Admin',
            'role_id' => 1,
            'is_active' => true,
            'phone' => '1234567890',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        User::create([
            'name' => 'Jhon',
            'last_name' => 'Doe',
            'username' => 'JhonD',
            'password' => Hash::make('Zona2024*'),
            'role_id' => 2,
            'is_active' => true,
            'phone' => '0987654321',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
