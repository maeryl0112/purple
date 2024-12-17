<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Enums\UserRolesEnum;
use App\Models\Admin; 
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $userroles = [
            [
                'id' => UserRolesEnum::Customer,
                'name' => 'Customer',
                'status' => true,
            ],
            [
                'id' => UserRolesEnum::Employee,
                'name' => 'Employee',
                'status' => true,
            ],
            [
                'id' => UserRolesEnum::Admin,
                'name' => 'Admin',
                'status' => true,
            ]

        ];

        foreach ($userroles as $role) {
            \App\Models\Role::create($role);
        }

        // Create admin user
        \App\Models\User::create([
            'name' => 'Admin',
            'email' => 'admin@purplelook.com',
            'email_verified_at' => now(),
            'password' => Hash::make('Purplelook@2019'),
            'phone_number' => '12345699901',
            'role_id' => UserRolesEnum::Admin,
        ]);

        // create mock customers


        // create mock employees
        \App\Models\User::create([
            'name' => 'Employee 1',
            'email' => 'emp1@purplelook.com',
            'email_verified_at' => now(),
            'password' => Hash::make('emppassword'),
            'phone_number' => '16445678901',
            'role_id' => UserRolesEnum::Employee,
        ]);

   

        // this Employee is suspeneded
        \App\Models\User::create([
            'name' => 'Employee 3',
            'email' => 'emp3@purplelook.com',
            'email_verified_at' => now(),
            'password' => Hash::make('emppassword'),
            'phone_number' => '00345678901',
            'role_id' => UserRolesEnum::Employee,
            'status' => '0',
        ]);

        // Deals
        \App\Models\Deal::create([
            'name' => 'Deal 1',
            'description' => 'Deal 1 description',
            'start_date' => '2023-07-16',
            'end_date' => '2023-07-20',
            'discount' => '10',
            'is_hidden' => '0',
        ]);

        // categories Skin, Makeup, Nails, Hair
        \App\Models\Category::create([
            'name' => 'Skin',
        ]);

        \App\Models\Category::create([
            'name' => 'Makeup',
        ]);

        \App\Models\Category::create([
            'name' => 'Hair',
        ]);

        \App\Models\Category::create([
            'name' => 'Nails',
        ]);

        $this->call([
            ServicesSeeder::class,
            TimeSlotSeeder::class,
        ]);

        Admin::factory()->create();
    }
}
