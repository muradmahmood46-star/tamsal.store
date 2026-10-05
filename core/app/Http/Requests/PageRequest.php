<?php

namespace App\Http\Requests;

use Illuminate\{
    Validation\Rule,
    Foundation\Http\FormRequest,
    Support\Str
};

class PageRequest extends FormRequest
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
        $rawSlug = $this->slug ?: $this->title;
        $slug = Str::slug($rawSlug);
        if (empty($slug)) {
            $slug = 'page-' . time();
        }

        $this->merge([
            'slug' => strtolower($slug),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $mains = ['shop','contact','blog','cart','checkout'];
        $id = $this->page ? ',' . $this->page->id : '';

        return  [
            'title'   => 'required|max:255',
            'slug'    => ['required', 'max:255', 'regex:/^[a-zA-Z0-9-]+$/', Rule::notIn($mains), 'unique:pages,slug' . $id],
            'details' => 'required',
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
            'slug.required' => __('Slug field is required.'),
            'slug.unique'   => __('This slug has already been taken.'),
            'slug.not_in'   => __('You can not use this slug.'),
            'slug.regex'    => __('Slug Must Not Have Any Special Characters.')
        ];
    }
}
