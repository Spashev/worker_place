<?php

namespace App\Components\User\Repository;

use Bloomex\Common\Blca\Models\BlcaColumn;
use Bloomex\Common\Blca\Models\BlcaUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class UserColumnQuery
{
    public function __construct(
        private readonly BlcaColumn $blcaColumn
    ) {
    }

    public function getUserColumns(BlcaUser $user, string $model): ?BlcaColumn
    {
        return  $this->blcaColumn
            ->where('user_id', $user->id)
            ->where('model', $model)
            ->first('columns');
    }
}