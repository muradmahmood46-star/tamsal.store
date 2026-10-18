<?php

namespace App\Http\Controllers\User;

use App\{
    Http\Requests\UserRequest,
    Http\Controllers\Controller,
    Repositories\Front\UserRepository
};
use App\Helpers\ImageHelper;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AccountController extends Controller
{

    /**
     * Constructor Method.
     *
     * Setting Authentication
     *
     * @param  \App\Repositories\Back\UserRepository $repository
     *
     */
    public function __construct(UserRepository $repository)
    {
        $this->middleware('auth');
        $this->middleware('localize');
        $this->repository = $repository;
    }

    public function index()
    {
        $userId = Auth::user()->id;
        $unreadChats = \App\Models\Conversation::where('user_id', $userId)->where('user_unread_count', '>', 0)->where('deleted_by_user', 0)->sum('user_unread_count');
        $totalChats = \App\Models\Conversation::where('user_id', $userId)->where('deleted_by_user', 0)->count();

        return view('user.dashboard.dashboard',[
            'allorders' => Order::whereUserId($userId)->count(),
            'pending' => Order::whereUserId($userId)->whereOrderStatus('pending')->count(),
            'progress' => Order::whereUserId($userId)->whereOrderStatus('In Progress')->count(),
            'delivered' => Order::whereUserId($userId)->whereOrderStatus('Delivered')->count(),
            'canceled' => Order::whereUserId($userId)->whereOrderStatus('Canceled')->count(),
            'unreadChats' => $unreadChats,
            'totalChats' => $totalChats,
        ]);
    }


    public function profile()
    {
        $user = Auth::user();
        $check_newsletter = Subscriber::where('email',$user->email)->exists();
        return view('user.dashboard.index',[
            'user' => $user,
            'check_newsletter' => $check_newsletter,
        ]);
    }



    public function profileUpdate(UserRequest $request)
    {   
     
        $this->repository->profileUpdate($request);
        Session::flash('success',__('Profile Updated Successfully.'));
        return redirect()->back();
    }

    public function addresses()
    {
        $user = Auth::user();
        $addresses = UserAddress::where('user_id', $user->id)
            ->orderBy('is_default', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return view('user.dashboard.address', [
            'user' => $user,
            'addresses' => $addresses
        ]);
    }

    public function addressStore(Request $request)
    {
        $request->validate([
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'required|max:50',
            'province' => 'required|max:100',
            'city' => 'required|max:100',
            'area' => 'required|max:100',
            'locality' => 'nullable|max:191',
            'address1' => 'required',
            'zip' => 'nullable|max:50',
        ]);

        $user = Auth::user();
        $hasExisting = UserAddress::where('user_id', $user->id)->exists();
        $isDefault = ($request->has('is_default') && $request->is_default == 1) || !$hasExisting ? 1 : 0;

        if ($isDefault) {
            UserAddress::where('user_id', $user->id)->update(['is_default' => 0]);
        }

        $address = UserAddress::create([
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'province' => $request->province,
            'city' => $request->city,
            'area' => $request->area,
            'locality' => $request->locality,
            'address1' => $request->address1,
            'zip' => $request->zip ?? 'N/A',
            'country' => 'Pakistan',
            'is_default' => $isDefault,
        ]);

        if ($isDefault) {
            $user->update([
                'bill_address1' => $address->address1,
                'bill_address2' => $address->locality,
                'bill_city' => $address->city,
                'bill_zip' => $address->zip,
                'bill_country' => 'Pakistan',
                'ship_address1' => $address->address1,
                'ship_address2' => $address->locality,
                'ship_city' => $address->city,
                'ship_zip' => $address->zip,
                'ship_country' => 'Pakistan',
            ]);
        }

        Session::flash('success', __('Address saved successfully.'));
        return back();
    }

    public function addressDelete($id)
    {
        $user = Auth::user();
        $address = UserAddress::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $remaining = UserAddress::where('user_id', $user->id)->orderBy('id', 'desc')->first();
            if ($remaining) {
                $remaining->update(['is_default' => 1]);
            }
        }

        Session::flash('success', __('Address deleted successfully.'));
        return back();
    }

    public function addressSetDefault($id)
    {
        $user = Auth::user();
        UserAddress::where('user_id', $user->id)->update(['is_default' => 0]);
        $address = UserAddress::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        $address->update(['is_default' => 1]);

        $user->update([
            'bill_address1' => $address->address1,
            'bill_address2' => $address->locality,
            'bill_city' => $address->city,
            'bill_zip' => $address->zip,
            'bill_country' => 'Pakistan',
            'ship_address1' => $address->address1,
            'ship_address2' => $address->locality,
            'ship_city' => $address->city,
            'ship_zip' => $address->zip,
            'ship_country' => 'Pakistan',
        ]);

        Session::flash('success', __('Default address updated successfully.'));
        return back();
    }

    public function billingSubmit(Request $request)
    {

        $request->validate([
            'bill_address1' => 'required|max:100',
            'bill_address2' => 'nullable|max:100',
            'bill_zip'      => 'nullable|max:100',
            'bill_city'      => 'required|max:100',
            'bill_company'   => 'nullable|max:100',
            'bill_country'   => 'required|max:100',
        ]);
        $user =  Auth::user();
        $input = $request->all();
        $user->update($input);
        Session::flash('success',__('Address update successfully'));
        return back();
    }

    public function shippingSubmit(Request $request)
    {
        $request->validate([
            'ship_address1' => 'required|max:100',
            'ship_address2' => 'nullable|max:100',
            'ship_zip'      => 'nullable|max:100',
            'ship_city'      => 'required|max:100',
            'ship_company'   => 'nullable|max:100',
            'ship_country'   => 'required|max:100',
        ]);
        $user =  Auth::user();
        $input = $request->all();
        $user->update($input);
        Session::flash('success',__('Address update successfully'));
        return back();
    }


    public function removeAccount()
    {
        $user = User::where('id',Auth::user()->id)->first();
        ImageHelper::handleDeletedImage($user,'photo','assets/images/');
        $user->delete();
        Session::flash('success',__('Your account successfully remove'));
        return redirect(route('front.index'));
    }


}
