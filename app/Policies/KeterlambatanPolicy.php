<?php

namespace App\Policies;

use App\Models\Keterlambatan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class KeterlambatanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->level == "admin" || $user->level == "security" || $user->level == "user";
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Keterlambatan $keterlambatan): bool
    {
        return $user->level == "admin" || $user->level == "security" || $user->id == $keterlambatan->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->level == "security" || $user->level == "user";
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Keterlambatan $keterlambatan): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Keterlambatan $keterlambatan): bool
    {
        return $user->level == "admin";
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Keterlambatan $keterlambatan): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Keterlambatan $keterlambatan): bool
    {
        return false;
    }
}
