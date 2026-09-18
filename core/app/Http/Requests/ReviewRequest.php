<?php

namespace App\Http\Requests;

use Illuminate\{
    Foundation\Http\FormRequest,
    Http\Exceptions\HttpResponseException,
    Contracts\Validation\Validator
};


class ReviewRequest extends FormRequest
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
            'rating' => 'required|numeric|min:1|max:5',
            'review' => 'required',
            'photo'  => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:8192'
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
            'rating.required'   =>  __('Rating field is required.'),
            'review.required'   =>  __('Review field is required.'),
            'photo.image'       =>  __('The uploaded file must be an image.'),
            'photo.mimes'       =>  __('The photo must be a JPG, PNG, WebP, or GIF image.'),
            'photo.max'         =>  __('The photo size must not exceed 8MB.')
        ];
    }

    /**
     * Returning json response.
     *
     * @return array
     */

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(array('errors' => $validator->getMessageBag()->toArray())));
    }

}
