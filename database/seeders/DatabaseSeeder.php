<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            PetshopSeeder::class,
            EmployeeSeeder::class,
            ServiceSeeder::class,
            ProductSeeder::class,
            ExtraProductSeeder::class,
            PetSeeder::class,
            CouponSeeder::class,        
            OrderSeeder::class,        
            ReviewSeeder::class,        
            AppointmentSeeder::class,
            BusinessHoursSeeder::class
        ]);
    }
}
