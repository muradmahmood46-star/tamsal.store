<?php

namespace App\Repositories\Back;

use App\{
    Models\Item,
    Models\Gallery,
    Helpers\ImageHelper
};
use App\Models\Currency;
use App\Models\Review;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class ItemRepository
{

    /**
     * Store item.
     *
     * @param  \App\Http\Requests\ItemRequest  $request
     * @return void
     */

    public function store($request)
    {
        self::ensureColumnsExist();
        
        $input = $request->all();
        if ($file = $request->file('photo')) {
            $images_name = ImageHelper::ItemhandleUploadedImage($request->file('photo'),'images');

            $input['photo'] = $images_name[0];
            $input['thumbnail'] = $images_name[1];
        }

        $curr = Currency::where('is_default',1)->first();
        $input['discount_price'] = $request->discount_price / $curr->value;
        $input['previous_price'] = $request->previous_price / $curr->value;

        if($request->has('meta_keywords')){
            $input['meta_keywords'] = str_replace(["value", "{", "}", "[","]",":","\""], '', $request->meta_keywords);
        }

        if($request->has('is_social')){
            $input['social_icons'] = json_encode($input['social_icons']);
            $input['social_links'] = json_encode($input['social_links']);
        }else{
            $input['is_social']    = 0;
            $input['social_icons'] = null;
            $input['social_links'] = null;
        }

        if($request->has('tags')){
            $input['tags'] = str_replace(["value", "{", "}", "[","]",":","\""], '', $request->tags);
        }

        if($request->has('is_specification')){
            $input['specification_name'] = json_encode($input['specification_name']);
            $input['specification_description'] = json_encode($input['specification_description']);
        }else{
            $input['is_specification']    = 0;
            $input['specification_name'] = null;
            $input['specification_description'] = null;
        }

        if($request->has('license_name') && $request->has('license_key')){
            $input['license_name'] = json_encode($input['license_name']);
            $input['license_key'] = json_encode($input['license_key']);
        }else{
            $input['license_name'] = null;
            $input['license_key'] = null;
        }

        // digital product file upload
        if($request->item_type == 'digital'){
            if($request->hasFile('file')){
                $file = $request->file;
                $name = time().str_replace(' ', '', $file->getClientOriginalName());
                $file->move('assets/files',$name);
                $input['file'] = $name;
            }
        }

        if($request->item_type == 'license'){
            if($request->hasFile('file')){
                $file = $request->file;
                $name = time().str_replace(' ', '', $file->getClientOriginalName());
                $file->move('assets/files',$name);
                $input['file'] = $name;
            }
        }


        $input['is_type'] = 'undefine';
        $input['advance_payment_type'] = !empty($input['advance_payment_type']) ? $input['advance_payment_type'] : 'percentage';
        $input['advance_payment_amount'] = (isset($input['advance_payment_amount']) && $input['advance_payment_amount'] !== '' && $input['advance_payment_amount'] !== null) ? (float)$input['advance_payment_amount'] : 0.00;
        $input['is_free_delivery'] = !empty($input['is_free_delivery']) ? 1 : 0;
        $input['delivery_fee'] = (isset($input['delivery_fee']) && $input['delivery_fee'] !== '' && $input['delivery_fee'] !== null) ? (float)$input['delivery_fee'] : 0.00;
        $input['is_returnable'] = !empty($input['is_returnable']) ? 1 : 0;
        $input['return_days'] = (isset($input['return_days']) && $input['return_days'] !== '' && $input['return_days'] !== null) ? (int)$input['return_days'] : 14;
        $input['is_custom_rating'] = !empty($input['is_custom_rating']) ? 1 : 0;
        $input['custom_rating'] = (isset($input['custom_rating']) && $input['custom_rating'] !== '' && $input['custom_rating'] !== null) ? (float)$input['custom_rating'] : 5.00;
        $input['custom_rating_count'] = (isset($input['custom_rating_count']) && $input['custom_rating_count'] !== '' && $input['custom_rating_count'] !== null) ? (int)$input['custom_rating_count'] : 0;
        $input['vendor_id'] = $input['vendor_id'] ?? 0;
        $input['is_hidden_by_block'] = $input['is_hidden_by_block'] ?? 0;
        $input['approval_status'] = $input['approval_status'] ?? 'Approved';
        $input['stock'] = (isset($input['stock']) && $input['stock'] !== '' && $input['stock'] !== null) ? (int)$input['stock'] : 0;
        $input['estimated_profit'] = (isset($input['estimated_profit']) && $input['estimated_profit'] !== '' && $input['estimated_profit'] !== null) ? (float)$input['estimated_profit'] : 0.00;

        $item = Item::create($input);
        $item_id = $item->id;

        $this->handleVariants($item, $request);
        $this->handleRatingManagement($item, $request);
        $this->handleReturnPolicy($item, $request);

        if(isset($input['galleries'])){
            $this->galleriesUpdate($request,$item_id);
        }

        return $item_id;

    }

    /**
     * Update item.
     *
     * @param  \App\Http\Requests\ItemRequest  $request
     * @return void
     */

    public function update($item,$request)
    {
        self::ensureColumnsExist();
        $input = $request->all();

        if ( $request->file('photo')) {

            $images_name = ImageHelper::ItemhandleUpdatedUploadedImage($request->photo,'images',$item,'images','photo');
            $input['photo'] = $images_name[0];
            $input['thumbnail'] = $images_name[1];
        }


        if($request->has('meta_keywords')){
            $input['meta_keywords'] = str_replace(["value", "{", "}", "[","]",":","\""], '', $request->meta_keywords);
        }

        $curr = Currency::where('is_default',1)->first();
        $input['discount_price'] = $request->discount_price / $curr->value;
        $input['previous_price'] = $request->previous_price / $curr->value;

        if($request->has('is_social')){
            $input['social_icons'] = json_encode($input['social_icons']);
            $input['social_links'] = json_encode($input['social_links']);
        }else{
            $input['is_social']    = 0;
            $input['social_icons'] = null;
            $input['social_links'] = null;
        }

        if($request->has('tags')){
            $input['tags'] = str_replace(["value", "{", "}", "[","]",":","\""], '', $request->tags);
        }

        if($request->has('is_specification')){
            $input['specification_name'] = json_encode($input['specification_name']);
            $input['specification_description'] = json_encode($input['specification_description']);
        }else{
            $input['is_specification']    = 0;
            $input['specification_name'] = null;
            $input['specification_description'] = null;
        }

        if($request->has('license_name') && $request->has('license_key')){
            $input['license_name'] = json_encode($input['license_name']);
            $input['license_key'] = json_encode($input['license_key']);
        }else{
            $input['license_name'] = null;
            $input['license_key'] = null;
        }


        if($request->item_type == 'digital'){
            if(!$request->hasFile('file')){
                if($request->link){
                    if(file_exists('assets/files/'.$item->file)){
                        unlink('assets/files/'.$item->file);
                    }
                    $input['file'] = null;
                }
            }
        }
        // digital product file upload
        if($request->item_type == 'digital'){
            if($request->hasFile('file')){
                if($item->file){
                    if(file_exists('assets/files/'.$item->file)){
                        unlink('assets/files/'.$item->file);
                    }
                }

                $file = $request->file;
                $name = time().str_replace(' ', '', $file->getClientOriginalName());
                $file->move('assets/files',$name);
                $input['file'] = $name;
                $input['link'] = null;
            }
        }
        $input['advance_payment_type'] = !empty($input['advance_payment_type']) ? $input['advance_payment_type'] : 'percentage';
        $input['advance_payment_amount'] = (isset($input['advance_payment_amount']) && $input['advance_payment_amount'] !== '' && $input['advance_payment_amount'] !== null) ? (float)$input['advance_payment_amount'] : 0.00;
        $input['is_free_delivery'] = !empty($input['is_free_delivery']) ? 1 : 0;
        $input['delivery_fee'] = (isset($input['delivery_fee']) && $input['delivery_fee'] !== '' && $input['delivery_fee'] !== null) ? (float)$input['delivery_fee'] : 0.00;
        $input['is_returnable'] = !empty($input['is_returnable']) ? 1 : 0;
        $input['return_days'] = (isset($input['return_days']) && $input['return_days'] !== '' && $input['return_days'] !== null) ? (int)$input['return_days'] : 14;
        $input['is_custom_rating'] = !empty($input['is_custom_rating']) ? 1 : 0;
        $input['custom_rating'] = (isset($input['custom_rating']) && $input['custom_rating'] !== '' && $input['custom_rating'] !== null) ? (float)$input['custom_rating'] : 5.00;
        $input['custom_rating_count'] = (isset($input['custom_rating_count']) && $input['custom_rating_count'] !== '' && $input['custom_rating_count'] !== null) ? (int)$input['custom_rating_count'] : 0;
        if (isset($input['stock'])) {
            $input['stock'] = ($input['stock'] !== '' && $input['stock'] !== null) ? (int)$input['stock'] : 0;
        }
        if (isset($input['estimated_profit'])) {
            $input['estimated_profit'] = ($input['estimated_profit'] !== '' && $input['estimated_profit'] !== null) ? (float)$input['estimated_profit'] : 0.00;
        }

        $item->update($input);
        
        $this->handleVariants($item, $request);
        $this->handleRatingManagement($item, $request);
        $this->handleReturnPolicy($item, $request);

        if(isset($input['galleries'])){
            $this->galleriesUpdate($request,$item->id);
        }
    }

    public static function ensureColumnsExist()
    {
        try {
            if (Schema::hasTable('items')) {
                Schema::table('items', function (Blueprint $table) {
                    if (!Schema::hasColumn('items', 'estimated_profit')) {
                        $table->decimal('estimated_profit', 16, 2)->default(0.00)->after('video');
                    }
                    if (!Schema::hasColumn('items', 'item_variants')) {
                        $table->longText('item_variants')->nullable()->after('stock');
                    }
                    if (!Schema::hasColumn('items', 'is_custom_rating')) {
                        $table->tinyInteger('is_custom_rating')->default(0)->after('stock');
                    }
                    if (!Schema::hasColumn('items', 'custom_rating')) {
                        $table->decimal('custom_rating', 3, 2)->nullable()->default(5.00)->after('is_custom_rating');
                    }
                    if (!Schema::hasColumn('items', 'custom_rating_count')) {
                        $table->integer('custom_rating_count')->nullable()->default(0)->after('custom_rating');
                    }
                    if (!Schema::hasColumn('items', 'advance_payment_type')) {
                        $table->string('advance_payment_type')->default('percentage');
                    }
                    if (!Schema::hasColumn('items', 'advance_payment_amount')) {
                        $table->decimal('advance_payment_amount', 11, 2)->default(0);
                    }
                    if (!Schema::hasColumn('items', 'is_free_delivery')) {
                        $table->tinyInteger('is_free_delivery')->default(0);
                    }
                    if (!Schema::hasColumn('items', 'delivery_fee')) {
                        $table->decimal('delivery_fee', 11, 2)->default(0);
                    }
                    if (!Schema::hasColumn('items', 'is_returnable')) {
                        $table->tinyInteger('is_returnable')->default(0);
                    }
                    if (!Schema::hasColumn('items', 'return_days')) {
                        $table->integer('return_days')->default(14);
                    }
                });
            }
            if (Schema::hasTable('reviews')) {
                Schema::table('reviews', function (Blueprint $table) {
                    if (!Schema::hasColumn('reviews', 'customer_name')) {
                        $table->string('customer_name')->nullable()->after('user_id');
                    }
                    if (!Schema::hasColumn('reviews', 'is_admin_added')) {
                        $table->tinyInteger('is_admin_added')->default(0)->after('customer_name');
                    }
                });
            }
        } catch (\Exception $e) {
            // Ignore if columns already exist
        }
    }

    public function handleReturnPolicy($item, $request)
    {
        try {
            if (!Schema::hasColumn('items', 'is_returnable')) {
                Schema::table('items', function (Blueprint $table) {
                    $table->tinyInteger('is_returnable')->default(0);
                    $table->integer('return_days')->default(14);
                });
            }
        } catch (\Exception $e) {
            // Ignore
        }

        $item->is_returnable = ($request->has('is_returnable') && $request->is_returnable == '1') ? 1 : 0;
        $item->return_days = ($request->has('return_days') && $request->return_days !== null && $request->return_days !== '') ? (int)$request->return_days : 14;
        $item->save();
    }

    public function handleRatingManagement($item, $request)
    {
        try {
            if (!Schema::hasColumn('items', 'is_custom_rating')) {
                Schema::table('items', function (Blueprint $table) {
                    $table->tinyInteger('is_custom_rating')->default(0)->after('stock');
                    $table->decimal('custom_rating', 3, 2)->nullable()->default(5.00)->after('is_custom_rating');
                    $table->integer('custom_rating_count')->nullable()->default(0)->after('custom_rating');
                });
            }
            if (!Schema::hasColumn('reviews', 'customer_name')) {
                Schema::table('reviews', function (Blueprint $table) {
                    $table->string('customer_name')->nullable()->after('user_id');
                    $table->tinyInteger('is_admin_added')->default(0)->after('customer_name');
                });
            }
        } catch (\Exception $e) {
            // Ignore if columns already exist
        }

        $item->is_custom_rating = ($request->has('is_custom_rating') && $request->is_custom_rating == '1') ? 1 : 0;
        $item->custom_rating = ($request->has('custom_rating') && $request->custom_rating !== null && $request->custom_rating !== '') ? (float)$request->custom_rating : 5.00;
        $item->custom_rating_count = ($request->has('custom_rating_count') && $request->custom_rating_count !== null && $request->custom_rating_count !== '') ? (int)$request->custom_rating_count : 0;
        $item->save();

        // Handle Demo / Admin Reviews if submitted
        if ($request->has('demo_reviewer_name') && is_array($request->demo_reviewer_name)) {
            $rNames = $request->demo_reviewer_name;
            $rRatings = $request->demo_rating ?? [];
            $rSubjects = $request->demo_subject ?? [];
            $rReviews = $request->demo_review ?? [];

            for ($i = 0; $i < count($rNames); $i++) {
                $name = trim($rNames[$i] ?? '');
                $reviewText = trim($rReviews[$i] ?? '');
                $rating = isset($rRatings[$i]) ? (int)$rRatings[$i] : 5;
                $subject = trim($rSubjects[$i] ?? 'Verified Purchase');

                if ($name !== '' && $reviewText !== '') {
                    Review::create([
                        'user_id' => 0,
                        'item_id' => $item->id,
                        'customer_name' => $name,
                        'is_admin_added' => 1,
                        'rating' => max(1, min(5, $rating)),
                        'subject' => !empty($subject) ? $subject : 'Product Review',
                        'review' => $reviewText,
                        'status' => 1
                    ]);
                }
            }
        }
    }

    public function handleVariants($item, $request)
    {
        try {
            if (!Schema::hasColumn('items', 'item_variants')) {
                Schema::table('items', function (Blueprint $table) {
                    $table->longText('item_variants')->nullable()->after('stock');
                });
            }
        } catch (\Exception $e) {
            // Ignore if column creation fails or already exists
        }

        if ($request->has('is_variant') && $request->is_variant == '1' && $request->has('variant_stock')) {
            $colors = $request->variant_color ?? [];
            $sizes = $request->variant_size ?? [];
            $stocks = $request->variant_stock ?? [];
            $prices = $request->variant_price ?? [];

            $variantsData = [];
            $totalStock = 0;

            for ($i = 0; $i < count($stocks); $i++) {
                $c = isset($colors[$i]) ? trim($colors[$i]) : '';
                $s = isset($sizes[$i]) ? trim($sizes[$i]) : '';
                $st = isset($stocks[$i]) ? (int)$stocks[$i] : 0;
                $pr = isset($prices[$i]) ? (float)$prices[$i] : 0;

                if ($c !== '' || $s !== '') {
                    $variantsData[] = [
                        'color' => $c,
                        'size' => $s,
                        'stock' => $st,
                        'price' => $pr
                    ];
                    $totalStock += $st;
                }
            }

            if (!empty($variantsData)) {
                $item->item_variants = json_encode($variantsData);
                $item->stock = $totalStock;
                $item->save();

                $this->syncAttributesFromVariants($item, $variantsData);
            } else {
                $item->item_variants = null;
                $item->save();
            }
        } else {
            $item->item_variants = null;
            $item->save();
        }
    }

    public function syncAttributesFromVariants($item, $variantsData)
    {
        $hasColor = false;
        $hasSize = false;

        $colorStocks = [];
        $sizeStocks = [];
        $colorPrices = [];
        $sizePrices = [];

        foreach ($variantsData as $v) {
            $c = $v['color'] ?? '';
            $s = $v['size'] ?? '';
            $st = (int)($v['stock'] ?? 0);
            $pr = (float)($v['price'] ?? 0);

            if ($c !== '') {
                $hasColor = true;
                $colorStocks[$c] = ($colorStocks[$c] ?? 0) + $st;
                $colorPrices[$c] = $pr;
            }
            if ($s !== '') {
                $hasSize = true;
                $sizeStocks[$s] = ($sizeStocks[$s] ?? 0) + $st;
                $sizePrices[$s] = $pr;
            }
        }

        // 1. Sync Color Attribute
        if ($hasColor) {
            $colorAttr = \App\Models\Attribute::firstOrCreate(
                ['item_id' => $item->id, 'name' => 'Color'],
                ['keyword' => 'color']
            );
            $colorAttr->options()->whereNotIn('name', array_keys($colorStocks))->delete();
            foreach ($colorStocks as $cName => $cStock) {
                $curr = Currency::where('is_default', 1)->first();
                $convertedPrice = $curr && $curr->value > 0 ? ($colorPrices[$cName] / $curr->value) : $colorPrices[$cName];
                \App\Models\AttributeOption::updateOrCreate(
                    ['attribute_id' => $colorAttr->id, 'name' => $cName],
                    ['keyword' => \Illuminate\Support\Str::slug($cName), 'stock' => (string)$cStock, 'price' => $convertedPrice]
                );
            }
        } else {
            $colorAttr = \App\Models\Attribute::where('item_id', $item->id)->where('name', 'Color')->first();
            if ($colorAttr) {
                $colorAttr->options()->delete();
                $colorAttr->delete();
            }
        }

        // 2. Sync Size Attribute
        if ($hasSize) {
            $sizeAttr = \App\Models\Attribute::firstOrCreate(
                ['item_id' => $item->id, 'name' => 'Size'],
                ['keyword' => 'size']
            );
            $sizeAttr->options()->whereNotIn('name', array_keys($sizeStocks))->delete();
            foreach ($sizeStocks as $sName => $sStock) {
                $curr = Currency::where('is_default', 1)->first();
                $convertedPrice = $curr && $curr->value > 0 ? ($sizePrices[$sName] / $curr->value) : $sizePrices[$sName];
                \App\Models\AttributeOption::updateOrCreate(
                    ['attribute_id' => $sizeAttr->id, 'name' => $sName],
                    ['keyword' => \Illuminate\Support\Str::slug($sName), 'stock' => (string)$sStock, 'price' => $convertedPrice]
                );
            }
        } else {
            $sizeAttr = \App\Models\Attribute::where('item_id', $item->id)->where('name', 'Size')->first();
            if ($sizeAttr) {
                $sizeAttr->options()->delete();
                $sizeAttr->delete();
            }
        }
    }

    public function highlight($item,$request)
    {
        $input = $request->all();
        if($request->is_type != 'flash_deal'){
            $input['date'] = null;
        }
        $item->update($input);
    }

    /**
     * Delete item.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function delete($item)
    {
        if($item->galleries()->count() > 0){
            foreach($item->galleries as $gallery){
                $this->galleryDelete($gallery);
            }
        }

        if($item->campaigns->count() > 0){
            $item->campaigns()->delete();
        }
        if($item->reviews->count() > 0){
            $item->reviews()->delete();
        }

        if($item->attributes()->count() > 0){
            foreach($item->attributes as $attribute){
                $attribute->options()->delete();
            }
            $item->attributes()->delete();
        }

        ImageHelper::handleDeletedImage($item,'photo','images');
        ImageHelper::handleDeletedImage($item,'thumbnail','images');
        if($item->item_type == 'digital' && $item->file){
            ImageHelper::handleDeletedImage($item,'file','images');
        }
        $item->delete();
    }

    /**
     * Update gallery.
     *
     * @param  \App\Http\Requests\GalleryRequest  $request
     * @return void
     */

    public function galleriesUpdate($request,$item_id=null)
    {
        Gallery::insert($this->storeImageData($request,$item_id));
    }

    /**
     * Delete gallery.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function galleryDelete($gallery)
    {
        ImageHelper::handleDeletedImage($gallery,'photo','images');
        $gallery->delete();
    }

    /**
     * Custom Function.
     * @return void
     */

    public function storeImageData($request,$item_id=null)
    {
        $storeData = [];
        if ($galleries = $request->file('galleries')) {
            foreach($galleries as $key => $gallery){
                $storeData[$key] = [
                    'photo'=>  ImageHelper::handleUploadedImage($gallery,'images'),
                    'item_id' => $item_id ? $item_id : $request['item_id'],
                ];
            }
        }
        return $storeData;
    }

}
