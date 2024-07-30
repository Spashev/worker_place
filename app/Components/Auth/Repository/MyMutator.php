<?php

namespace App\Components\Auth\Repository;

use App\Models\User;

class MyMutator
{
    public function updateAccessCode(User $user, string $accessCode): bool
    {
        $user->access_code = md5($accessCode);
        return $user->save();
    }
}