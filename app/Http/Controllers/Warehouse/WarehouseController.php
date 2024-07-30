<?php

namespace App\Http\Controllers\Warehouse;

use App\Components\Warehouse\Services\WarehouseService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Warehouse\UserWarehousesListResource;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class WarehouseController extends Controller
{
    /**
     * @throws \Exception
     * @see WarehouseControllerOA::userWarehousesList()
     */
    public function userWarehousesList(WarehouseService $service): JsonResponse
    {
        $response = $service->usersWarehousesList();
        $resource = new UserWarehousesListResource($response);

        return $resource->response()->setStatusCode(Response::HTTP_OK);
    }
}