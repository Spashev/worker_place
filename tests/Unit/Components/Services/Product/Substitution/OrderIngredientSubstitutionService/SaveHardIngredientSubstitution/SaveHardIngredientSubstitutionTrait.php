<?php

namespace Tests\Unit\Components\Services\Product\Substitution\OrderIngredientSubstitutionService\SaveHardIngredientSubstitution;

use App\Http\Requests\Product\Substitution\SubstitutionHardIngredientRequest;

trait SaveHardIngredientSubstitutionTrait
{
    public function makeCredentials(array $data): SubstitutionHardIngredientRequest
    {
        $request = new SubstitutionHardIngredientRequest($data);
        $request->request->add($data);
        $request->setContainer(app());
        $request->validateResolved();

        return $request;
    }
}