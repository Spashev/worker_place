<?php

namespace App\Http\Controllers\Order;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Components\Orders\Services\OrderHistoryService\OrderHistoryService;
use App\Components\Orders\Services\PackagerService\PackagerManager;
use App\Helpers\ErrorCode;
use App\Helpers\SuccessCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\InPackagingRequest;
use App\Http\Resources\Order\OrderPackaging\OrderInPackagingResource;
use Bloomex\Common\Blca\Models\BlcaOrder;
use Symfony\Component\HttpFoundation\Response;

class PackagerController extends Controller
{
    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function inPackaging(InPackagingRequest $request, PackagerManager $manager): JsonResponse
    {
        $data = $manager->doPackaging($request);
        $history = $manager->getHistory();
        $resource = new OrderInPackagingResource($history, $data);
        return $resource->response()->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function confirmSet(InPackagingRequest $request, PackagerManager $manager): Response
    {
        $products = $manager->confirmSet($request);
        if ($products->isEmpty()) {
            return response(['message' => trans(ErrorCode::ERROR_SAVE_HISTORY)], Response::HTTP_BAD_REQUEST);
        }
        return response(['message' => trans(SuccessCode::HISTORY_DATA_SAVED)], Response::HTTP_CREATED);
    }

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function packaged(BlcaOrder $order, PackagerManager $manager): Response
    {
        $history = $manager->packaged($order);
        if (isset($history)) {
            return response(['message' => trans(SuccessCode::HISTORY_DATA_SAVED)], Response::HTTP_CREATED);
        }
        return response(['message' => trans(ErrorCode::ERROR_SAVE_HISTORY)], Response::HTTP_BAD_REQUEST);
    }
}