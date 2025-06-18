<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\User;
use App\Models\Petshop;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employeeUser = User::whereHas('roles', function($query) {
            $query->where('name', 'employee');
        })->first();

        $petshop = Petshop::first();

        Employee::create([
            'user_id' => $employeeUser->id,
            'petshop_id' => $petshop->id,
            'position' => 'groomer', // tosador
            'bio' => 'Tosador profissional com 5 anos de experiência.'
        ]);
    }
}