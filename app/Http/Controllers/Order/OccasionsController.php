<?php

namespace App\Http\Controllers\Order;

use App\Components\Orders\Services\OccasionsService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Order\OccasionListResource;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OccasionsController extends Controller
{
    /**
     * @throws \Exception
     * @see OccasionsControllerOA::list()
     */
    public function list(OccasionsService $service): JsonResponse
    {
        $response = $service->list();

        $resource = new OccasionListResource($response);

        return $resource->response()->setStatusCode(Response::HTTP_OK);
    }
}