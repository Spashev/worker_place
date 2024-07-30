<?php

namespace App\Http\Requests\User;

use App\Components\User\Contracts\DTO\UserUpdateDTOInterface;
use App\Components\User\Contracts\UserUpdateInterface;
use App\Components\User\DTO\UserUpdateDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest implements UserUpdateInterface
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
            'roles' => ['required', 'array'],
            'roles.*' => ['string', Rule::exists('roles', 'name')],

            'warehouses' => ['required', 'array'],
            'warehouses.*' => ['numeric', Rule::exists('jos_vm_warehouse', 'warehouse_id')],
        ];
    }

    public function getUserDTO(): UserUpdateDTOInterface
    {
        return new UserUpdateDTO($this->request->all());
    }
}