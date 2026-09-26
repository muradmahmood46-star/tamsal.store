@php
    $user = Auth::user() ?? ($user ?? null);
@endphp
@if($user)
<div class="col-lg-4">
    <aside class="user-info-wrapper">
      <div class="user-info">
        <div class="user-avatar">
          <img id="avater_photo_view" src="{{ $user->photoUrl() }}" alt="User" style="width: 90px; height: 90px; object-fit: cover; border-radius: 50%;">
        </div>

        <div class="user-data">
          <h4 class="h5">{{ $user->first_name . ' ' . $user->last_name }}</h4>
          @if($user->isSeller())
            <a href="{{ route('front.catalog', ['vendor' => $user->id]) }}" target="_blank" title="{{ __('View Live Store') }}" style="text-decoration: none;">
                <span class="badge badge-success px-2 py-1 mb-1 font-weight-bold"><i class="fas fa-store mr-1"></i>{{ ($user->seller && !empty($user->seller->shop_name)) ? $user->seller->shop_name : __('Verified Seller') }} <i class="fas fa-external-link-alt ml-1" style="font-size: 9px;"></i></span>
            </a><br>
          @endif
          <span>{{__('Joined')}} {{$user->created_at ? $user->created_at->format('M d, Y') : ''}}</span>
        </div>
      </div>
      <nav class="list-group">
        <a class="list-group-item {{ request()->is('user/dashboard') ? 'active' : '' }}" href="{{route('user.dashboard')}}"><i class="icon-command"></i>{{__('Dashboard')}}</a>
        <a class="list-group-item {{ request()->is('user/profile') ? 'active' : '' }}" href="{{route('user.profile')}}"><i class="icon-user"></i>{{__('Profile')}}</a>
        @if($user->isSeller())
            <a class="list-group-item text-success font-weight-bold" href="{{ route('front.catalog', ['vendor' => $user->id]) }}" target="_blank"><i class="fas fa-store"></i>{{__('My Store (Live)')}}</a>
            <a class="list-group-item text-primary font-weight-bold" href="{{route('seller.dashboard')}}"><i class="icon-layout"></i>{{__('Seller Dashboard')}}</a>
        @else
            <a class="list-group-item {{ request()->is('user/open-shop*') ? 'active' : '' }}" href="{{route('user.store.apply')}}"><i class="icon-shopping-bag"></i>{{__('Open Shop / List Product')}}</a>
        @endif
        <a class="list-group-item {{ request()->is('user/ticket') ? 'active' : '' }}" href="{{route('user.ticket')}}"><i class="icon-file-text"></i>{{__('Support Ticket')}}</a>
        @php
            $userUnreadCount = \App\Models\Conversation::where('user_id', $user->id)->where('user_unread_count', '>', 0)->where('deleted_by_user', 0)->sum('user_unread_count');
        @endphp
        <a class="list-group-item with-badge {{ request()->is('user/messages*') ? 'active' : '' }}" href="{{route('user.message.index')}}">
            <i class="icon-message-square"></i>{{__('My Chats & Messages')}}
            @if($userUnreadCount > 0)
                <span class="badge badge-success badge-pill">{{$userUnreadCount}}</span>
            @endif
        </a>
        <a class="list-group-item with-badge {{ request()->is('user/orders') ? 'active' : '' }}" href="{{route('user.order.index')}}"><i class="icon-shopping-bag"></i>{{__('Orders')}}<span class="badge badge-default badge-pill">{{$user->orders->count()}}</span></a>
        <a class="list-group-item {{ request()->is('user/addresses') ? 'active' : '' }}" href="{{route('user.address')}}"><i class="icon-map-pin"></i>{{__('Address')}}</a>
        <a class="list-group-item  with-badge {{ request()->is('user/wishlists') ? 'active' : '' }}" href="{{route('user.wishlist.index')}}"><i class="icon-heart"></i>{{__('Wishlist')}}<span class="badge badge-default badge-pill">{{$user->wishlists->count()}}</span></a>
        <a class="list-group-item remove-account with-badge" data-bs-toggle="modal" data-bs-target=".modal" href="javascript:;"><i class="icon-trash"></i>{{__('Delete Account')}}</a>
        <a class="list-group-item with-badge" href="{{route('user.logout')}}"><i class="icon-log-out"></i>{{__('Log out')}}</a>
      </nav>
    </aside>

    <div class="modal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{__('Remove Account')}}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>{{__('Are You Sure?')}}</p>
            <p>{{__('Do you remove you account?')}}</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
            <a href="{{route('user.account.remove')}}" type="button" class="btn btn-danger">{{__('Remove Account')}}</a>
          </div>
        </div>
      </div>
    </div>

  </div>
@endif
