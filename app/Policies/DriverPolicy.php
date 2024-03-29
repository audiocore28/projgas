<?php

namespace App\Policies;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DriverPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->can('view any driver');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Driver  $driver
     * @return mixed
     */
    public function view(User $user, Driver $driver)
    {
        return $user->can('view driver', $driver);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('create driver');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Driver  $driver
     * @return mixed
     */
    public function update(User $user, Driver $driver)
    {
        return $user->can('update driver', $driver);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Driver  $driver
     * @return mixed
     */
    public function delete(User $user, Driver $driver)
    {
        return $user->can('delete driver', $driver);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Driver  $driver
     * @return mixed
     */
    public function restore(User $user, Driver $driver)
    {
        return $user->can('restore driver', $driver);
    }

}
