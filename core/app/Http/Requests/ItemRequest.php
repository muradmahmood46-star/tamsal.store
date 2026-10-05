<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Item;

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
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $rawSlug = !empty($this->slug) ? $this->slug : $this->name;
        $slug = Str::slug($rawSlug);
        if (empty($slug)) {
            $slug = 'product-' . time() . '-' . Str::random(4);
        }

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

        // Auto make slug unique if taken by another item so vendor/admin is never blocked
        $existsQuery = Item::where('slug', $slug);
        if ($itemId) {
            $existsQuery->where('id', '!=', $itemId);
        }
        if ($existsQuery->exists()) {
            $slug = $slug . '-' . time() . '-' . Str::random(3);
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
            'sku'             => ['nullable', 'min:6', 'regex:/^(?=.*[a-zA-Z])(?=.*[0-9])[a-zA-Z0-9_-]+$/'],
            'slug'            => ['nullable', 'string', 'max:255', 'unique:items,slug' . $id, 'regex:/^[a-zA-Z0-9-]+$/'],
            'category_id'     => 'required',
            'details'         => 'required',
            'link'            => $check_link,
            'file'            => [$check_file, 'file', 'mimes:zip'],
            'sort_details'    => 'required',
            'discount_price'  => 'required|max:50',
            'previous_price'  => 'nullable|max:50',
            'stock'           => 'nullable|numeric|max:9999999999',
            'estimated_profit' => 'nullable|numeric|min:0',
            'is_cod'          => 'nullable|in:0,1',
            'product_from'    => 'nullable|string|max:255',
            'contact_number'  => 'nullable|string|max:100',
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
            'sku.min'                  =>  __('SKU / Product ID must be at least 6 characters.'),
            'sku.regex'                =>  __('SKU / Product ID must contain at least 1 alphabet and 1 number (min 6 characters).'),
            'tax_id.required'          =>  __('Tax field is required.'),
            'category_id.required'     =>  __('Category field is required.'),
            'brand_id.required'        =>  __('Brand field is required.'),
            'slug.required'            =>  __('Slug field is required.'),
            'slug.unique'              =>  __('This slug or SKU has already been taken.'),
            'slug.regex'               =>  __('Slug format is invalid. Please use only letters, numbers, and dashes.'),
            'details.required'         =>  __('Description field is required.'),
            'sort_details.required'    =>  __('Sort Description field is required.'),
            'discount_price.required'  =>  __('Current Price field is required.'),
            'stock.required'           =>  __('Stock field is required.'),
            'photo.required'           =>  __('Image field is required.'),
            'photo.mimes'              =>  __('Please upload a valid image file.')
        ];
    }
}
