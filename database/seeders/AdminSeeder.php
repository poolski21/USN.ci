<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (! $email || ! $password) {
            $this->command->warn('ADMIN_EMAIL or ADMIN_PASSWORD is not set in .env. Skipping admin seeder.');
            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Poolski',
                'prenom' => 'Poolski',
                'nom' => 'Poolski',
                'matricule' => 'ADMIN-0001',
                'universite' => 'USN',
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
                'email_verified_at' => now(),
                'is_certified' => true,
                'certification_status' => 'approved',
                'certification_package' => 'premium',
                'subscription_plan' => 'premium',
                'visibility_boost' => 10,
                'certified_via' => 'admin',
                'certified_at' => now(),
                // MySQL TIMESTAMP range ends in 2038, so keep the expiration within that limit.
                'certified_until' => now()->addYears(10),
            ]
        );
    }
}
