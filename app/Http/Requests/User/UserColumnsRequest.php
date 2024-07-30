<?php

namespace App\Http\Requests\User;

use App\Components\User\Contracts\UserColumnsInterface;
use Illuminate\Foundation\Http\FormRequest;

class UserColumnsRequest extends FormRequest implements UserColumnsInterface
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
            'model' => 'required|string|max:30',
            'columns' => 'required|json',
        ];
    }

    public function getColumns()
    {
        return $this->input('columns');
    }

    public function getModel(): string
    {
        return $this->input('model');
    }
}