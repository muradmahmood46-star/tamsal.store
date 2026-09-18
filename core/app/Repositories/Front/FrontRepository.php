<?php

namespace App\Repositories\Front;

use App\{
    Models\Post,
    Models\Page,
    Models\Order,
};
use App\Helpers\PriceHelper;
use App\Helpers\ImageHelper;
use App\Models\Bcategory;
use Illuminate\Support\Facades\Auth;

class FrontRepository
{

    public function displayPosts($request){
        if($request->has('category')){
            return Post::with('category')->whereCategoryId(Bcategory::where('slug',$request->category)->first()->id)->latest('id')->paginate(6);
        }
        else if($request->has('search')){
            return Post::with('category')->where('title', 'like', '%' . $request->search . '%')->orWhere('details', 'like', '%' . $request->search  . '%')->latest('id')->paginate(6);
        }

        else if($request->has('tag')){
            return Post::with('category')->where('tags', 'like', '%' . $request->tag . '%')->latest('id')->paginate(6);
        }
        else{
            return Post::with('category')->latest('id')->paginate(6);
        }
    }

    public function displayPost($slug){
        $tagz = '';
        $tags = null;
        $name = Post::pluck('tags')->toArray();
        foreach($name as $nm)
        {
            $tagz .= $nm.',';
        }
        $tags = array_unique(explode(',',$tagz));
        return [
            'posts'       => Post::orderby('id','desc')->take(4)->get(),
            'post'       => Post::whereSlug($slug)->first(),
            'categories' => Bcategory::withCount('posts')->whereStatus(1)->get(),
            'tags'       => array_filter($tags)
        ];
    }

    public function displayPage($slug){
        return Page::whereSlug($slug)->firstOrFail();
    }

    public function reviewSubmit($request)
    {
        $user = Auth::user();
        if (!$user) {
            return [
                'errors' => [
                    0 => __('Please login to submit a review.'),
                ],
            ];
        }

        $input = [
            'item_id' => $request->item_id,
            'rating'  => $request->rating,
            'subject' => $request->subject,
            'review'  => $request->review,
            'status'  => 1,
        ];

        // Handle up to 3 uploaded photos
        $uploadedPhotos = [];
        if ($request->hasFile('photos')) {
            $files = is_array($request->file('photos')) ? $request->file('photos') : [$request->file('photos')];
            $files = array_slice($files, 0, 3);
            foreach ($files as $file) {
                if ($file) {
                    $savedName = ImageHelper::handleUploadedImage($file, 'images');
                    if ($savedName) {
                        $uploadedPhotos[] = $savedName;
                    }
                }
            }
        } elseif ($request->hasFile('photo')) {
            $savedName = ImageHelper::handleUploadedImage($request->file('photo'), 'images');
            if ($savedName) {
                $uploadedPhotos[] = $savedName;
            }
        }

        if (!empty($uploadedPhotos)) {
            $input['photo'] = json_encode($uploadedPhotos);
        }

        // Check if the user already has a review for this item
        $existingReview = $user->reviews()->where('item_id', $request->item_id)->first();

        if ($existingReview) {
            if (!empty($uploadedPhotos) && !empty($existingReview->photo)) {
                $oldPhotos = json_decode($existingReview->photo, true);
                if (!is_array($oldPhotos)) {
                    $oldPhotos = [$existingReview->photo];
                }
                foreach ($oldPhotos as $oldP) {
                    ImageHelper::handleUploadedImage(null, 'images', $oldP);
                }
            }
            $existingReview->update($input);
            return __('Your Review Updated Successfully.');
        }

        // Create a new review
        $user->reviews()->create($input);
        return __('Your Review Submitted Successfully.');
    }
    


}
