<?php

namespace App\Http\Requests\User;

use App\Components\User\Contracts\UserWarehousesInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserWarehousesRequest extends FormRequest implements UserWarehousesInterface
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
            'warehouses' => ['required', 'array', 'min:1'],
            'warehouses.*' => ['required', 'numeric', Rule::exists('jos_vm_warehouse', 'warehouse_id')],
        ];
    }

    public function getWarehouses(): array
    {
        return $this->input('warehouses');
    }
}