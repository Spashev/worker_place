<?php

namespace App\Http\Requests\Product\Substitution;

use App\Components\Product\Contracts\SubstitutionHardIngredientInterface;
use Illuminate\Foundation\Http\FormRequest;

class SubstitutionHardIngredientRequest extends FormRequest implements SubstitutionHardIngredientInterface
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
            'name' => ['required', 'string', 'max:50'],
            'quantity' => ['required', 'integer', 'min:1'],
            'color' => ['string', 'max:20', 'nullable'],
            'type' => ['required', 'string', 'max:10'], //minor or major
        ];
    }

    public function getSubstitutionName(): string
    {
        return $this->input('name');
    }

    public function getQuantity(): int
    {
        return (int)$this->input('quantity');
    }

    public function getColor(): string|null
    {
        return $this->input('color');
    }
    public function getType(): string
    {
        return $this->input('type');
    }
}