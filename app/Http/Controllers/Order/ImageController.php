<?php

namespace App\Http\Controllers\Order;

use App\Helpers\ErrorCode;
use App\Helpers\SuccessCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\ImageRequest;
use App\Http\Resources\Order\Image\HistoryImageResource;
use Bloomex\Common\Blca\Models\BlcaOrderHistoryImage;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Bloomex\Common\Blca\Models\BlcaOrderHistory;
use App\Components\Orders\Services\ImageService\ImageService;

class ImageController extends Controller
{
    /**
     * @throws \Exception
     */
    public function addHistoryImage(
        ImageRequest     $request,
        BlcaOrderHistory $history,
        ImageService     $service
    ): Response|JsonResponse
    {
        $response = $service->addHistoryImage($history, $request);

        if ($response) {
            $resource = new HistoryImageResource($response);
            return $resource->response()->setStatusCode(Response::HTTP_CREATED);
        }

        return response(
            ['message' => trans(ErrorCode::ERROR_SAVE_IMAGE)],
            Response::HTTP_FAILED_DEPENDENCY
        );
    }

    /**
     * @throws \Exception
     */
    public function deleteHistoryImage(BlcaOrderHistoryImage $historyImage, ImageService $service): Response
    {
        $response = $service->deleteHistoryImage($historyImage);

        if ($response) {
            return response(['message' => trans(SuccessCode::IMAGE_DATA_REMOVED)], Response::HTTP_OK);
        }
        return response(['message' => trans(ErrorCode::ERROR_REMOVED_IMAGE)], Response::HTTP_BAD_REQUEST);
    }
}