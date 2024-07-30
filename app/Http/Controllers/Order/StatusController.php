<?php

namespace App\Http\Controllers\Order;

use App\Components\Orders\Services\StatusService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Order\StatusListResource;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class StatusController extends Controller
{
    /**
     * @throws \Exception
     * @see StatusControllerOA::list()
     */
    public function list(StatusService $service): JsonResponse
    {
        $response = $service->list();
        $resource = new StatusListResource($response);

        return $resource->response()->setStatusCode(Response::HTTP_OK);
    }
}