<?php

namespace App\Components\User\Service;

use App\Components\Auth\Services\RoleService;
use App\Components\User\Contracts\UserCreateInterface;
use App\Components\User\Contracts\UserRolesInterface;
use App\Components\User\Contracts\UserUpdateInterface;
use App\Components\User\Contracts\UserWarehousesInterface;
use App\Components\User\Repository\UserMutator;
use App\Components\User\Service\Validators\UserAssignRoleValidator;
use App\Components\User\Service\Validators\UserAssignWarehouseValidator;
use App\Components\User\Service\Validators\UserCreateValidator;
use App\Components\User\Service\Validators\UserUpdateValidator;
use App\Models\User;
use Bloomex\Common\Blca\Models\BlcaUser;
use Exception;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Application;
use Illuminate\Validation\ValidationException;

class UserMutatorService
{
    public function __construct(
        private readonly UserMutator     $command,
        private readonly Application     $app,
        private readonly DatabaseManager $db,
        private readonly Log             $log,
        private readonly RoleService     $roleService,
    ) {
    }

    public function setLastVisit(BlcaUser $user): BlcaUser
    {
        $updatedUser = $this->command->updateLastVisit($user);
        $updatedUser->save();

        return $updatedUser;
    }

    /**
     * @throws ValidationException|\Throwable
     */
    public function create(UserCreateInterface $request): BlcaUser
    {
        /** @var User $creator */
        $creator = auth()->user();
        $userDto = $request->getUserDTO();

        $validator = $this->app->make(UserCreateValidator::class);
        $validator->check($userDto, $creator);
        try {
            $this->db->beginTransaction();
            $newUser = $this->command->create($userDto);
            $newUser->warehouses()->attach($userDto->getWarehouses());
            $newUser->assignRole($userDto->getRoles());

            $this->db->commit();
        } catch (\Throwable $t) {
            $this->db->rollBack();
            $this->log::error($t->getMessage());
            throw new Exception('Internal error check logs');
        }

        return $newUser;
    }

    /**
     * @throws ValidationException|\Throwable
     */
    public function assignRoles(UserRolesInterface $request, User $user): BlcaUser
    {
        /** @var User $creator */
        $creator = auth()->user();
        $validator = $this->app->make(UserAssignRoleValidator::class);
        $validator->check($creator, $request->getRoles());
        try {
            $this->db->beginTransaction();
            $user->syncRoles($request->getRoles());

            $this->db->commit();
        } catch (\Throwable $t) {
            $this->db->rollBack();
            $this->log::error($t->getMessage());
            throw new Exception('Internal error check logs');
        }

        return $user;
    }

    /**
     * @throws ValidationException|\Throwable
     */
    public function assignWarehouses(UserWarehousesInterface $request, User $user): BlcaUser
    {
        /** @var User $creator */
        $creator = auth()->user();
        $warehousesIds = $request->getWarehouses();
        $validator = $this->app->make(UserAssignWarehouseValidator::class);
        $validator->check($creator, $warehousesIds);
        try {
            $this->db->beginTransaction();
            $user->warehouses()->attach($warehousesIds);

            $this->db->commit();
        } catch (\Throwable $t) {
            $this->db->rollBack();
            $this->log::error($t->getMessage());
            throw new Exception('Internal error check logs');
        }

        return $user;
    }

    public function update(UserUpdateInterface $request, User $user): User
    {
        /** @var User $creator */
        $creator = auth()->user();
        $userDto = $request->getUserDTO();
        $validator = $this->app->make(UserUpdateValidator::class);
        $validator->check($userDto, $creator);
        try {
            $this->db->beginTransaction();
            $user->warehouses()->sync($userDto->getWarehouses());
            $user->syncRoles($userDto->getRoles());

            $this->db->commit();
        } catch (\Throwable $t) {
            $this->db->rollBack();
            $this->log::error($t->getMessage());
            throw new Exception('Internal error check logs');
        }

        return $user;
    }
}
