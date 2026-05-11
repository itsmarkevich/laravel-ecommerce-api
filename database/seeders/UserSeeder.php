<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::query()->firstOrNew(['phone' => '+79990000001']);

        $user->forceFill([
            'name' => 'Demo User',
            'email' => 'user@example.com',
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
            'role' => 'user',
        ])->save();

        $admin = User::query()->firstOrNew(['phone' => '+79990000002']);

        $admin->forceFill([
            'name' => 'Demo Admin',
            'email' => 'admin@example.com',
            'phone_verified_at' => now(),
            'email_verified_at' => now(),
            'role' => 'admin',
        ])->save();
    }
}
