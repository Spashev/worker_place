<?php

namespace App\Http\Requests\Order;

use App\Components\Orders\Contracts\PaginationInterface;
use App\Rules\Orders\RangeDatesRule;
use App\Rules\Orders\IdsRule;
use App\Rules\Orders\OrderStatusesRule;
use Illuminate\Foundation\Http\FormRequest;

class OrderListRequest extends FormRequest implements PaginationInterface
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

    // adding default value
    public function validationData()
    {
        if (is_null($this->get('sort')) && is_null($this->get('sort'))) {
            $this->query->add(['sort' => '-id']);
        }

        return $this->all();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'order_currency' => 'string|max:3',
            'id' => ['string', new IdsRule],
            'delivered_at' => ['string', new RangeDatesRule],
            'created_at' => ['string', new RangeDatesRule],
            'updated_at' => ['string', new RangeDatesRule],
            'status' => ['string', new OrderStatusesRule],
            'warehouse' => 'string|max:255',
            'order_status' => 'string|max:255',
            'occasion' => 'string|max:255',

            'search' => 'string|max:255',
            'sort' => 'nullable|string',
            'sort.*' => 'string',

            'per_page' => 'numeric|gt:0',
            'page' => 'numeric|gt:0',
        ];
    }

    public function getPerPage(): int
    {
        return (int)$this->input('per_page', 10);
    }

    public function getPage(): int
    {
        return (int)$this->input('page');
    }
}