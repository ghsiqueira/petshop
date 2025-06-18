<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Admin
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@exemplo.com',
            'password' => Hash::make('password'),
            'phone' => '(11) 99999-9999',
            'address' => 'Rua do Administrador, 123',
        ]);
        $admin->assignRole('admin');

        // Cliente
        $client = User::create([
            'name' => 'Maria Silva',
            'email' => 'cliente@exemplo.com',
            'password' => Hash::make('password'),
            'phone' => '(11) 98888-8888',
            'address' => 'Avenida das Flores, 456',
        ]);
        $client->assignRole('client');

        // Petshop
        $petshop = User::create([
            'name' => 'João Petshop',
            'email' => 'petshop@exemplo.com',
            'password' => Hash::make('password'),
            'phone' => '(11) 97777-7777',
            'address' => 'Rua dos Animais, 789',
        ]);
        $petshop->assignRole('petshop');

        // Funcionário
        $employee = User::create([
            'name' => 'Carlos Funcionário',
            'email' => 'funcionario@exemplo.com',
            'password' => Hash::make('password'),
            'phone' => '(11) 96666-6666',
            'address' => 'Rua dos Trabalhadores, 321',
        ]);
        $employee->assignRole('employee');

        // Cliente adicional
        $client2 = User::create([
            'name' => 'Ana Oliveira',
            'email' => 'cliente2@exemplo.com',
            'password' => Hash::make('password'),
            'phone' => '(11) 95555-5555',
            'address' => 'Rua das Árvores, 654',
        ]);
        $client2->assignRole('client');
    }
}