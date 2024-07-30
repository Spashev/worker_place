<?php

namespace App\Http\Controllers\Auth;

use App\Components\Auth\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\LoginWorkerRequest;
use App\Http\Resources\Auth\LoginResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuthController extends Controller
{
    /**
     * @throws \Exception
     * @see AuthControllerOA::login()
     */
    public function login(LoginRequest $request, AuthService $service): JsonResponse
    {
        $response = $service->login($request);
        $resource = new LoginResource($response);

        return $resource->response()->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @throws \Exception
     * @see AuthControllerOA::loginWorker()
     */
    public function loginWorker(LoginWorkerRequest $request, AuthService $service): JsonResponse
    {
        $response = $service->loginWorker($request);
        $resource = new LoginResource($response);

        return $resource->response()->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @see AuthControllerOA::logout()
     */
    public function logout()
    {
        try {
            Auth::user()->tokens()->delete();
        } catch (Throwable $e) {
            Log::error('LOGOUT ERROR', $e);
        }
        return response(['message' => trans('auth.logout')], Response::HTTP_OK);
    }
}
