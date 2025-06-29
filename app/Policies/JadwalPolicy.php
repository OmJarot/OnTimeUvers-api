<?php

namespace App\Policies;

use App\Models\Jadwal;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JadwalPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->level == "dba";
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Jadwal $jadwal): bool
    {
        return $user->level == "admin" || $user->level == "dba" || $user->id == $jadwal->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->level == "dba";
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Jadwal $jadwal): bool
    {
        return $user->level == "dba";
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Jadwal $jadwal): bool
    {
        return $user->level == "dba";
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Jadwal $jadwal): bool
    {
        return $user->level == "dba";
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Jadwal $jadwal): bool
    {
        return $user->level == "dba";
    }
}
