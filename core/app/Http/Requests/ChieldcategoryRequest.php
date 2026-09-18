<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class ChieldcategoryRequest extends FormRequest
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
            $slug = 'childcategory-' . time();
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
        return [
            'slug'           => 'nullable|string|max:255',
            'category_id'    => 'required',
            'subcategory_id' => 'required',
            'name'           => 'required|max:255'
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
            'category_id.required'    => __('Category field is required.'),
            'subcategory_id.required' => __('Subcategory field is required.'),
            'name.required'           => __('Name field is required.'),
        ];
    }
}
