<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use App\Models\Item;

class AffiliateRequest extends FormRequest
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
            $slug = 'affiliate-' . time() . '-' . Str::random(4);
        }

        $itemId = null;
        if ($this->affiliate) {
            if (is_object($this->affiliate) && isset($this->affiliate->id)) {
                $itemId = $this->affiliate->id;
            } elseif (is_numeric($this->affiliate)) {
                $itemId = $this->affiliate;
            }
        }
        if (!$itemId && $this->route('affiliate')) {
            $routeAffiliate = $this->route('affiliate');
            if (is_object($routeAffiliate) && isset($routeAffiliate->id)) {
                $itemId = $routeAffiliate->id;
            } elseif (is_numeric($routeAffiliate)) {
                $itemId = $routeAffiliate;
            }
        }
        if (!$itemId && $this->route('id')) {
            $routeId = $this->route('id');
            if (is_numeric($routeId)) {
                $itemId = $routeId;
            }
        }

        // Auto make slug unique if taken by another item so user is never blocked
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
        $itemId = null;
        if ($this->affiliate) {
            if (is_object($this->affiliate) && isset($this->affiliate->id)) {
                $itemId = $this->affiliate->id;
            } elseif (is_numeric($this->affiliate)) {
                $itemId = $this->affiliate;
            }
        }
        if (!$itemId && $this->route('affiliate')) {
            $routeAffiliate = $this->route('affiliate');
            if (is_object($routeAffiliate) && isset($routeAffiliate->id)) {
                $itemId = $routeAffiliate->id;
            } elseif (is_numeric($routeAffiliate)) {
                $itemId = $routeAffiliate;
            }
        }
        if (!$itemId && $this->route('id')) {
            $routeId = $this->route('id');
            if (is_numeric($routeId)) {
                $itemId = $routeId;
            }
        }

        $id = $itemId ? ',' . $itemId : '';
        $required = $itemId ? 'nullable' : 'required';

        return [
            'name'            => 'required|max:255',
            'slug'            => ['nullable', 'string', 'max:255', 'unique:items,slug' . $id, 'regex:/^[a-zA-Z0-9-]+$/'],
            'category_id'     => 'required',
            'details'         => 'required',
            'affiliate_link'  => 'required',
            'sort_details'    => 'required',
            'discount_price'  => 'required|max:50',
            'previous_price'  => 'nullable|max:50',
            'estimated_profit' => 'nullable|numeric|min:0',
            'product_from'    => 'nullable|string|max:255',
            'contact_number'  => 'nullable|string|max:100',
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
            'affiliate_link.required'  =>  __('Affiliate link is required.'),
            'category_id.required'     =>  __('Category field is required.'),
            'brand_id.required'        =>  __('Brand field is required.'),
            'slug.required'            =>  __('Slug field is required.'),
            'slug.unique'              =>  __('This slug has already been taken.'),
            'slug.regex'               =>  __('Slug format is invalid. Please use only letters, numbers, and dashes.'),
            'details.required'         =>  __('Description field is required.'),
            'sort_details.required'    =>  __('Sort Description field is required.'),
            'discount_price.required'  =>  __('Current Price field is required.'),
            'photo.required'           =>  __('Image field is required.'),
            'photo.mimes'              =>  __('Please upload a valid image file.')
        ];
    }
}
