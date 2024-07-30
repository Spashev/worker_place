<?php

namespace App\Rules\Auth;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

class SettingRule implements ValidationRule
{
    protected $type;

    /**
     * Create a new rule instance.
     *
     * @param string $type
     */
    public function __construct(string|null $type)
    {
        $this->type = $type;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $isValid = match($this->type) {
            'integer' => is_numeric($value) && intval($value) == $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null,
            'string' => is_string($value),
            default => false,
        };

        if (!$isValid) {
            $fail("The $attribute must be a valid {$this->type}.");
        }
    }
}