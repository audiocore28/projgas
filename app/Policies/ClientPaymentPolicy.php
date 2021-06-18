<?php

namespace App\Policies;

use App\Models\ClientPayment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPaymentPolicy
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
        if ($user->can('view any client payment')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ClientPayment  $clientPayment
     * @return mixed
     */
    public function view(User $user, ClientPayment $clientPayment)
    {
        if ($user->can('view client payment')) {
            return true;
        }
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->can('create client payment')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ClientPayment  $clientPayment
     * @return mixed
     */
    public function update(User $user, ClientPayment $clientPayment)
    {
        if ($user->can('update client payment')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ClientPayment  $clientPayment
     * @return mixed
     */
    public function delete(User $user, ClientPayment $clientPayment)
    {
        if ($user->can('delete client payment')) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ClientPayment  $clientPayment
     * @return mixed
     */
    public function restore(User $user, ClientPayment $clientPayment)
    {
        if ($user->can('restore client payment')) {
            return true;
        }
    }
}
