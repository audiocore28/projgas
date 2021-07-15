<?php

namespace App\Policies;

use App\Models\Depot;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepotPolicy
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
        return $user->can('view any depot');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Depot  $depot
     * @return mixed
     */
    public function view(User $user, Depot $depot)
    {
        return $user->can('view depot', $depot);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('create depot');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Depot  $depot
     * @return mixed
     */
    public function update(User $user, Depot $depot)
    {
        return $user->can('update depot', $depot);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Depot  $depot
     * @return mixed
     */
    public function delete(User $user, Depot $depot)
    {
        return $user->can('delete depot', $depot);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Depot  $depot
     * @return mixed
     */
    public function restore(User $user, Depot $depot)
    {
        return $user->can('restore depot', $depot);
    }

}
