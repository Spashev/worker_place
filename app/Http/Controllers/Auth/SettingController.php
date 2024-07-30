<?php

namespace App\Http\Controllers\Auth;

use App\Components\Auth\Services\AuthService;
use App\Components\Auth\Services\SettingService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SettingRequest;
use App\Http\Resources\Auth\SettingsListResource;
use Bloomex\Common\Blca\Models\BlcaSetting;
use Symfony\Component\HttpFoundation\Response;

class SettingController extends Controller
{
    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function saveSetting(SettingRequest $request, SettingService $settingsService): Response
    {
        $settingsService->saveSetting($request);
        return response()->json(['message' => 'Setting updated/saved successfully'], 201);
    }

    /**
     * @throws \Exception
     */
    public function listSettings(SettingService $settingsService): Response
    {
        $response = $settingsService->listSettings();
        $resource = SettingsListResource::make($response);
        return $resource->response()->setStatusCode(Response::HTTP_OK);
    }

    public function removeSetting(BlcaSetting $setting, SettingService $settingService): Response
    {
        $settingService->removeSetting($setting);
        return response()->json(['message' => 'Setting deleted successfully']);
    }

    /**
     * @throws \Exception
     */
    public function logoutAllUsers(AuthService $authService): Response
    {
        $authService->logoutAllUsers();
        return response()->json(['message' => 'Users logout successfully']);
    }
}