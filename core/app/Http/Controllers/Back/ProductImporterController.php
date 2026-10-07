<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Gallery;
use App\Models\Item;
use App\Models\Subcategory;
use App\Models\ChieldCategory;
use App\Repositories\Back\ItemRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImporterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
    }

    /**
     * Display the 1-Click Importer view.
     */
    public function index()
    {
        $categories = Category::where('status', 1)->get();
        $curr = Currency::where('is_default', 1)->first();

        return view('back.item.importer', compact('categories', 'curr'));
    }

    /**
     * Fetch product details and images from given URL.
     */
    public function fetch(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        $url = trim($request->url);

        try {
            $productData = $this->scrapeUrl($url);

            if (empty($productData['name'])) {
                return response()->json([
                    'success' => false,
                    'message' => __('Could not automatically extract product title. Please check the URL or enter details manually.')
                ], 422);
            }

            // Auto-match best category
            $categories = Category::where('status', 1)->get();
            $matchedCategoryId = $this->matchCategory($productData['name'], $categories);
            $productData['category_id'] = $matchedCategoryId;

            return response()->json([
                'success' => true,
                'data' => $productData,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error fetching product from URL: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save the imported product into database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'discount_price' => 'required|numeric|min:0.1',
            'category_id' => 'required|integer',
        ]);

        $curr = Currency::where('is_default', 1)->first();
        $currValue = $curr ? $curr->value : 1;

        // 1. Download & store main image
        $mainPhotoName = null;
        $thumbnailName = null;

        if ($request->hasFile('photo')) {
            $images_name = \App\Helpers\ImageHelper::ItemhandleUploadedImage($request->file('photo'), 'images');
            $mainPhotoName = $images_name[0];
            $thumbnailName = $images_name[1];
        } elseif (!empty($request->main_image_url)) {
            $downloaded = $this->downloadAndSaveImage($request->main_image_url);
            if ($downloaded) {
                $mainPhotoName = $downloaded['photo'];
                $thumbnailName = $downloaded['thumbnail'];
            }
        }

        // Fallback default image if none found
        if (!$mainPhotoName) {
            $mainPhotoName = 'default.jpg';
            $thumbnailName = 'default.jpg';
        }

        // 2. Generate unique slug
        $baseSlug = Str::slug($request->name);
        if (empty($baseSlug)) {
            $baseSlug = 'product-' . time();
        }
        $slug = $baseSlug;
        $counter = 1;
        while (Item::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        // 3. Generate SKU
        $sku = $request->sku ?: ItemRepository::generateAutoSku();

        // 4. Create Product
        $item = new Item();
        $item->name = $request->name;
        $item->slug = $slug;
        $item->sku = $sku;
        $item->item_type = 'normal';
        $item->category_id = $request->category_id;
        $item->subcategory_id = $request->subcategory_id ?: null;
        $item->childcategory_id = $request->childcategory_id ?: null;
        $item->brand_id = $request->brand_id ?: null;
        $item->tax_id = $request->tax_id ?: 0;

        $item->photo = $mainPhotoName;
        $item->thumbnail = $thumbnailName;

        $item->discount_price = $request->discount_price / $currValue;
        $item->previous_price = !empty($request->previous_price) ? ($request->previous_price / $currValue) : 0;

        $item->stock = $request->stock ?: 20;
        $item->sort_details = $request->sort_details ?: Str::limit(strip_tags($request->details), 200);
        $item->details = $request->details ?: $request->name;
        $item->tags = $request->tags ?: null;

        $item->is_cod = $request->input('is_cod', 1);
        $item->is_free_delivery = $request->input('is_free_delivery', 0);
        $item->delivery_fee = $request->input('delivery_fee', 0);

        $item->advance_payment_type = $request->input('advance_payment_type', 'percentage');
        $item->advance_payment_amount = $request->input('advance_payment_amount', 0);

        $item->is_returnable = $request->input('is_returnable', 1);
        $item->return_days = $request->input('return_days', 14);

        $item->is_custom_rating = $request->input('is_custom_rating', 1);
        $item->custom_rating = $request->input('custom_rating', '4.9');
        $item->custom_rating_count = $request->input('custom_rating_count', 32);

        $item->estimated_profit = $request->input('estimated_profit', 0);
        $item->product_from = $request->input('product_from', 'HHC Dropshipping');
        $item->contact_number = $request->input('contact_number', null);
        $item->video = $request->video ?: null;

        $item->meta_keywords = $request->meta_keywords ?: null;
        $item->meta_description = $request->meta_description ?: Str::limit(strip_tags($request->details), 160);

        $item->status = 1;
        $item->vendor_id = null; // Admin In-House Product
        $item->is_specification = 0;

        $item->save();

        // 5. Download & attach gallery images
        if ($request->has('gallery_urls') && is_array($request->gallery_urls)) {
            foreach ($request->gallery_urls as $gUrl) {
                if (!empty($gUrl) && $gUrl !== $request->main_image_url) {
                    $savedGallery = $this->downloadAndSaveImage($gUrl);
                    if ($savedGallery) {
                        Gallery::create([
                            'item_id' => $item->id,
                            'photo' => $savedGallery['photo'],
                        ]);
                    }
                }
            }
        }

        // Check if user clicked "Save & Edit"
        if ($request->input('is_button') == 1) {
            return redirect()->route('back.item.edit', $item->id)->withSuccess(__('Product Imported & Created Successfully! You can now fine-tune details.'));
        }

        return redirect()->route('back.item.index')->withSuccess(__('Product Imported & Published Successfully!'));
    }

    /**
     * Scrape HTML and metadata from remote URL.
     */
    private function scrapeUrl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.9,ur;q=0.8',
            'Cache-Control: no-cache',
        ]);

        $html = curl_exec($ch);
        $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL) ?: $url;
        curl_close($ch);

        if (!$html) {
            throw new \Exception(__('Unable to connect or download page content from the given link.'));
        }

        $domain = parse_url($effectiveUrl, PHP_URL_HOST);
        $supplierName = 'HHC Dropshipping';
        if ($domain) {
            if (stripos($domain, 'hhcdropshipping') !== false) {
                $supplierName = 'HHC Dropshipping';
            } elseif (stripos($domain, 'daraz') !== false) {
                $supplierName = 'Daraz.pk';
            } elseif (stripos($domain, 'aliexpress') !== false) {
                $supplierName = 'AliExpress';
            } else {
                $supplierName = ucfirst(str_ireplace(['www.', '.com', '.pk', '.store', '.shop'], '', $domain));
            }
        }

        $title = '';
        $description = '';
        $price = '';
        $images = [];

        // 1. Check JSON-LD schema
        if (preg_match_all('/<script[^>]*type=[\'"]application\/ld\+json[\'"][^>]*>(.*?)<\/script>/is', $html, $matches)) {
            foreach ($matches[1] as $jsonString) {
                $data = json_decode(trim($jsonString), true);
                if ($data) {
                    if (isset($data['@graph']) && is_array($data['@graph'])) {
                        foreach ($data['@graph'] as $gItem) {
                            if (isset($gItem['@type']) && (strtolower($gItem['@type']) === 'product' || strtolower($gItem['@type']) === 'itempage')) {
                                $data = $gItem;
                                break;
                            }
                        }
                    }

                    if (isset($data['@type']) && (strtolower($data['@type']) === 'product' || strtolower($data['@type']) === 'itempage')) {
                        if (!empty($data['name']) && empty($title)) {
                            $title = html_entity_decode($data['name']);
                        }
                        if (!empty($data['description']) && empty($description)) {
                            $description = html_entity_decode($data['description']);
                        }
                        if (!empty($data['image'])) {
                            if (is_array($data['image'])) {
                                foreach ($data['image'] as $img) {
                                    $imgUrl = is_array($img) ? ($img['url'] ?? '') : $img;
                                    if ($imgUrl) $images[] = $this->resolveAbsoluteUrl($imgUrl, $effectiveUrl);
                                }
                            } else {
                                $images[] = $this->resolveAbsoluteUrl($data['image'], $effectiveUrl);
                            }
                        }
                        if (!empty($data['offers'])) {
                            $offers = is_array($data['offers']) ? (isset($data['offers'][0]) ? $data['offers'][0] : $data['offers']) : [];
                            if (!empty($offers['price'])) {
                                $price = preg_replace('/[^0-9.]/', '', (string) $offers['price']);
                            }
                        }
                    }
                }
            }
        }

        // 2. OpenGraph Fallbacks
        if (empty($title) && preg_match('/<meta[^>]*property=[\'"]og:title[\'"][^>]*content=[\'"](.*?)[\'"]/i', $html, $m)) {
            $title = html_entity_decode($m[1]);
        }
        if (empty($description) && preg_match('/<meta[^>]*property=[\'"]og:description[\'"][^>]*content=[\'"](.*?)[\'"]/i', $html, $m)) {
            $description = html_entity_decode($m[1]);
        }
        if (empty($description) && preg_match('/<meta[^>]*name=[\'"]description[\'"][^>]*content=[\'"](.*?)[\'"]/i', $html, $m)) {
            $description = html_entity_decode($m[1]);
        }

        if (preg_match_all('/<meta[^>]*property=[\'"]og:image[\'"][^>]*content=[\'"](.*?)[\'"]/i', $html, $ogImgs)) {
            foreach ($ogImgs[1] as $imgUrl) {
                $images[] = $this->resolveAbsoluteUrl($imgUrl, $effectiveUrl);
            }
        }

        // 3. HTML Title & Heading Fallbacks
        if (empty($title) && preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $html, $m)) {
            $title = trim(strip_tags($m[1]));
        }
        if (empty($title) && preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $m)) {
            $title = trim(strip_tags($m[1]));
            // Clean up store names from title
            $title = preg_replace('/(\s*[-|–]\s*.*)$/', '', $title);
        }

        // 4. Extract price from meta / regex if not found
        if (empty($price)) {
            if (preg_match('/<meta[^>]*property=[\'"](?:og:price:amount|product:price:amount)[\'"][^>]*content=[\'"](.*?)[\'"]/i', $html, $m)) {
                $price = preg_replace('/[^0-9.]/', '', $m[1]);
            }
        }

        // 5. Scrape additional product images from DOM
        if (preg_match_all('/<img[^>]*(?:data-src|data-zoom|data-large|src)=[\'"]([^\'"]+\.(?:jpe?g|png|webp|avif)[^\'"]*)[\'"]/i', $html, $domImgs)) {
            foreach ($domImgs[1] as $domImg) {
                // Ignore small icons, avatars, badges
                if (stripos($domImg, 'logo') === false && stripos($domImg, 'icon') === false && stripos($domImg, 'avatar') === false && stripos($domImg, 'banner') === false) {
                    $images[] = $this->resolveAbsoluteUrl($domImg, $effectiveUrl);
                }
            }
        }

        // Deduplicate & clean images list
        $images = array_values(array_unique(array_filter($images)));

        // Clean description
        if (empty($description)) {
            $description = $title;
        }

        return [
            'name' => trim($title),
            'details' => trim($description),
            'sort_details' => Str::limit(strip_tags($description), 220),
            'raw_price' => $price ? floatval($price) : null,
            'images' => array_slice($images, 0, 8),
            'product_from' => $supplierName,
            'source_url' => $effectiveUrl,
        ];
    }

    /**
     * Download an external image from URL and save to storage/images.
     */
    private function downloadAndSaveImage($imageUrl)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $imageUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36');
            $imgData = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode >= 200 && $httpCode < 300 && !empty($imgData)) {
                $ext = 'jpg';
                $pathInfo = pathinfo(parse_url($imageUrl, PHP_URL_PATH));
                if (!empty($pathInfo['extension'])) {
                    $cleanExt = strtolower(explode('?', $pathInfo['extension'])[0]);
                    if (in_array($cleanExt, ['jpg', 'jpeg', 'png', 'webp', 'avif'])) {
                        $ext = $cleanExt;
                    }
                }

                $photoName = 'OM_' . time() . '_' . Str::random(8) . '.' . $ext;
                $thumbnailName = 'OM_thumb_' . time() . '_' . Str::random(8) . '.' . $ext;

                Storage::put('images/' . $photoName, $imgData);
                Storage::put('images/' . $thumbnailName, $imgData);

                return [
                    'photo' => $photoName,
                    'thumbnail' => $thumbnailName,
                ];
            }
        } catch (\Throwable $e) {
            // Silence image download error
        }

        return null;
    }

    /**
     * Auto-match the closest Category from DB based on title tokens.
     */
    private function matchCategory($title, $categories)
    {
        if ($categories->isEmpty()) {
            return 1;
        }

        $titleLower = strtolower($title);
        $bestCategoryId = $categories->first()->id;
        $highestScore = 0;

        foreach ($categories as $cat) {
            $catName = strtolower($cat->name);
            $catSlug = strtolower(str_replace('-', ' ', $cat->slug));
            $score = 0;

            // Direct substring match
            if (strpos($titleLower, $catName) !== false) {
                $score += 10;
            }

            // Word token match
            $words = explode(' ', $catName . ' ' . $catSlug);
            foreach ($words as $w) {
                $w = trim($w);
                if (strlen($w) > 3 && strpos($titleLower, $w) !== false) {
                    $score += 3;
                }
            }

            if ($score > $highestScore) {
                $highestScore = $score;
                $bestCategoryId = $cat->id;
            }
        }

        return $bestCategoryId;
    }

    /**
     * Resolve relative image URL to absolute URL.
     */
    private function resolveAbsoluteUrl($url, $baseUrl)
    {
        $url = trim($url);
        if (empty($url)) return '';

        if (preg_match('/^https?:\/\//i', $url)) {
            return $url;
        }

        if (substr($url, 0, 2) === '//') {
            return 'https:' . $url;
        }

        $parts = parse_url($baseUrl);
        $scheme = $parts['scheme'] ?? 'https';
        $host = $parts['host'] ?? '';

        if (substr($url, 0, 1) === '/') {
            return $scheme . '://' . $host . $url;
        }

        $path = $parts['path'] ?? '/';
        $dir = dirname($path);
        return $scheme . '://' . $host . ($dir === '/' ? '' : $dir) . '/' . $url;
    }
}
