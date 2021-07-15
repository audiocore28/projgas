<?php

namespace App\Policies;

use App\Models\MonthlyMindoroTransaction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MonthlyMindoroTransactionPolicy
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
        return $user->can('view any mindoro transaction');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MonthlyMindoroTransaction  $monthlyMindoroTransaction
     * @return mixed
     */
    public function view(User $user, MonthlyMindoroTransaction $monthlyMindoroTransaction)
    {
        return $user->can('view mindoro transaction', $monthlyMindoroTransaction);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('create mindoro transaction');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MonthlyMindoroTransaction  $monthlyMindoroTransaction
     * @return mixed
     */
    public function update(User $user, MonthlyMindoroTransaction $monthlyMindoroTransaction)
    {
        return $user->can('update mindoro transaction', $monthlyMindoroTransaction);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MonthlyMindoroTransaction  $monthlyMindoroTransaction
     * @return mixed
     */
    public function delete(User $user, MonthlyMindoroTransaction $monthlyMindoroTransaction)
    {
        return $user->can('delete mindoro transaction', $monthlyMindoroTransaction);
    }

    public function print(User $user, MonthlyMindoroTransaction $monthlyMindoroTransaction)
    {
        return $user->can('print mindoro transaction', $monthlyMindoroTransaction);
    }

}