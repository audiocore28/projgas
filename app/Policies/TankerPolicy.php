<?php

namespace App\Policies;

use App\Models\Tanker;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TankerPolicy
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
        if ($user->can('view any tanker')) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Tanker  $tanker
     * @return mixed
     */
    public function view(User $user, Tanker $tanker)
    {
        if ($user->can('view tanker')) {
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
        if ($user->can('create tanker')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Tanker  $tanker
     * @return mixed
     */
    public function update(User $user, Tanker $tanker)
    {
        if ($user->can('update tanker')) {
            return true;
        }
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Tanker  $tanker
     * @return mixed
     */
    public function delete(User $user, Tanker $tanker)
    {
        if ($user->can('delete tanker')) {
            return true;
        }
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Tanker  $tanker
     * @return mixed
     */
    public function restore(User $user, Tanker $tanker)
    {
        if ($user->can('restore tanker')) {
            return true;
        }
    }

}
