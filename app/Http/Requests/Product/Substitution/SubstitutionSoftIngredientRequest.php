<?php

namespace App\Http\Requests\Product\Substitution;

use App\Components\Product\Contracts\SubstitutionSoftIngredientInterface;
use Illuminate\Foundation\Http\FormRequest;

class SubstitutionSoftIngredientRequest extends FormRequest implements SubstitutionSoftIngredientInterface
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => ['required', 'string', 'max:10'], //minor or major
        ];
    }

    public function getType(): string
    {
        return $this->input('type');
    }
}