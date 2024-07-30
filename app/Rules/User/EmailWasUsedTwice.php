<?php

namespace App\Rules\User;

use Bloomex\Common\Blca\Models\BlcaUser;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EmailWasUsedTwice implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $countEmails = BlcaUser::where('email', $value)->count();

        if ($countEmails >= 2) {
            $fail("The :attribute was used twice, please ask admins for help");
        }
    }
}