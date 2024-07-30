<?php

namespace App\Components\Auth\Repository;

use Illuminate\Support\Facades\DB;

class AuthMutator
{
    public function deleteUsersOMS(): bool
    {
        return DB::delete("DELETE FROM personal_access_tokens WHERE name IN ('oms-app-packager', 'oms-app')");
    }
}