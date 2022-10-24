<?php

namespace App\Services\Users;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

class UserService
{
    /**
     * @param array $userData
     *
     * @return User|Builder|Model
     */
    public function createUser(array $userData): User|Builder|Model
    {
        return User::query()->create([
            'username' => $userData['username'],
            'role' => $userData['role'],
            'deposit' => 0,
            'password' => bcrypt('password'),
        ]);
    }

    /**
     * @param User $user
     * @param array $userData
     *
     * @return User
     */
    public function updateUser(User $user, array $userData): User
    {
        if (array_key_exists('password', $userData)) {
            $user->fill([
                'password' => bcrypt($userData['password']),
            ]);

            unset($userData['password'], $userData['password_confirmation']);
        }

        $user = $user->fill($userData);
        $user->save();

        return $user;
    }

    /**
     * @param User $user
     * @param int $amount
     *
     * @return User
     */
    public function deposit(User $user, int $amount): User
    {
        $user->newQuery()->increment('deposit', $amount);

        return $user->refresh();
    }

    /**
     * @param User $user
     *
     * @return User
     */
    public function resetDeposit(User $user): User
    {
        $user = $user->fill([ 'deposit' => 0 ]);
        $user->save();

        return $user;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function deleteUser(User $user): bool
    {
        try {
            DB::transaction(function () use ($user) {
                $user->purchases()->delete();
                $user->products()->delete();
                $user->delete();
            });
        } catch (Throwable $ex) {
            app('log')->error($ex->getMessage(), $ex->getTrace());

            return false;
        }

        return true;
    }
}
