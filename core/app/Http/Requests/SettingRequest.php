<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
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
        if(isset($this->is_validate)){
            return [
                'title' => 'required|max:255',
                'footer_address' => 'required|max:255',
                'footer_phone' => 'required|max:255',
                'footer_email' => 'required|max:255',
                'copy_right' => 'required|max:255',
                'friday_start' => 'required|max:255',
                'friday_end' => 'required|max:255',
                'working_days_from_to' => 'required|max:255',
                'logo' => 'mimes:jpeg,jpg,png,svg,webp,gif,bmp,tiff,tif,avif,ico,jfif,heic,heif',
                'meta_image' => 'mimes:jpeg,jpg,png,svg,webp,gif,bmp,tiff,tif,avif,ico,jfif,heic,heif',
                'loader' => 'mimes:jpeg,jpg,png,svg,gif,webp,bmp,ico',
                'favicon' => 'mimes:jpeg,jpg,png,svg,ico,gif,webp,png',
                'feature_image' => 'mimes:jpeg,jpg,png,svg,webp,gif,bmp,tiff,tif,avif,ico,jfif,heic,heif',
                'home_background' => 'mimes:jpeg,jpg,png,svg,webp,gif,bmp,tiff,tif,avif,ico,jfif,heic,heif',
                'breadcumb_background' => 'mimes:jpeg,jpg,png,svg,webp,gif,bmp,tiff,tif,avif,ico,jfif,heic,heif',
                'footer_background' => 'mimes:jpeg,jpg,png,svg,webp,gif,bmp,tiff,tif,avif,ico,jfif,heic,heif',
                'popup_banner' => 'mimes:jpeg,jpg,png,svg,webp,gif,bmp,tiff,tif,avif,ico,jfif,heic,heif',
                'footer_gateway_img' => 'mimes:jpeg,jpg,png,svg,webp,gif,bmp,tiff,tif,avif,ico,jfif,heic,heif'
            ];
        }else{
            return [

            ];
        }

    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'logo.mimes'    => __('Please upload a valid image file.'),
            'loader.mimes'    => __('Please upload a valid image file.'),
            'favicon.mimes'    => __('Please upload a valid favicon file.'),
            'feature_image.mimes'    => __('Please upload a valid image file.'),
            'home_background.mimes'    => __('Please upload a valid image file.'),
            'breadcumb_background.mimes'    => __('Please upload a valid image file.'),
            'footer_background.mimes'    => __('Please upload a valid image file.'),
            'popup_banner.mimes'    => __('Please upload a valid image file.'),
        ];
    }

}
