<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'athletegum@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'), // Default password - should be changed
                'email_verified_at' => now(),
                'is_superadmin' => true,
            ]
        );

        // Ensure the user is a super admin even if they already existed
        if (!$user->is_superadmin) {
            $user->is_superadmin = true;
            $user->save();
        }

        $this->command->info('Super admin created: ' . $user->email);
    }
}
