<?php

namespace App\Services\Registration;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UserRegistrationService
{
    /**
     * @param array $userData
     *
     * @return User|Builder|Model
     */
    public function register(array $userData): User|Builder|Model
    {
        return User::query()->create([
            'username' => $userData['username'],
            'role' => $userData['role'],
            'deposit' => 0,
            'password' => bcrypt('password'),
        ]);
    }
}
