<?php

namespace App\Rules\Orders;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

class OrderStatusesRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $maxChars = 2;
        $ids = explode(',', $value);

        $rules = [
            '*' => "string|max:$maxChars"
        ];

        $validator = Validator::make($ids, $rules);
        if ($validator->fails()) {
            $fail("The :attribute must be string values, max chars: $maxChars");
        }
    }
}
