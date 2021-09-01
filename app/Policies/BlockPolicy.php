<?php

namespace App\Policies;

use App\Models\{Block, User};
use Illuminate\Auth\Access\HandlesAuthorization;

class BlockPolicy
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
     * @param  \App\Models\block  $block
     * @return mixed
     */
    public function update(User $user, Block $block)
    {
        return $user->id === $block->school->user_id;
        
    }

     /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Models\Block  $block
     * @return mixed
     */
    public function delete(User $user, Block $block)
    {
        return $user->id === $block->school->user_id;
    }
}
