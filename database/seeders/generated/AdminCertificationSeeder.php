<?php

namespace Database\Seeders\generated;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminCertificationSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (! $email || ! $password) {
            $this->command->warn('ADMIN_EMAIL or ADMIN_PASSWORD is not set. Skipping admin certification seeder.');
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
                'certified_via' => 'admin',
                'certified_at' => now(),
                'certified_until' => now()->addYears(100),
            ]
        );
    }
}
