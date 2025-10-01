<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ensure required roles exist
        $principalRole = Role::firstOrCreate(
            ['slug' => 'principal'],
            ['name' => 'Principal']
        );
        $teacherRole = Role::firstOrCreate(
            ['slug' => 'teacher'],
            ['name' => 'Teacher']
        );
        $parentRole = Role::firstOrCreate(
            ['slug' => 'parent'],
            ['name' => 'Parent']
        );

        // Create principal user (idempotent)
        User::firstOrCreate(
            ['email' => 'principal@sudlon.edu.ph'],
            [
                'name' => 'Principal',
                'password' => Hash::make('password'),
                'role_id' => $principalRole->id,
            ]
        );

        // Create teachers teacher1..teacher3
        $teacherRoleId = $teacherRole->id;
        if ($teacherRoleId) {
            for ($i = 1; $i <= 3; $i++) {
                User::firstOrCreate(
                    ['email' => "teacher{$i}@gmail.com"],
                    [
                        'name' => "Teacher {$i}",
                        'password' => Hash::make('password'),
                        'role_id' => $teacherRoleId,
                    ]
                );
            }
        }

        // Seed student management data
        // $this->call(StudentManagementSeeder::class);
    }
}
