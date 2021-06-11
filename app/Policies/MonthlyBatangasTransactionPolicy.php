<?php

namespace App\Policies;

use App\Models\MonthlyBatangasTransaction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MonthlyBatangasTransactionPolicy
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
        if ($user->can('view any batangas transaction')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MonthlyBatangasTransaction  $monthlyBatangasTransaction
     * @return mixed
     */
    public function view(User $user, MonthlyBatangasTransaction $monthlyBatangasTransaction)
    {
        if ($user->can('view batangas transaction')) {
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
        if ($user->can('create batangas transaction')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MonthlyBatangasTransaction  $monthlyBatangasTransaction
     * @return mixed
     */
    public function update(User $user, MonthlyBatangasTransaction $monthlyBatangasTransaction)
    {
        if ($user->can('update batangas transaction')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MonthlyBatangasTransaction  $monthlyBatangasTransaction
     * @return mixed
     */
    public function delete(User $user, MonthlyBatangasTransaction $monthlyBatangasTransaction)
    {
        if ($user->can('delete batangas transaction')) {
            return true;
        }
    }

    public function print(User $user, Purchase $purchase)
    {
        if ($user->can('print batangas transaction')) {
            return true;
        }
    }

}
