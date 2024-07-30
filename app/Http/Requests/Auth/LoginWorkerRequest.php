<?php

namespace App\Http\Requests\Auth;

use App\Components\Auth\Contracts\LoginWorkerRequestInterface;
use Illuminate\Foundation\Http\FormRequest;

class LoginWorkerRequest extends FormRequest implements LoginWorkerRequestInterface
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
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
            'password' => [
                'required',
            ],
        ];
    }

    public function getUserPassword(): string
    {
        return $this->input('password');
    }
}