<?php

namespace App\Http\Requests\Auth;

use App\Components\Auth\Contracts\SettingsInterface;
use App\Rules\Auth\SettingRule;
use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest implements SettingsInterface
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
            'id' => 'integer',
            'key' => 'required|string|min:3|max:255',
            'type' => 'required|in:integer,string,boolean',
            'value' => ['required', 'string', 'min:1', 'max:255', new SettingRule($this->input('type'))],
        ];
    }

    public function getId(): ?int
    {
        return $this->input('id');
    }

    public function getKey(): string
    {
        return $this->input('key');
    }

    public function getValue(): string
    {
        return $this->input('value');
    }

    public function getType(): string
    {
        return $this->input('type');
    }
}