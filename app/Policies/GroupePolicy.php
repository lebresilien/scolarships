<?php

namespace App\Policies;

use App\Models\{Groupe, User};
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Groupe  $groupe
     * @return mixed
     */
    public function update(User $user, Groupe $groupe)
    {
        return $user->id === $groupe->school->user_id;
        
    }

     /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Groupe  $groupe
     * @return mixed
     */
    public function delete(User $user, Groupe $groupe)
    {
        return $user->id === $groupe->school->user_id;
    }
}
