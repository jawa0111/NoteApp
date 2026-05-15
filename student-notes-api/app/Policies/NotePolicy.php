<?php

namespace App\Policies;

use App\Models\Note;
use App\Models\User;

class NotePolicy
{
    /**
     * Determine whether the user can view any notes.
     */
    public function viewAny($user = null): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the note.
     */
    public function view($user = null, Note $note): bool
    {
        // if notes are owned by users, enforce ownership; otherwise allow
        if ($user === null) {
            return true;
        }

        if (isset($note->user_id)) {
            return $user->id === $note->user_id;
        }

        return true;
    }

    /**
     * Determine whether the user can create notes.
     */
    public function create($user = null): bool
    {
        return true;
    }

    /**
     * Determine whether the user can archive the note.
     */
    public function archive($user = null, Note $note): bool
    {
        if ($user === null) {
            // allow anonymous archive in this basic policy; change to false to require auth
            return true;
        }

        if (isset($note->user_id)) {
            return $user->id === $note->user_id;
        }

        return true;
    }
}
