@php
    $sellerPendingOrdersCount = \App\Models\Order::where('vendor_id', Auth::id())
        ->where('order_status', 'Pending')
        ->count();
@endphp

<ul class="nav">

    <li class="nav-item {{ request()->is('seller/dashboard') || request()->is('seller') ? 'active' : '' }}">
        <a href="{{ route('seller.dashboard') }}">
            <i class="fas fa-home"></i>
            <p>{{ __('Dashboard') }}</p>
        </a>
    </li>

    <li class="nav-item {{ request()->is('seller/item*') || request()->is('seller/deal*') || request()->is('seller/bulk*') || request()->is('seller/brand*') ? 'active submenu' : '' }}">
        <a data-toggle="collapse" href="#items">
            <i class="fab fa-product-hunt"></i>
            <p>{{ __('Manage Products') }}</p>
            <span class="caret"></span>
        </a>
        <div class="collapse {{ request()->is('seller/item*') || request()->is('seller/deal*') || request()->is('seller/bulk*') || request()->is('seller/brand*') ? 'show' : '' }}" id="items">
            <ul class="nav nav-collapse">
                <li class="{{ request()->is('seller/brand*') ? 'active' : '' }}">
                    <a class="sub-link" href="{{ route('seller.brand.index') }}">
                        <span class="sub-item">{{ __('Brands') }}</span>
                    </a>
                </li>
                <li class="{{ request()->is('seller/item/create') || request()->is('seller/item/add') ? 'active' : '' }}">
                    <a class="sub-link" href="{{ route('seller.item.create') }}">
                        <span class="sub-item">{{ __('Add Product') }}</span>
                    </a>
                </li>
                <li class="{{ request()->is('seller/item') && !request()->input('is_type') ? 'active' : '' }}">
                    <a class="sub-link" href="{{ route('seller.item.index') }}">
                        <span class="sub-item">{{ __('All Products') }}</span>
                    </a>
                </li>
                <li class="{{ request()->is('seller/deal*') ? 'active' : '' }}">
                    <a class="sub-link" href="{{ route('seller.deal.index') }}">
                        <span class="sub-item">{{ __('Manage Bundles') }}</span>
                    </a>
                </li>
                <li class="{{ request()->is('seller/stock/out/product') ? 'active' : '' }}">
                    <a class="sub-link" href="{{ route('seller.item.stock.out') }}">
                        <span class="sub-item">{{ __('Stock Out Products') }}</span>
                    </a>
                </li>
                <li class="{{ request()->is('seller/bulk/product*') ? 'active' : '' }}">
                    <a class="sub-link" href="{{ route('seller.bulk.product.index') }}">
                        <span class="sub-item">{{ __('CSV Import & Export') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <li class="nav-item {{ request()->is('seller/orders*') || request()->is('seller/order*') ? 'active submenu' : '' }}">
        <a data-toggle="collapse" href="#order">
            <i class="fab fa-first-order"></i>
            <p>{{ __('Manage Orders') }} </p>
            <span class="caret"></span>
        </a>
        <div class="collapse {{ request()->is('seller/orders*') || request()->is('seller/order*') ? 'show' : '' }}" id="order">
            <ul class="nav nav-collapse">
                <li class="{{ !request()->input('type') && request()->is('seller/orders') ? 'active' : '' }}">
                    <a class="sub-link" href="{{ route('seller.order.index') }}">
                        <span class="sub-item">{{ __('All Orders List') }}</span>
                    </a>
                </li>
                <li class="{{ request()->input('type') == 'Pending' ? 'active' : '' }}">
                    <a class="sub-link" href="{{ route('seller.order.index') . '?type=' . 'Pending' }}">
                        <span class="sub-item">{{ __('New Orders') }}</span>
                        @if($sellerPendingOrdersCount > 0)
                            <span class="badge badge-warning text-dark ml-2" style="font-size: 11px; padding: 2px 7px; border-radius: 10px;">{{ $sellerPendingOrdersCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="{{ request()->input('type') == 'Accepted' ? 'active' : '' }}">
                    <a class="sub-link" href="{{ route('seller.order.index') . '?type=' . 'Accepted' }}">
                        <span class="sub-item">{{ __('Accepted Orders') }}</span>
                    </a>
                </li>
                <li class="{{ request()->input('type') == 'Send to Delivery House' ? 'active' : '' }}">
                    <a class="sub-link" href="{{ route('seller.order.index') . '?type=' . 'Send to Delivery House' }}">
                        <span class="sub-item">{{ __('Send to Delivery House') }}</span>
                    </a>
                </li>
                <li class="{{ request()->input('type') == 'In Progress' ? 'active' : '' }}">
                    <a class="sub-link" href="{{ route('seller.order.index') . '?type=' . 'In Progress' }}">
                        <span class="sub-item">{{ __('Delivery in Progress') }}</span>
                    </a>
                </li>
                <li class="{{ request()->input('type') == 'Delivered' ? 'active' : '' }}">
                    <a class="sub-link" href="{{ route('seller.order.index') . '?type=' . 'Delivered' }}">
                        <span class="sub-item">{{ __('Delivered Orders') }}</span>
                    </a>
                </li>
                <li class="{{ request()->input('type') == 'Canceled' ? 'active' : '' }}">
                    <a class="sub-link" href="{{ route('seller.order.index') . '?type=' . 'Canceled' }}">
                        <span class="sub-item">{{ __('Canceled Orders') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    @php
        $sellerUnreadCount = \App\Models\Conversation::where('vendor_id', Auth::id())->where('user_id', '!=', 0)->where('vendor_unread_count', '>', 0)->where('deleted_by_vendor', 0)->sum('vendor_unread_count');
        $sellerAdminUnreadCount = \App\Models\Conversation::where('vendor_id', Auth::id())->where('user_id', 0)->whereNull('item_id')->where('vendor_unread_count', '>', 0)->where('deleted_by_vendor', 0)->sum('vendor_unread_count');
    @endphp
    <li class="nav-item {{ request()->is('seller/messages*') ? 'active' : '' }}">
        <a href="{{ route('seller.message.index') }}">
            <i class="fas fa-comments text-primary"></i>
            <p>{{ __('Customer Messages') }}</p>
            @if($sellerUnreadCount > 0)
                <span class="badge badge-success badge-counter" style="position: absolute; right: 15px; top: 12px; font-size: 11px; padding: 2px 7px; border-radius: 10px;">{{ $sellerUnreadCount }}</span>
            @endif
        </a>
    </li>

    <li class="nav-item {{ request()->is('seller/admin-messages*') ? 'active' : '' }}">
        <a href="{{ route('seller.admin_message.index') }}">
            <i class="fas fa-user-shield text-info"></i>
            <p>{{ __('Admin Messages') }}</p>
            @if($sellerAdminUnreadCount > 0)
                <span class="badge badge-danger badge-counter" style="position: absolute; right: 15px; top: 12px; font-size: 11px; padding: 2px 7px; border-radius: 10px; font-weight: bold;">{{ $sellerAdminUnreadCount }}</span>
            @endif
        </a>
    </li>

    @php
        $vendorId = Auth::id();
        $unreadAnnouncementsCount = 0;
        if ($vendorId && \Illuminate\Support\Facades\Schema::hasTable('vendor_announcements') && \Illuminate\Support\Facades\Schema::hasTable('vendor_announcement_views')) {
            $viewedIds = \App\Models\VendorAnnouncementView::where('vendor_id', $vendorId)->pluck('announcement_id');
            $unreadAnnouncementsCount = \App\Models\VendorAnnouncement::where('status', 1)
                ->whereNotIn('id', $viewedIds)
                ->count();
        }
    @endphp
    <li class="nav-item {{ request()->is('seller/announcements*') ? 'active' : '' }}">
        <a href="{{ route('seller.announcement.index') }}">
            <i class="fas fa-bullhorn text-warning"></i>
            <p>{{ __('Announcements') }}</p>
            @if($unreadAnnouncementsCount > 0)
                <span class="badge badge-danger badge-counter" style="position: absolute; right: 15px; top: 12px; font-size: 11px; padding: 2px 7px; border-radius: 10px; font-weight: bold;">{{ $unreadAnnouncementsCount }}</span>
            @endif
        </a>
    </li>

    <li class="nav-item {{ request()->is('seller/wallet*') ? 'active' : '' }}">
        <a href="{{ route('seller.wallet.index') }}">
            <i class="fas fa-wallet"></i>
            <p>{{ __('Wallet & Balance') }}</p>
        </a>
    </li>

    <li class="nav-item {{ request()->is('seller/transactions*') ? 'active' : '' }}">
        <a href="{{ route('seller.transaction.index') }}">
            <i class="fas fa-random"></i>
            <p>{{ __('Transactions') }}</p>
        </a>
    </li>

    <li class="nav-item {{ request()->is('seller/category*') || request()->is('seller/subcategory*') || request()->is('seller/childcategory*') || request()->is('seller/chieldcategory*') ? 'active submenu' : '' }}">
        <a data-toggle="collapse" href="#category">
            <i class="fas fa-list-alt"></i>
            <p>{{ __('Manage Categories') }}</p>
            <span class="caret"></span>
        </a>
        <div class="collapse {{ request()->is('seller/category*') || request()->is('seller/subcategory*') || request()->is('seller/childcategory*') || request()->is('seller/chieldcategory*') ? 'show' : '' }}" id="category">
            <ul class="nav nav-collapse">
                <li class="{{ request()->is('seller/category*') ? 'active' : '' }}">
                    <a class="sub-link" href="{{ route('seller.category.index') }}">
                        <span class="sub-item">{{ __('Categories') }}</span>
                    </a>
                </li>
                <li class="{{ request()->is('seller/subcategory*') ? 'active' : '' }}">
                    <a class="sub-link" href="{{ route('seller.subcategory.index') }}">
                        <span class="sub-item">{{ __('Sub categories') }}</span>
                    </a>
                </li>
                <li class="{{ request()->is('seller/childcategory*') || request()->is('seller/chieldcategory*') ? 'active' : '' }}">
                    <a class="sub-link" href="{{ route('seller.childcategory.index') }}">
                        <span class="sub-item">{{ __('Child categories') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <li class="nav-item {{ request()->is('seller/profile*') ? 'active' : '' }}">
        <a href="{{ route('seller.profile') }}">
            <i class="fas fa-store-alt"></i>
            <p>{{ __('Store Settings') }}</p>
        </a>
    </li>

</ul>
