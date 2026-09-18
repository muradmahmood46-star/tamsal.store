<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class CategoryRequest extends FormRequest
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
            $slug = 'category-' . time();
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
        $required = $this->category ? '' : 'required';

        return [
            'slug'          => 'nullable|string|max:255',
            'photo'         => [$required,'mimes:jpeg,jpg,png,svg,webp,gif,bmp,tiff,tif,avif,ico,jfif,heic,heif'],
            'name'          => 'required|max:255',
            'meta_keywords' => 'nullable|max:255',
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
            'name.required'  => __('Category name is required.'),
            'photo.required' => __('Image field is required.'),
            'photo.mimes'    => __('Please upload a valid image file.'),
        ];
    }
}
