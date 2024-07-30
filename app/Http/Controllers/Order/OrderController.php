<?php

namespace App\Http\Controllers\Order;

use App\Components\Orders\Services\OrderService\OrderQueryService;
use App\Components\User\Service\UserColumnsService;
use App\Exceptions\InvalidOrderUserAccess;
use App\Helpers\ErrorCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderListRequest;
use App\Http\Resources\Order\OrderDetailsResource;
use App\Http\Resources\Order\OrderListResource;
use Bloomex\Common\Blca\Models\BlcaOrder;
use Bloomex\Common\Blca\Models\BlcaUser;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    /**
     * @throws \Exception
     * @see OrderControllerOA::list()
     */
    public function list(OrderListRequest $request, OrderQueryService $orderService, UserColumnsService $columnService): JsonResponse
    {
        /** @var BlcaUser $auth */
        $auth = auth()->user();
        $response = $orderService->list($request);
        $columns =  $columnService->get($auth, 'Order');

        $resource = new OrderListResource($response, $columns);

        return $resource->response()->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @throws \Exception
     * @see OrderControllerOA::show()
     */
    public function show(BlcaOrder $order, OrderQueryService $orderService): Response
    {
        try {
            $response = $orderService->show($order);
        } catch (InvalidOrderUserAccess $e) {
            return response(['message' => trans(ErrorCode::ACCESS_FAILED)], Response::HTTP_NOT_ACCEPTABLE);
        }

        $resource = new OrderDetailsResource($response);
        return $resource->response()->setStatusCode(Response::HTTP_OK);
    }
}