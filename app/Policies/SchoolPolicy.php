<?php

namespace App\Policies;

use App\Models\{School, User};
use Illuminate\Auth\Access\HandlesAuthorization;

class SchoolPolicy
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
     * @param  \App\Models\School  $school
     * @return mixed
     */
    public function update(User $user, School $school)
    {
        return $user->id === $school->user_id;
        
    }

     /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Models\School  $school
     * @return mixed
     */
    public function delete(User $user, School $school)
    {
        return $user->id === $school->user_id;
    }
}
