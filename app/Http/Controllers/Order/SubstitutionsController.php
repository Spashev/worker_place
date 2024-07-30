<?php

namespace App\Http\Controllers\Order;

use App\Helpers\SuccessCode;
use App\Http\Controllers\Controller;
use Bloomex\Common\Blca\Models\BlcaOrderItem;
use Symfony\Component\HttpFoundation\Response;
use Bloomex\Common\Blca\Models\BlcaOrderItemIngredient;
use App\Components\Product\Services\ProductIngredientService;
use App\Http\Resources\Product\Substitution\OrderItemResource;
use App\Components\Product\Services\OrderItemSubstitutionService;
use App\Http\Resources\Product\Substitution\SubstitutionsListResource;
use App\Components\Product\Services\OrderIngredientSubstitutionService;
use App\Http\Requests\Product\Substitution\SubstitutionHardIngredientRequest;
use App\Http\Requests\Product\Substitution\SubstitutionSoftIngredientRequest;
use App\Http\Resources\Product\Substitution\OrderIngredientSubstitutionResource;

class SubstitutionsController extends Controller
{
    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function list(BlcaOrderItemIngredient $ingredient, ProductIngredientService $service): Response
    {
        $data = $service->list($ingredient->ingredient_name);
        $resource = new SubstitutionsListResource($data);

        return $resource->response()->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function saveProductSubstitution(
        BlcaOrderItem                     $product,
        OrderItemSubstitutionService      $service,
        SubstitutionSoftIngredientRequest $request
    ): Response
    {
        $data = $service->saveItemSubstitution($product->order_item_id, $request);
        $resource = new OrderItemResource($data);

        return $resource->response()->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function saveHardSubstitution(
        BlcaOrderItemIngredient            $ingredient,
        OrderIngredientSubstitutionService $service,
        SubstitutionHardIngredientRequest  $request)
    : Response
    {
        $data = $service->saveHardIngredientSubstitution($ingredient->order_ingredient_id, $request);
        $resource = new OrderIngredientSubstitutionResource($data);

        return $resource->response()->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function saveSoftSubstitution(
        BlcaOrderItemIngredient            $ingredient,
        OrderIngredientSubstitutionService $service,
        SubstitutionSoftIngredientRequest  $request)
    : Response
    {
        $service->saveSoftIngredientSubstitution($ingredient->order_ingredient_id, $request);
        return response(['message' => trans(SuccessCode::SUBSTITUTION_DATA_SAVED)], Response::HTTP_CREATED);
    }
}