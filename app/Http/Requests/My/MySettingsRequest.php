<?php

namespace App\Http\Requests\My;

use App\Components\Auth\Contracts\MySettingsInterface;
use Illuminate\Foundation\Http\FormRequest;

class MySettingsRequest extends FormRequest implements MySettingsInterface
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
            'theme' => 'string|max:191',
            'lang' => 'string|max:191'
        ];
    }

    public function getTheme(): string
    {
        return $this->input('theme');
    }

    public function getLang(): string
    {
        return $this->input('lang');
    }
}