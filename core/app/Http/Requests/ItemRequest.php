<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ItemRequest extends FormRequest
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
        // Resolve item ID if this is an update request
        $itemId = null;
        if ($this->item) {
            if (is_object($this->item) && isset($this->item->id)) {
                $itemId = $this->item->id;
            } elseif (is_numeric($this->item)) {
                $itemId = $this->item;
            }
        }

        if (!$itemId && $this->route('item')) {
            $routeItem = $this->route('item');
            if (is_object($routeItem) && isset($routeItem->id)) {
                $itemId = $routeItem->id;
            } elseif (is_numeric($routeItem)) {
                $itemId = $routeItem;
            }
        }

        if (!$itemId && $this->route('id')) {
            $routeId = $this->route('id');
            if (is_numeric($routeId)) {
                $itemId = $routeId;
            }
        }

        $isEdit = !empty($itemId);
        $id = $isEdit ? ',' . $itemId : '';
        $required = $isEdit ? 'nullable' : 'required';

        $check_link = ($this->file_type == 'link') ? 'required' : 'nullable';
        if ($this->item_type == 'digital') {
            if ($isEdit) {
                $check_file = 'nullable';
            } else {
                $check_file = ($this->file_type == 'file') ? 'required' : 'nullable';
            }
        } elseif ($this->item_type == 'license') {
            if ($isEdit) {
                $check_file = 'nullable';
            } else {
                $check_file = ($this->file_type == 'file') ? 'required' : 'nullable';
            }
        } else {
            $check_file = 'nullable';
        }

        return [
            'name'            => 'required|max:255',
            'slug'            => ['required', 'unique:items,slug' . $id, 'regex:/^[a-zA-Z0-9-]+$/'],
            'category_id'     => 'required',
            'details'         => 'required',
            'link'            => $check_link,
            'file'            => [$check_file, 'file', 'mimes:zip'],
            'sort_details'    => 'required',
            'discount_price'  => 'required|max:50',
            'previous_price'  => 'nullable|max:50',
            'stock'           => 'nullable|numeric|max:9999999999',
            'estimated_profit' => 'nullable|numeric|min:0',
            'tax_id'          => 'nullable',
            'photo'           => [$required, 'mimes:jpeg,jpg,png,svg,webp,gif,bmp,tiff,tif,avif,ico,jfif,heic,heif']
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
            'name.required'            =>  __('Name field is required.'),
            'tax_id.required'          =>  __('Tax field is required.'),
            'category_id.required'     =>  __('Category field is required.'),
            'brand_id.required'        =>  __('Brand field is required.'),
            'slug.required'            =>  __('Slug field is required.'),
            'slug.unique'              =>  __('This slug has already been taken.'),
            'details.required'         =>  __('Description field is required.'),
            'sort_details.required'    =>  __('Sort Description field is required.'),
            'discount_price.required'  =>  __('Current Price field is required.'),
            'stock.required'           =>  __('Stock field is required.'),
            'photo.required'           =>  __('Image field is required.'),
            'photo.mimes'              =>  __('Please upload a valid image file.')
        ];
    }

}
