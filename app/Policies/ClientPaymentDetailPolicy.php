<?php

namespace App\Policies;

use App\Models\ClientPaymentDetail;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPaymentDetailPolicy
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
        if ($user->can('view any client payment detail')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ClientPaymentDetail  $clientPaymentDetail
     * @return mixed
     */
    public function view(User $user, ClientPaymentDetail $clientPaymentDetail)
    {
        if ($user->can('view client payment detail')) {
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
        if ($user->can('create client payment detail')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ClientPaymentDetail  $clientPaymentDetail
     * @return mixed
     */
    public function update(User $user, ClientPaymentDetail $clientPaymentDetail)
    {
        if ($user->can('update client payment detail')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ClientPaymentDetail  $clientPaymentDetail
     * @return mixed
     */
    public function delete(User $user, ClientPaymentDetail $clientPaymentDetail)
    {
        if ($user->can('delete client payment detail')) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ClientPaymentDetail  $clientPaymentDetail
     * @return mixed
     */
    public function restore(User $user, ClientPaymentDetail $clientPaymentDetail)
    {
        if ($user->can('restore client payment detail')) {
            return true;
        }
    }
}
