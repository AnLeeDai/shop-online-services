<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Hash;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['role_name' => 'admin']);

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'email' => 'admin@mail.com',
                'phone' => '0334920373',
                'avatar' => 'https://picsum.photos/seed/picsum/200/300',
                'password' => Hash::make('Admin@123'),
                'role_id' => $adminRole->id,
            ]
        );
    }
}
