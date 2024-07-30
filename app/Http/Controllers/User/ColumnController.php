<?php

namespace App\Http\Controllers\User;

use App\Components\User\Service\UserColumnsService;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserColumnsRequest;
use Bloomex\Common\Blca\Models\BlcaUser;
use Symfony\Component\HttpFoundation\Response;

class ColumnController extends Controller
{
    /**
     * @throws \Exception
     */
    public function store(UserColumnsRequest $request, UserColumnsService $service): Response
    {
        $service->save($request);

        return response(['message' => trans('columns_saved')], Response::HTTP_OK);
    }
}