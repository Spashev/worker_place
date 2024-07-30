<?php

namespace App\Http\Controllers\Auth;

use App\Components\Auth\Services\MyMutatorService;
use App\Http\Controllers\Controller;
use App\Http\Requests\My\MySettingsRequest;
use App\Http\Resources\Auth\MySettingsResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MyController extends Controller
{
    /**
     * @throws \Exception
     */
    public function createCode(MyMutatorService $myMutatorService): JsonResponse
    {
        $response = $myMutatorService->createCode();
        return new JsonResponse(['access_code' => $response], Response::HTTP_CREATED);
    }

    /**
     * @throws \Exception
     */
    public function saveSettings(MySettingsRequest $request, MyMutatorService $myMutatorService): JsonResponse
    {
        $response = $myMutatorService->saveSettings($request);
        $resource = new MySettingsResource($response);

        return $resource->response()->setStatusCode(Response::HTTP_CREATED);
    }
}