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
        return $user->can('view any batangas transaction');
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
        return $user->can('view batangas transaction', $monthlyBatangasTransaction);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('create batangas transaction');
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
        return $user->can('update batangas transaction', $monthlyBatangasTransaction);
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
        return $user->can('delete batangas transaction', $monthlyBatangasTransaction);
    }

    public function print(User $user, MonthlyBatangasTransaction $monthlyBatangasTransaction)
    {
        return $user->can('print batangas transaction', $monthlyBatangasTransaction);
    }

}
