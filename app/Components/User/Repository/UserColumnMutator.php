<?php

namespace App\Components\User\Repository;

use App\Components\User\Contracts\UserColumnsInterface;
use Bloomex\Common\Blca\Models\BlcaColumn;
use Bloomex\Common\Blca\Models\BlcaUser;
use Illuminate\Database\Eloquent\Builder;

class UserColumnMutator
{
    public function __construct(
        private readonly BlcaColumn $blcaColumn
    ) {
    }

    public function updateUserColumns(UserColumnsInterface $resource, BlcaUser $user): BlcaColumn
    {
        $this->deleteUserColumns($resource, $user);

        return $this->blcaColumn->create([
            'columns' => $resource->getColumns(),
            'model' => $resource->getModel(),
            'user_id' => $user->id,
        ]);
    }

    private function deleteUserColumns(UserColumnsInterface $resource, BlcaUser $user): void
    {
        $this->getQuery()
            ->where('user_id', $user->id)
            ->where('model', $resource->getModel())
            ->delete();
    }

    private function getQuery(): Builder
    {
        return $this->blcaColumn->newQuery();
    }
}