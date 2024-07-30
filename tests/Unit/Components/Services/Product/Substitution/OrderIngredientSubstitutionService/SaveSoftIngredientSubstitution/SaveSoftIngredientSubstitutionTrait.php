<?php

namespace Tests\Unit\Components\Services\Product\Substitution\OrderIngredientSubstitutionService\SaveSoftIngredientSubstitution;

use App\Http\Requests\Product\Substitution\SubstitutionSoftIngredientRequest;

trait SaveSoftIngredientSubstitutionTrait
{
    public function makeCredentials(array $data): SubstitutionSoftIngredientRequest
    {
        $request = new SubstitutionSoftIngredientRequest($data);
        $request->request->add($data);
        $request->setContainer(app());
        $request->validateResolved();

        return $request;
    }
}