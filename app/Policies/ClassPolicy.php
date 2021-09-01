<?php

namespace App\Policies;

use App\Models\{User, Classe};
use Illuminate\Auth\Access\HandlesAuthorization;

class ClassPolicy
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
     * @param  \App\Models\Classe  $classe
     * @return mixed
     */
    public function update(User $user, Classe $classe)
    {
        return $user->id === $classe->block->school->user_id;
        
    }

     /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Classe  $groupe
     * @return mixed
     */
    public function delete(User $user, Classe $classe)
    {
        return $user->id === $classe->block->school->user_id;
    }
}
