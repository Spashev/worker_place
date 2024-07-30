<?php

namespace App\Rules\Orders;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

class IdsRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $ids = explode(',', $value);

        $rules = [
            '*' => 'numeric'
        ];

        $validator = Validator::make($ids, $rules);
        if ($validator->fails()) {
            $fail('The :attribute must be int values');
        }
    }
}
