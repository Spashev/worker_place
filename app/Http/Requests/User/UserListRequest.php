<?php

namespace App\Http\Requests\User;

use App\Components\Orders\Contracts\PaginationInterface;
use App\Rules\Orders\IdsRule;
use App\Rules\Orders\RangeDatesRule;
use Illuminate\Foundation\Http\FormRequest;

class UserListRequest extends FormRequest implements PaginationInterface
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
            'id' => ['string', new IdsRule],
            'created_at' => ['string', new RangeDatesRule],
            'updated_at' => ['string', new RangeDatesRule],

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