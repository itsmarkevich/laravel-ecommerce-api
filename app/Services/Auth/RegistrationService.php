<?php

namespace App\Services\Auth;

use App\Models\User;

class RegistrationService
{
    public function findOrCreateByPhone(string $phone): User
    {
        return User::query()->firstOrCreate(
            ['phone' => $phone],
            [
                'phone_verified_at' => null,
                'name' => null,
                'email' => null,
            ]);
    }

    public function completeProfile(User $user, string $name, string $email): User
    {
        $user->update(compact('name', 'email'));
        return $user;
    }

    public function markPhoneVerified(User $user): void
    {
        if (!$user->phone_verified_at) {
            $user->phone_verified_at = now();
            $user->save();
        }
    }
}
