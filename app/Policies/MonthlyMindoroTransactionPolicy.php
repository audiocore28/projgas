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
        if ($user->can('view any mindoro transaction')) {
            return true;
        }
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
        if ($user->can('view mindoro transaction')) {
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
        if ($user->can('create mindoro transaction')) {
            return true;
        }
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
        if ($user->can('update mindoro transaction')) {
            return true;
        }
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
        if ($user->can('delete mindoro transaction')) {
            return true;
        }
    }

    public function print(User $user, MonthlyMindoroTransaction $monthlyMindoroTransaction)
    {
        if ($user->can('print mindoro transaction')) {
            return true;
        }
    }

}