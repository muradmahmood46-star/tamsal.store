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
     * Smart Parser for Raw Pasted Text / HHC Copy-Paste.
     */
    public function parseText(Request $request)
    {
        $rawText = trim($request->input('raw_text', ''));
        $inputTitle = trim($request->input('input_title', ''));
        $inputSortDetails = trim($request->input('input_sort_details', ''));
        $inputDetails = trim($request->input('input_details', ''));
        
        $mainImageUrl = trim($request->input('main_image_url', ''));
        $galleryUrls = $request->input('gallery_urls', []);
        if (is_string($galleryUrls)) {
            $galleryUrls = array_filter(array_map('trim', preg_split('/[\r\n,]+/', $galleryUrls)));
        }

        $explicitImages = [];
        if (!empty($mainImageUrl)) {
            $explicitImages[] = $mainImageUrl;
        }
        if (is_array($galleryUrls)) {
            foreach ($galleryUrls as $gUrl) {
                $gUrl = trim($gUrl);
                if (!empty($gUrl) && !in_array($gUrl, $explicitImages)) {
                    $explicitImages[] = $gUrl;
                }
            }
        }

        $imageUrlsInput = trim($request->input('image_urls', ''));
        if (!empty($imageUrlsInput)) {
            $extraUrls = array_filter(array_map('trim', preg_split('/[\r\n,]+/', $imageUrlsInput)));
            foreach ($extraUrls as $eUrl) {
                if (!empty($eUrl) && !in_array($eUrl, $explicitImages)) {
                    $explicitImages[] = $eUrl;
                }
            }
        }

        $combinedSource = ($inputTitle ? ("Product Title: " . $inputTitle . "\n") : '') . 
                          ($inputSortDetails ? ("Short Description: " . $inputSortDetails . "\n") : '') . 
                          ($inputDetails ? ("Description: " . $inputDetails . "\n") : '') . 
                          $rawText;

        $data = $this->extractDataFromText($combinedSource, implode("\n", $explicitImages));

        // Overwrite with explicitly provided title/descriptions if available
        if (!empty($inputTitle)) {
            $data['name'] = $inputTitle;
        }
        if (!empty($inputSortDetails)) {
            $data['sort_details'] = $inputSortDetails;
        }
        if (!empty($inputDetails)) {
            $data['details'] = $inputDetails;
        }

        // If explicit main or gallery images were provided, ensure they are placed first in exact order
        if (!empty($explicitImages)) {
            $existingImages = $data['images'] ?? [];
            $mergedImages = $explicitImages;
            foreach ($existingImages as $img) {
                if (!in_array($img, $mergedImages)) {
                    $mergedImages[] = $img;
                }
            }
            $data['images'] = array_slice($mergedImages, 0, 10);
        }

        // Auto-match best category
        $categories = Category::where('status', 1)->get();
        $matchedCategoryId = $this->matchCategory($data['name'] . ' ' . ($data['category_hint'] ?? ''), $categories);
        $data['category_id'] = $matchedCategoryId;
        $matchedCat = $categories->where('id', $matchedCategoryId)->first();
        $catName = $matchedCat ? $matchedCat->name : '';

        // Auto-match Brand (if any brand matches the title)
        $data['brand_id'] = $this->matchBrand($data['name']);

        // Supplier URL
        $inputSupplierUrl = trim($request->input('supplier_url', $request->input('input_supplier_url', '')));
        if (empty($inputSupplierUrl)) {
            if (preg_match('/https?:\/\/(?!.*\.(?:jpg|jpeg|png|webp|gif|svg))(?:[^\s<>"\']+)/i', $rawText . ' ' . $inputDetails, $urlMatch)) {
                $inputSupplierUrl = $urlMatch[0];
            }
        }
        $data['supplier_url'] = $inputSupplierUrl;

        // Auto-generate high-ranking SEO Meta Keywords & Meta Description (Product Tags are manual only in preview form)
        $seo = $this->generateSeoAndTags($data['name'], $data['details'] ?: $data['sort_details'], $catName);
        $data['tags'] = ''; // Manual entry only in preview form
        $data['meta_keywords'] = $seo['meta_keywords'];
        $data['meta_description'] = $seo['meta_description'];

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
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

        // Check if user pasted a login-protected member URL
        if (stripos($url, 'member.hhcdropshipping.com') !== false) {
            return response()->json([
                'success' => false,
                'is_hhc_member' => true,
                'message' => __('Yeh HHC Member Portal ka login wala link hai jo private hota hai. Neeche "Smart Text / HHC Copy-Paste" tab par click karein aur HHC page ka text wahan paste karein.')
            ], 422);
        }

        try {
            $productData = $this->scrapeUrl($url);

            if (empty($productData['name']) || stripos($productData['name'], 'login') !== false) {
                return response()->json([
                    'success' => false,
                    'message' => __('Could not automatically extract product title. This page might require login. Please use the "Smart Text Copy-Paste" tab.')
                ], 422);
            }

            // Auto-match best category
            $categories = Category::where('status', 1)->get();
            $matchedCategoryId = $this->matchCategory($productData['name'], $categories);
            $productData['category_id'] = $matchedCategoryId;
            $matchedCat = $categories->where('id', $matchedCategoryId)->first();
            $catName = $matchedCat ? $matchedCat->name : '';

            // Auto-match Brand (if any brand matches the title)
            $productData['brand_id'] = $this->matchBrand($productData['name']);

            // Supplier URL
            $productData['supplier_url'] = $url;

            // Auto-generate high-ranking SEO Meta Keywords & Meta Description (Product Tags manual only)
            $seo = $this->generateSeoAndTags($productData['name'], $productData['details'] ?: $productData['sort_details'], $catName);
            $productData['tags'] = ''; // Manual entry only
            $productData['meta_keywords'] = $seo['meta_keywords'];
            $productData['meta_description'] = $seo['meta_description'];

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

        // Tags & Meta sanitization (Supports Tagify JSON and plain comma-separated tags)
        if ($request->filled('tags')) {
            $item->tags = str_replace(["value", "{", "}", "[","]",":","\""], '', $request->tags);
        } else {
            $item->tags = null;
        }

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
        $item->supplier_url = $request->filled('supplier_url') ? trim($request->supplier_url) : null;
        $item->video = $request->video ?: null;

        if ($request->filled('meta_keywords')) {
            $item->meta_keywords = str_replace(["value", "{", "}", "[","]",":","\""], '', $request->meta_keywords);
        } else {
            $item->meta_keywords = null;
        }

        $item->meta_description = $request->filled('meta_description') 
            ? trim(strip_tags($request->meta_description)) 
            : Str::limit(strip_tags($request->details ?: $request->name), 155, '...');

        $item->status = 1;
        $item->vendor_id = null; // Admin In-House Product

        // Specifications
        if ($request->has('is_specification') && $request->is_specification == 1) {
            $item->is_specification = 1;
            $item->specification_name = json_encode($request->specification_name ?? []);
            $item->specification_description = json_encode($request->specification_description ?? []);
        } else {
            $item->is_specification = 0;
            $item->specification_name = null;
            $item->specification_description = null;
        }

        $item->save();

        // Handle Variants, Demo Reviews & Rating, and Return Policy via ItemRepository
        $itemRepo = new ItemRepository();
        $itemRepo->handleVariants($item, $request);
        $itemRepo->handleRatingManagement($item, $request);
        $itemRepo->handleReturnPolicy($item, $request);

        // 5. Download & attach gallery images (from URLs)
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

        // 6. Attach uploaded gallery files
        if ($request->hasFile('galleries')) {
            foreach ($request->file('galleries') as $gFile) {
                $gName = \App\Helpers\ImageHelper::handleUploadedImage($gFile, 'images');
                if ($gName) {
                    Gallery::create([
                        'item_id' => $item->id,
                        'photo' => $gName,
                    ]);
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
     * Smart Text Extraction for HHC & Supplier Copy-Pastes.
     */
    private function extractDataFromText($text, $imageUrlsInput = '')
    {
        $lines = preg_split('/\r\n|\r|\n/', $text);
        $lines = array_values(array_filter(array_map('trim', $lines)));

        $title = '';
        $price = '';
        $sku = '';
        $stock = 20;
        $categoryHint = '';
        $description = '';
        $images = [];

        // 1. Find Title (check explicit 'Product Title: ...' first or pick top descriptive line)
        if (preg_match('/(?:Product\s*(?:Title|Name)|Item\s*Name|Title|Name)\s*[:=]\s*([^\r\n]+)/i', $text, $m)) {
            $candidate = trim(strip_tags($m[1]));
            if (strlen($candidate) > 3) {
                $title = $candidate;
            }
        }

        if (empty($title)) {
            $noisePattern = '/^(?:login|dashboard|billing|shipment|courier|rider|clear cart|payment|advance|cash on delivery|confirm|business profiles|total|sub total|weight|rs\b|pkr\b|price|https?:\/\/|product info|description|stock|quantity|reviews|categories|contact|pickup|cutoff|aaj\s+nahi)/i';
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (strlen($trimmed) >= 4 && strlen($trimmed) <= 250 && !preg_match($noisePattern, $trimmed) && preg_match('/[a-zA-Z]/', $trimmed)) {
                    $title = $trimmed;
                    break;
                }
            }
        }

        // 2. Find Price (e.g. Rs 330, PKR 330, 330 x 1, Price: Rs. 330)
        if (preg_match('/(?:Price|Cost|Wholesale)\s*[:=]?\s*(?:rs\.?|pkr)?\s*([0-9,]+(?:\.[0-9]+)?)/i', $text, $m)) {
            $price = floatval(str_replace(',', '', $m[1]));
        } elseif (preg_match('/(?:rs\.?|pkr)\s*([0-9,]+(?:\.[0-9]+)?)/i', $text, $m)) {
            $price = floatval(str_replace(',', '', $m[1]));
        } elseif (preg_match('/([0-9,]+(?:\.[0-9]+)?)\s*x\s*[0-9]+/i', $text, $m)) {
            $price = floatval(str_replace(',', '', $m[1]));
        }

        // 3. Find Product ID / SKU
        if (preg_match('/(?:Product\s*ID|SKU|Item\s*Code|ID)\s*[:=]?\s*([0-9a-zA-Z_-]+)/i', $text, $m)) {
            $cleanId = trim($m[1]);
            $sku = (stripos($cleanId, 'TS') === 0) ? $cleanId : ('TS' . $cleanId);
        } else {
            $sku = ItemRepository::generateAutoSku();
        }

        // 4. Find Stock Quantity
        if (preg_match('/(?:Quantity|Stock|Available|Qty)\s*[:=]?\s*([0-9]+)/i', $text, $m)) {
            $stock = intval($m[1]);
        }

        // 5. Find Category Hint
        if (preg_match('/(?:Categories|Category)\s*[:=]?\s*(.*?)(?:\n|Description|Stock|Q & A|$)/is', $text, $m)) {
            $categoryHint = trim(strip_tags($m[1]));
        }

        // 6. Find Description
        if (preg_match('/(?:Description|Details|Specifications|Product Details|Overview)\s*[:=]?\s*([\s\S]*?)(?:Q & A|Product Reviews|Stock Status|Customer Reviews|Related Products|Stock\b|$)/i', $text, $m)) {
            $descCandidate = trim($m[1]);
            // Remove common header words
            $descCandidate = preg_replace('/^(?:Q & A|Stock|Product Reviews|Description)\s*/i', '', $descCandidate);
            if (strlen($descCandidate) > 5) {
                $description = $descCandidate;
            }
        }

        if (empty($description)) {
            // Fallback: collect lines after title or general lines
            $descLines = [];
            $foundTitle = false;
            foreach ($lines as $line) {
                if ($line === $title) {
                    $foundTitle = true;
                    continue;
                }
                if ($foundTitle && strlen($line) > 5 && !preg_match('/^(?:rs|pkr|price|sub total|total|weight|cutoff|pickup)/i', $line)) {
                    $descLines[] = $line;
                }
            }
            if (!empty($descLines)) {
                $description = implode("\n", array_slice($descLines, 0, 10));
            } else {
                $description = $title;
            }
        }

        // 7. Extract Image URLs from text or direct input
        $combinedText = $text . "\n" . $imageUrlsInput;
        if (preg_match_all('/https?:\/\/[^\s"\'<>]+\.(?:jpe?g|png|webp|avif)(?:\?[^\s"\'<>]*)?/i', $combinedText, $imgMatches)) {
            $images = array_values(array_unique($imgMatches[0]));
        }

        // Also check CDN image patterns without standard extension
        if (empty($images)) {
            if (preg_match_all('/https?:\/\/[^\s"\'<>]*(?:cloudinary|amazonaws|hhcdropshipping|cdn)[^\s"\'<>]+/i', $combinedText, $cdnMatches)) {
                $images = array_values(array_unique($cdnMatches[0]));
            }
        }

        return [
            'name' => $title,
            'details' => $description,
            'sort_details' => Str::limit(strip_tags($description), 220),
            'raw_price' => $price ?: null,
            'sku' => $sku,
            'stock' => $stock > 0 ? $stock : 20,
            'category_hint' => $categoryHint,
            'images' => array_slice($images, 0, 8),
            'product_from' => 'HHC Dropshipping',
        ];
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
            $title = preg_replace('/(\s*[-|–]\s*.*)$/', '', $title);
        }

        // 4. Extract price
        if (empty($price)) {
            if (preg_match('/<meta[^>]*property=[\'"](?:og:price:amount|product:price:amount)[\'"][^>]*content=[\'"](.*?)[\'"]/i', $html, $m)) {
                $price = preg_replace('/[^0-9.]/', '', $m[1]);
            }
        }

        // 5. Scrape additional product images from DOM
        if (preg_match_all('/<img[^>]*(?:data-src|data-zoom|data-large|src)=[\'"]([^\'"]+\.(?:jpe?g|png|webp|avif)[^\'"]*)[\'"]/i', $html, $domImgs)) {
            foreach ($domImgs[1] as $domImg) {
                if (stripos($domImg, 'logo') === false && stripos($domImg, 'icon') === false && stripos($domImg, 'avatar') === false && stripos($domImg, 'banner') === false) {
                    $images[] = $this->resolveAbsoluteUrl($domImg, $effectiveUrl);
                }
            }
        }

        // Deduplicate & clean images list
        $images = array_values(array_unique(array_filter($images)));

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
     * Match Brand based on Product Name.
     */
    private function matchBrand($productName)
    {
        if (empty($productName)) {
            return null;
        }
        $brands = DB::table('brands')->whereStatus(1)->where(function($q){ 
            $q->whereNull('vendor_id')->orWhere('vendor_id', 0); 
        })->get();

        $productLower = ' ' . strtolower($productName) . ' ';
        foreach ($brands as $brand) {
            $bName = strtolower(trim($brand->name));
            if (strlen($bName) >= 2 && strpos($productLower, ' ' . $bName . ' ') !== false) {
                return $brand->id;
            }
        }
        return null;
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

    /**
     * Generate SEO Meta Keywords, Meta Description, and Product Tags from Title and Content.
     * Generates max 5 truly relatable Product Tags and max 5 Meta Keywords.
     */
    private function generateSeoAndTags($title, $description, $categoryName = '')
    {
        $rawTitle = strip_tags($title);
        $cleanTitle = trim(preg_replace('/[^\p{L}\p{N}\s-]/u', ' ', $rawTitle));
        $cleanTitle = preg_replace('/\s+/', ' ', $cleanTitle);
        
        $stopWords = [
            'for', 'the', 'and', 'with', 'in', 'of', 'to', 'a', 'an', 'is', 'on', 'at', 'by', 
            'from', 'this', 'that', 'it', 'are', 'was', 'will', 'or', 'be', 'as', 'but', 'not', 
            'all', 'any', 'can', 'had', 'has', 'have', 'each', 'few', 'more', 'most', 'other', 
            'some', 'such', 'no', 'nor', 'too', 'very', 'only', 'own', 'same', 'so', 'than', 
            'just', 'should', 'now', 'pk', 'rs', 'pkr', 'price', 'off', 'deal', 'new', 'original',
            'pcs', 'piece', 'pieces', 'pack', 'set', 'item', 'items', 'genuine', 'hot', 'sale',
            'high', 'quality', 'best', 'free', 'shipping'
        ];

        // Tokenize words
        $allWords = explode(' ', strtolower($cleanTitle));
        $meaningfulWords = [];
        foreach ($allWords as $w) {
            $w = trim($w);
            // remove measurement suffixes like 500ml, 100g, 20w, etc.
            $w = preg_replace('/^\d+(ml|g|kg|pcs|pc|mah|w|v|cm|mm|m|inch)?$/i', '', $w);
            if (strlen($w) >= 3 && !in_array($w, $stopWords) && !is_numeric($w)) {
                $meaningfulWords[] = $w;
            }
        }
        $meaningfulWords = array_values(array_unique($meaningfulWords));

        // 1. Tags generation (Max 5 truly relatable product tags)
        $tags = [];

        // Tag 1: Core 2-3 word phrase from meaningful words (e.g. "collagen hair mask")
        if (count($meaningfulWords) >= 2) {
            $tags[] = implode(' ', array_slice($meaningfulWords, 0, min(3, count($meaningfulWords))));
        }

        // Tag 2: Primary 2-word pair (e.g. "hair mask")
        if (count($meaningfulWords) >= 2) {
            $tags[] = $meaningfulWords[count($meaningfulWords) - 2] . ' ' . $meaningfulWords[count($meaningfulWords) - 1];
        }

        // Tag 3: Secondary 2-word pair or specific attribute
        if (count($meaningfulWords) >= 3) {
            $tags[] = $meaningfulWords[0] . ' ' . $meaningfulWords[1];
        }

        // Tag 4: Category Name
        if (!empty($categoryName)) {
            $cleanCat = strtolower(trim(preg_replace('/[^\p{L}\s]/u', '', $categoryName)));
            if (!empty($cleanCat)) {
                $tags[] = $cleanCat;
            }
        }

        // Tag 5: Top individual meaningful words
        foreach ($meaningfulWords as $mw) {
            $tags[] = $mw;
        }

        // Clean & Deduplicate tags
        $finalTags = [];
        foreach ($tags as $t) {
            $t = trim($t);
            if (!empty($t) && !in_array($t, $finalTags)) {
                $finalTags[] = $t;
            }
            if (count($finalTags) >= 5) {
                break;
            }
        }
        if (empty($finalTags)) {
            $finalTags[] = strtolower(Str::limit($cleanTitle, 30, ''));
        }
        $tagsString = implode(', ', array_slice($finalTags, 0, 5));

        // 2. Meta Keywords generation (Max 5 High-Intent Search Queries)
        $metaKeywords = [];
        $shortCore = !empty($meaningfulWords) ? implode(' ', array_slice($meaningfulWords, 0, min(3, count($meaningfulWords)))) : strtolower(Str::limit($cleanTitle, 35, ''));

        // Query 1: buy [core product]
        $metaKeywords[] = 'buy ' . $shortCore;

        // Query 2: [core product] price in pakistan
        $metaKeywords[] = $shortCore . ' price in pakistan';

        // Query 3: best [core product / category] in pakistan
        $catLabel = !empty($categoryName) ? strtolower(trim($categoryName)) : $shortCore;
        $metaKeywords[] = 'best ' . $catLabel . ' in pakistan';

        // Query 4: [core product] online pakistan
        $metaKeywords[] = $shortCore . ' online pakistan';

        // Query 5: original [core product] cash on delivery
        $metaKeywords[] = 'original ' . $shortCore . ' cash on delivery';

        // Clean & strictly take top 5
        $finalMetaKeywords = array_values(array_unique(array_slice($metaKeywords, 0, 5)));
        $metaKeywordsString = implode(', ', $finalMetaKeywords);

        // 3. Meta Description (100% perfectly fit SEO snippet under 160 chars)
        $cleanDesc = trim(preg_replace('/\s+/', ' ', strip_tags($description)));
        if (strlen($cleanDesc) >= 60 && strlen($cleanDesc) <= 155) {
            $metaDesc = $cleanDesc;
        } elseif (strlen($cleanDesc) > 155) {
            $metaDesc = Str::limit($cleanDesc, 148, '...');
        } else {
            $displayTitle = Str::limit($rawTitle, 55, '');
            $metaDesc = "Buy {$displayTitle} online in Pakistan at best price. High quality, cash on delivery & easy returns at Tamsal.";
            if (strlen($metaDesc) > 158) {
                $metaDesc = Str::limit($metaDesc, 155, '...');
            }
        }

        return [
            'tags' => $tagsString,
            'meta_keywords' => $metaKeywordsString,
            'meta_description' => $metaDesc,
        ];
    }
}
