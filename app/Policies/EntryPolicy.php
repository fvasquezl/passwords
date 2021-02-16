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
}
