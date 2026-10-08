<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class BrandRequest extends FormRequest
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
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $rawSlug = $this->slug ?: $this->name;
        $slug = Str::slug($rawSlug);
        if (empty($slug)) {
            $slug = 'brand-' . time();
        }

        $this->merge([
            'slug' => $slug,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $setting = \App\Models\Setting::first();
        $isBrandImageEnabled = ($setting && isset($setting->is_brand_image)) ? (int)$setting->is_brand_image : 1;

        if ($isBrandImageEnabled === 0) {
            return [
                'photo' => ['nullable', 'mimes:jpeg,jpg,png,svg,webp,gif,bmp,tiff,tif,avif,ico,jfif,heic,heif'],
                'name'  => 'required|max:255',
                'slug'  => 'nullable|string|max:255',
            ];
        }

        $required = $this->brand ? 'nullable' : 'required';

        return [
            'photo' => [$required, 'mimes:jpeg,jpg,png,svg,webp,gif,bmp,tiff,tif,avif,ico,jfif,heic,heif'],
            'name'  => 'required|max:255',
            'slug'  => 'nullable|string|max:255',
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
            'photo.required' => __('Photo field is required.'),
            'photo.mimes'    => __('Please upload a valid image file.'),
            'name.required'  => __('Brand Name is required.'),
        ];
    }
}
