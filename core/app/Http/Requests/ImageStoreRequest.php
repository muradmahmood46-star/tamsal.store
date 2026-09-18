<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImageStoreRequest extends FormRequest
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
            'photo' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($value && $value instanceof \Illuminate\Http\UploadedFile) {
                        $ext = strtolower($value->getClientOriginalExtension());
                        $allowed = ['jpeg', 'jpg', 'png', 'svg', 'webp', 'gif', 'bmp', 'tiff', 'tif', 'avif', 'ico', 'jfif', 'heic', 'heif', 'json', 'lottie', 'txt'];
                        if (!in_array($ext, $allowed)) {
                            $fail(__('Please upload a valid image or Lottie animation file.'));
                        }
                    }
                }
            ]
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'photo.required' => __('Image field is required.')
        ];
    }



}
