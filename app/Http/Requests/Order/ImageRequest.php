<?php

namespace App\Http\Requests\Order;

use App\Components\Orders\Contracts\ImageRequestInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class ImageRequest extends FormRequest implements ImageRequestInterface
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
    public function rules(): array
    {
        return [
            'image' => ['required', 'mimes:jpg,jpeg,png', 'max:5000'],
        ];
    }

    public function getImage(): UploadedFile
    {
        return $this->file('image');
    }

    public function getExtension(): string
    {
        return $this->file('image')->extension();
    }

    public function messages()
    {
        return [
            'image.required' => "Image is required",
            'image.max' => "Maximum image size to upload is 5MB (5242 KB). If you are uploading a photo, try to reduce its resolution to make it under 5MB",
            'image.mimes' => "You must use JPEG,PNG types",
        ];
    }
}