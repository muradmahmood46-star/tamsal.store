<?php

namespace App\Http\Controllers\Back;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\HomeCutomize;
use Illuminate\Http\Request;

class HomePageController extends Controller
{

     /**
     * Constructor Method.
     *
     * Setting Authentication
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('adminlocalize');
    }


    public function index(){
        $data = HomeCutomize::first();

        return view('back.home-page.index',[
            'hero_banner' => json_decode($data->hero_banner,true),
            'first_banner' => json_decode($data->banner_first,true),
            'secend_banner' => json_decode($data->banner_secend,true),
            'third_banner' => json_decode($data->banner_third,true),
            'popular_category' => json_decode($data->popular_category,true),
            'three_column_category' => json_decode($data->two_column_category,true),
            'feature_category' => json_decode($data->feature_category,true),
            'home4_banner' => json_decode($data->home_page4,true),
            'home_4_popular_category' => json_decode($data->home_4_popular_category,true),
        ]);
    }

    /**
     * Reusable validation rule for banner images and Lottie files (.json, .lottie)
     */
    private function imageOrLottieRule($maxKb = 25600)
    {
        return [
            'nullable',
            function ($attribute, $value, $fail) use ($maxKb) {
                if ($value && $value instanceof \Illuminate\Http\UploadedFile) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    $allowed = ['jpeg', 'jpg', 'png', 'gif', 'svg', 'webp', 'json', 'lottie', 'txt', 'avif', 'bmp'];
                    if (!in_array($ext, $allowed)) {
                        $fail(__(':attribute must be an image or a Lottie animation file (.json, .lottie).', ['attribute' => $attribute]));
                    }
                    if ($value->getSize() > $maxKb * 1024) {
                        $fail(__(':attribute may not be greater than :max KB.', ['attribute' => $attribute, 'max' => $maxKb]));
                    }
                }
            }
        ];
    }

    public function hero_banner_update(Request $request)
    {
        $request->validate([
            'img1' => $this->imageOrLottieRule(),
            'img2' => $this->imageOrLottieRule(),
            'title1' => 'nullable|max:200',
            'title2' => 'nullable|max:200',
            'subtitle1' => 'nullable|max:200',
            'url1' => 'nullable|max:200',
            'url2' => 'nullable|max:200',

        ]);
        $all_images_names = ['img1','img2'];
        $input = $request->all();
        foreach($all_images_names as $single_image){
            if($request->hasFile($single_image)){
                $data = HomeCutomize::first();
                $check = json_decode($data->hero_banner,true);
                $input[$single_image] = ImageHelper::handleUploadedImage($request->$single_image,'images',isset($check[$single_image]) ? $check[$single_image] : null);
            }
        }

        unset($input['_token']);
        $data = HomeCutomize::first();
        foreach(json_decode($data->hero_banner,true) as $key => $value){
            // Empty form fields are converted to null by Laravel.  Preserve that
            // intentional value instead of restoring the previously saved text.
            if(array_key_exists($key, $input)){
                $input[$key] =  $input[$key];
            }else{
                $input[$key] = $value;
            }
        }


        $data->hero_banner = json_encode($input,true);
        $data->update();
        return redirect()->back()->withSuccess(__('Banner Update Successfully'));

    }
    public function first_banner_update(Request $request)
    {
        $request->validate([
            'img1' => $this->imageOrLottieRule(),
            'img2' => $this->imageOrLottieRule(),
            'img3' => $this->imageOrLottieRule(),
            'img4' => $this->imageOrLottieRule(),
            'firsturl1' => 'nullable|max:200',
            'firsturl2' => 'nullable|max:200',
            'firsturl3' => 'nullable|max:200',
            'firsturl4' => 'nullable|max:200',
        ]);
        $all_images_names = ['img1','img2','img3','img4'];

        $input = $request->all();

        $data = HomeCutomize::first();

        foreach($all_images_names as $single_image){
            if($request->hasFile($single_image)){
                $data = HomeCutomize::first();
                $check = json_decode($data->banner_first,true);
                $input[$single_image] = ImageHelper::handleUploadedImage($request->$single_image,'images', $check[$single_image] ?? null);
            }else{
                $check = json_decode($data->banner_first,true);
                $input[$single_image] = $check[$single_image] ?? '';
            }
        }

        unset($input['_token']);
        unset($input['is_three_c_b_first']);

        $data->banner_first = json_encode($input,true);
        $data->update();

        if ($request->has('form_submitted')) {
            \App\Models\Setting::find(1)->update([
                'is_three_c_b_first' => $request->has('is_three_c_b_first') ? 1 : 0
            ]);
        }

        return redirect()->back()->withSuccess(__('Banner Update Successfully'));

    }

    public function secend_banner_update(Request $request)
    {
        $request->validate([
            'img1' => $this->imageOrLottieRule(),
            'img2' => $this->imageOrLottieRule(),
            'img3' => $this->imageOrLottieRule(),
            'url1' => 'nullable|max:200',
            'url2' => 'nullable|max:200',
            'url3' => 'nullable|max:200',
        ]);
        $all_images_names = ['img1','img2','img3'];
        $input = $request->all();

        $data = HomeCutomize::first();

        foreach($all_images_names as $single_image){
            if($request->hasFile($single_image)){
                $data = HomeCutomize::first();
                $check = json_decode($data->banner_secend,true);
                $input[$single_image] = ImageHelper::handleUploadedImage($request->$single_image,'images',$check[$single_image]);
            }else{
                $check = json_decode($data->banner_secend,true);
                $input[$single_image] = $check[$single_image];
            }
        }

        unset($input['_token']);
        unset($input['is_three_c_b_second']);

        $data->banner_secend = json_encode($input,true);
        $data->update();

        if ($request->has('form_submitted')) {
            \App\Models\Setting::find(1)->update([
                'is_three_c_b_second' => $request->has('is_three_c_b_second') ? 1 : 0
            ]);
        }

        return redirect()->back()->withSuccess(__('Banner Update Successfully'));

    }

    public function third_banner_update(Request $request)
    {

        $request->validate([
            'img1' => $this->imageOrLottieRule(),
            'img2' => $this->imageOrLottieRule(),
            'url1' => 'nullable|max:200',
            'url2' => 'nullable|max:200',
        ]);
        $all_images_names = ['img1','img2'];

        $input = $request->all();
        $data = HomeCutomize::first();

        foreach($all_images_names as $single_image){
            if($request->hasFile($single_image)){
                $data = HomeCutomize::first();
                $check = json_decode($data->banner_third,true);
                $input[$single_image] = ImageHelper::handleUploadedImage($request->$single_image,'images',$check[$single_image]);
            }else{
                $check = json_decode($data->banner_third,true);
                $input[$single_image] = $check[$single_image];
            }
        }
        unset($input['_token']);
        unset($input['is_two_c_b']);

        $data->banner_third = json_encode($input,true);
        $data->update();

        if ($request->has('form_submitted')) {
            \App\Models\Setting::find(1)->update([
                'is_two_c_b' => $request->has('is_two_c_b') ? 1 : 0
            ]);
        }

        return redirect()->back()->withSuccess(__('Banner Update Successfully'));

    }

    public function homepage4update(Request $request)
    {
        $request->validate([
            'img1' => $this->imageOrLottieRule(),
            'img2' => $this->imageOrLottieRule(),
            'img3' => $this->imageOrLottieRule(),
            'img4' => $this->imageOrLottieRule(),
            'img5' => $this->imageOrLottieRule(),
            'url1' => 'required|max:200',
            'url2' => 'required|max:200',
            'url3' => 'required|max:200',
            'url4' => 'required|max:200',
            'url5' => 'required|max:200',
            'label1' => 'required|max:200',
            'label2' => 'required|max:200',
            'label3' => 'required|max:200',
            'label4' => 'required|max:200',
            'label5' => 'required|max:200',
        ]);
        $all_images_names = ['img1','img2','img3','img4','img5'];
        $input = $request->all();
        foreach($all_images_names as $single_image){
            if($request->hasFile($single_image)){
                $data = HomeCutomize::first();
                $check = json_decode($data->home_page4,true);
                $input[$single_image] = ImageHelper::handleUploadedImage($request->$single_image,'images',$check[$single_image]);
            }
        }

        unset($input['_token']);

        $data = HomeCutomize::first();
        if(!$data->home_page4){
        $data->home_page4 = json_encode($input,true);
        $data->update();
        }else{
            foreach(json_decode($data->home_page4,true) as $key => $value){
                if(isset($input[$key])){
                    $input[$key] =  $input[$key];
                }else{
                    $input[$key] = $value;
                }
            }
            $data->home_page4 = json_encode($input,true);
            $data->update();
        }

        return redirect()->back()->withSuccess(__('Banner Update Successfully'));


    }


    public function popular_category_update(Request $request)
    {
        $request->validate([
            'popular_title' => 'required|max:255',
        ]);
        $input = $request->all();
        unset($input['_token']);
        $data = HomeCutomize::first();
        $data->popular_category = json_encode($input,true);
        $data->update();
        return redirect()->back()->withSuccess(__('Popular Category Update Successfully'));
    }

    public function tree_column_category_update(Request $request)
    {
        $input = $request->all();
        unset($input['_token']);
        $data = HomeCutomize::first();
        $data->two_column_category = json_encode($input,true);
        $data->update();
        return redirect()->back()->withSuccess(__('Tree Column Category Update Successfully'));
    }


    public function feature_category_update(Request $request)
    {
        $request->validate([
            'feature_title' => 'required|max:255',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);
        $input = [
            'feature_title' => $request->feature_title,
            'limit' => $request->limit ? (int)$request->limit : 8,
        ];
        $data = HomeCutomize::first();
        $data->feature_category = json_encode($input, true);
        $data->update();

        if ($request->has('form_submitted')) {
            \App\Models\Setting::find(1)->update([
                'is_featured_category' => $request->has('is_featured_category') ? 1 : 0
            ]);
        }

        return redirect()->back()->withSuccess(__('Newly Listed Products Setting Updated Successfully'));
    }

    public function homepage4categoryupdate(Request $request)
    {
       $category = json_encode($request->home_4_popular_category,true);
       $data = HomeCutomize::first();
       $data->home_4_popular_category = $category;
       $data->update();
       return redirect()->back()->withSuccess(__('Banner Update Successfully'));

    }
}
