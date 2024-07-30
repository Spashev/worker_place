<?php

namespace App\Components\User\Service;

use App\Components\User\Repository\UserColumnMutator;
use App\Components\User\Repository\UserColumnQuery;
use App\Http\Requests\User\UserColumnsRequest;
use Bloomex\Common\Blca\Models\BlcaColumn;
use Bloomex\Common\Blca\Models\BlcaUser;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Log;
use Exception;

class UserColumnsService
{
    public function __construct(
        private readonly UserColumnMutator $command,
        private readonly UserColumnQuery   $accessor,
        private readonly DatabaseManager   $db,
        protected readonly Log             $log,
    ) {
    }

    public function save(UserColumnsRequest $request): BlcaColumn
    {
        try {
            /** @var BlcaUser $user */
            $user = auth()->user();
            $this->db->beginTransaction();
            $blcaColumn = $this->command->updateUserColumns($request, $user);
            $this->db->commit();
        } catch (\Throwable $t) {
            $this->db->rollBack();
            $this->log::error($t->getMessage());
            throw new Exception('Internal error check logs');
        }

        return $blcaColumn;
    }

    public function get(BlcaUser $user, string $model): ?BlcaColumn
    {
        try {
            return $this->accessor->getUserColumns($user, $model);

        } catch (\Throwable $t) {
            $this->log::error($t->getMessage());
            throw new Exception('Internal error check logs');
        }
    }
}