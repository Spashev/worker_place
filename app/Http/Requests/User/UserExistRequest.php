<?php

namespace App\Http\Requests\User;

use App\Rules\User\EmailWasUsedTwice;
use Illuminate\Foundation\Http\FormRequest;

class UserExistRequest extends FormRequest
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
            'email' => [
                'required',
                'email',
                'max:191',
                'regex:/(.+)@(.+)\\.(.+)/i',
                new EmailWasUsedTwice(),
            ],
        ];
    }
    public function getEmail(): string
    {
        return $this->query('email');
    }
}