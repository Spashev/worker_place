<?php

namespace App\Http\Requests\User;

use App\Components\User\Contracts\DTO\UserDTOInterface;
use App\Components\User\Contracts\UserCreateInterface;
use App\Components\User\DTO\UserDTO;
use App\Rules\User\EmailWasUsedTwice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserCreateRequest extends FormRequest implements UserCreateInterface
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
            'name' => 'string|max:191',
            'email' => [
                'required',
                'email',
                'max:191',
                'regex:/(.+)@(.+)\\.(.+)/i',
                new EmailWasUsedTwice(),
            ],

            'roles' => ['required', 'array'],
            'roles.*' => ['string', Rule::exists('roles', 'name')],

            'warehouses' => ['required', 'array'],
            'warehouses.*' => ['numeric', Rule::exists('jos_vm_warehouse', 'warehouse_id')],
        ];
    }

    public function getUserDTO(): UserDTOInterface
    {
        return new UserDTO($this->request->all());
    }
}