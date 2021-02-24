<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EntryPolicy
{
    use HandlesAuthorization;

    public function create(User $user, $request)
    {
        return $user->tokenCan('entries:create') &&
            $user->id === $request->json('data.relationships.authors.data.id');
    }

    public function update(User $user, $entry)
    {
        return $user->tokenCan('entries:update') &&
            $entry->user->is($user);
    }


    public function delete(User $user, $entry)
    {
        return $user->tokenCan('entries:delete') &&
            $entry->user->is($user);
    }


    public function modifyCategories(User $user, $entry)
    {
        return $user->tokenCan('entries:modify-categories') &&
            $entry->user->is($user);
    }

    public function modifyAuthors(User $user, $entry)
    {
        return $user->tokenCan('entries:modify-authors') &&
            $entry->user->is($user);
    }
}
