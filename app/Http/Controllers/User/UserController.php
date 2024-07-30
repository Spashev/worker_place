<?php

namespace App\Http\Controllers\User;

use App\Components\Auth\Services\RoleService;
use App\Components\User\Service\UserColumnsService;
use App\Components\User\Service\UserMutatorService;
use App\Components\User\Service\UserQueryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserExistRequest;
use App\Http\Requests\User\UserListRequest;
use App\Http\Requests\User\UserRolesRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Requests\User\UserWarehousesRequest;
use App\Http\Resources\User\UserExistResource;
use App\Http\Resources\User\UserListResource;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\User\UserRolesResource;
use App\Http\Resources\User\UserWarehouseResource;
use App\Models\User;
use Bloomex\Common\Blca\Models\BlcaUser;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * @throws \Exception
     */
    public function list(UserListRequest $request, UserQueryService $userService, UserColumnsService $columnService): JsonResponse
    {
        /** @var BlcaUser $user */
        $user = auth()->user();

        $response = $userService->list($request);
        $columns = $columnService->get($user, 'User');

        $resource = new UserListResource($response, $columns);

        return $resource->response()->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function create(UserCreateRequest $request, UserMutatorService $userService): JsonResponse
    {
        $response = $userService->create($request);
        $resource = new UserResource($response);

        return $resource->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function update(User $user, UserUpdateRequest $request, UserMutatorService $userService): JsonResponse
    {
        $userService->update($request, $user);
        return new JsonResponse(['message' => trans('profile_updated')], Response::HTTP_ACCEPTED);
    }


    /**
     * @throws \Exception
     */
    public function exist(UserExistRequest $request, UserQueryService $userService): JsonResponse
    {
        $response = $userService->exist($request);
        if ($response instanceof User) {
            $resource = new UserExistResource($response);
        } else {
            return new JsonResponse(['exist' => false], Response::HTTP_OK);
        }

        return $resource->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @throws \Exception|\Throwable
     */
    public function availableRoles(RoleService $roleService): JsonResponse
    {
        /** @var User $creator */
        $creator = auth()->user();
        $response = $roleService->getAllManagedRoles($creator->getGuardRoleNames());

        return new JsonResponse(['roles' => $response], Response::HTTP_OK);
    }

    /**
     * @throws \Exception|\Throwable
     */
    public function assignRoles(UserRolesRequest $request, User $user, UserMutatorService $userService): JsonResponse
    {
        $response = $userService->assignRoles($request, $user);
        $resource = new UserRolesResource($response);

        return $resource->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @throws \Exception|\Throwable
     */
    public function assignWarehouses(UserWarehousesRequest $request, User $user, UserMutatorService $userService): JsonResponse
    {
        $response = $userService->assignWarehouses($request, $user);
        $resource = new UserWarehouseResource($response);

        return $resource->response()->setStatusCode(Response::HTTP_CREATED);
    }
}