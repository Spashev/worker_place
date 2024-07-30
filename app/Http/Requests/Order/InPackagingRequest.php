<?php

namespace App\Http\Requests\Order;

use App\Components\Orders\Contracts\IngredientListInterface;

use Illuminate\Foundation\Http\FormRequest;

class InPackagingRequest extends FormRequest implements IngredientListInterface
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
            'order_code' => ['required_without:order_id','string'],
            'order_id' => ['required_without:order_code','string'],
        ];
    }

    public function getOrderCode(): string
    {
        return $this->input('order_code');
    }

    public function getOrderId(): int
    {
        return (int)$this->input('order_id');
    }

    public function isQrCode(): bool
    {
        return $this->input('order_code') !== null;
    }
}