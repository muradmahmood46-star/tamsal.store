<ul class="nav">
    <li class="nav-item {{ request()->is('admin') ? 'active' : '' }}">
        <a href="{{ route('back.dashboard') }}">
            <i class="fas fa-home"></i>
            <p>{{ __('Dashboard') }}</p>
        </a>
    </li>

    <li class="nav-item {{ request()->is('admin/stores*') ? 'active' : '' }}">
        <a href="{{ route('back.stores.index') }}">
            <i class="fas fa-store-alt text-primary"></i>
            <p>{{ __('All Stores') }}</p>
        </a>
    </li>

    @php
        $pendingStoreRequestsCount = \App\Models\StoreRequest::where('status', 'Pending')->count();
        $pendingUnblockRequestsCount = \App\Models\StoreUnblockRequest::where('is_seen', 0)->where('status', 'Pending')->count();
        $pendingFineApprovalsCount = \App\Models\FinePayment::where('status', 'pending')->count();
        $pendingVendorProductsCount = \App\Models\Item::whereNotNull('vendor_id')->where('vendor_id', '!=', 0)->where('approval_status', 'Pending')->count();
    @endphp

    <li class="nav-item {{ request()->is('admin/store-requests*') ? 'active' : '' }}">
        <a href="{{ route('back.store_request.index') }}">
            <i class="fas fa-store"></i>
            <p>{{ __('Store Requests') }}</p>
            @if($pendingStoreRequestsCount > 0)
                <span class="badge badge-danger badge-counter" style="position: absolute; right: 15px; top: 12px; font-size: 11px; padding: 2px 7px; border-radius: 10px;">{{ $pendingStoreRequestsCount }}</span>
            @endif
        </a>
    </li>

    <li class="nav-item {{ request()->is('admin/unblock-requests*') ? 'active' : '' }}">
        <a href="{{ route('back.unblock_request.index') }}">
            <i class="fas fa-unlock-alt text-warning"></i>
            <p>{{ __('Store Unblock Requests') }}</p>
            <span class="badge badge-warning text-dark badge-counter unblock-sidebar-badge" style="position: absolute; right: 15px; top: 12px; font-size: 11px; padding: 2px 7px; border-radius: 10px; font-weight: bold; {{ $pendingUnblockRequestsCount > 0 ? '' : 'display:none;' }}">{{ $pendingUnblockRequestsCount }}</span>
        </a>
    </li>

    <li class="nav-item {{ request()->is('admin/fine-approvals*') ? 'active' : '' }}">
        <a href="{{ route('back.fine_approval.index') }}">
            <i class="fas fa-file-invoice-dollar text-warning"></i>
            <p>{{ __('Fine Approval') }}</p>
            @if($pendingFineApprovalsCount > 0)
                <span class="badge badge-warning text-dark badge-counter" style="position: absolute; right: 15px; top: 12px; font-size: 11px; padding: 2px 7px; border-radius: 10px; font-weight: bold;">{{ $pendingFineApprovalsCount }}</span>
            @endif
        </a>
    </li>

    <li class="nav-item {{ request()->is('admin/store-settings*') ? 'active' : '' }}">
        <a href="{{ route('back.store_setting.index') }}">
            <i class="fas fa-cogs"></i>
            <p>{{ __('Stores Rate & Setting') }}</p>
        </a>
    </li>

    <li class="nav-item {{ request()->is('admin/vendor-products*') ? 'active' : '' }}">
        <a href="{{ route('back.vendor_product.index') }}">
            <i class="fas fa-boxes"></i>
            <p>{{ __('Vendor Products') }}</p>
            @if($pendingVendorProductsCount > 0)
                <span class="badge badge-warning text-dark badge-counter" style="position: absolute; right: 15px; top: 12px; font-size: 11px; padding: 2px 7px; border-radius: 10px; font-weight: bold;">{{ $pendingVendorProductsCount }}</span>
            @endif
        </a>
    </li>

    @php
        $pendingDepositRequestsCount = \App\Models\DepositRequest::where('status', 'pending')->count();
        $customerLiveChatCount = \App\Models\Conversation::where(function($q) {
            $q->whereNull('vendor_id')->orWhere('vendor_id', 0);
        })->where('user_id', '>', 0)->where('deleted_by_vendor', 0)->where('vendor_unread_count', '>', 0)->sum('vendor_unread_count');
    @endphp
    <li class="nav-item {{ request()->is('admin/deposit-requests*') ? 'active' : '' }}">
        <a href="{{ route('back.deposit_request.index') }}">
            <i class="fas fa-hand-holding-usd"></i>
            <p>{{ __('Deposit Request') }}</p>
            @if($pendingDepositRequestsCount > 0)
                <span class="badge badge-warning text-dark badge-counter" style="position: absolute; right: 15px; top: 12px; font-size: 11px; padding: 2px 7px; border-radius: 10px; font-weight: bold;">{{ $pendingDepositRequestsCount }}</span>
            @endif
        </a>
    </li>

    <li class="nav-item {{ request()->is('admin/messages*') ? 'active' : '' }}">
        <a href="{{ route('back.message.index') }}">
            <i class="fas fa-headset text-info"></i>
            <p>{{ __('Customer Live Chats') }}</p>
            @if($customerLiveChatCount > 0)
                <span class="badge badge-success badge-counter" style="position: absolute; right: 15px; top: 12px; font-size: 11px; padding: 2px 7px; border-radius: 10px; font-weight: bold;">{{ $customerLiveChatCount }}</span>
            @endif
        </a>
    </li>

    @php
        $lastSeenBuyerSeller = session('admin_buyer_seller_last_seen', \Illuminate\Support\Facades\Cache::get('admin_buyer_seller_last_seen'));
        if (request()->is('admin/buyer-seller-chats*')) {
            $totalBuyerSellerChatsCount = 0;
        } elseif ($lastSeenBuyerSeller) {
            $totalBuyerSellerChatsCount = \App\Models\Conversation::whereNotNull('vendor_id')
                ->where('vendor_id', '>', 0)
                ->where('last_message_at', '>', $lastSeenBuyerSeller)
                ->count();
        } else {
            $totalBuyerSellerChatsCount = 0;
        }
    @endphp
    <li class="nav-item {{ request()->is('admin/buyer-seller-chats*') ? 'active' : '' }}">
        <a href="{{ route('back.buyer_seller_chat.index') }}">
            <i class="fas fa-comments text-primary"></i>
            <p>{{ __('Buyer & Seller Chats') }}</p>
            @if($totalBuyerSellerChatsCount > 0)
                <span class="badge badge-info badge-counter" style="position: absolute; right: 15px; top: 12px; font-size: 11px; padding: 2px 7px; border-radius: 10px; font-weight: bold;">{{ $totalBuyerSellerChatsCount }}</span>
            @endif
        </a>
    </li>

    <li class="nav-item">
        <a data-toggle="collapse" href="#items">
            <i class="fab fa-product-hunt"></i>
            <p>{{ __('Manage Products') }}</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="items">
            <ul class="nav nav-collapse">
                <li>
                    <a class="sub-link" href="{{ route('back.brand.index') }}">
                        <span class="sub-item">{{ __('Brands') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.item.add') }}">
                        <span class="sub-item">{{ __('Add Product') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.item.index') }}">
                        <span class="sub-item">{{ __('All Products') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.item.stock.out') }}">
                        <span class="sub-item">{{ __('Stock Out Products') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.campaign.index') }}">
                        <span class="sub-item">{{ __('Campaign Offer') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.deal.index') }}">
                        <span class="sub-item">{{ __('Manage Bundles') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.bulk.product.index') }}">
                        <span class="sub-item">{{ __('CSV Import & Export') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.review.index') }}">
                      <span class="sub-item">{{ __('Product Reviews') }}</span></a>
                </li>
            </ul>
        </div>
    </li>

    <li class="nav-item {{ request()->is('orders/*') ? 'submenu' : '' }}">
        <a data-toggle="collapse" href="#order">
            <i class="fab fa-first-order"></i>
            <p>{{ __('Manage Orders') }} </p>
            @if(App\Models\Notification::countOrder() > 0)
                <span class="badge badge-danger badge-counter" style="position: absolute; right: 35px; top: 12px; font-size: 11px; padding: 2px 6px; border-radius: 10px;">{{ App\Models\Notification::countOrder() }}</span>
            @endif
            <span class="caret"></span>
        </a>
        <div class="collapse" id="order">
            <ul class="nav nav-collapse">
                <li class="{{!request()->input('type') && request()->is('admin/orders')  ? 'active' : ''}}">
                    <a class="sub-link" href="{{ route('back.order.index') }}">
                        <span class="sub-item">{{ __('All Orders List') }}</span>
                    </a>
                </li>
                <li class="{{request()->input('type') == 'Pending' ? 'active' : ''}}">
                    <a class="sub-link d-flex justify-content-between align-items-center" href="{{ route('back.order.index').'?type='.'Pending' }}">
                        <span class="sub-item">{{ __('New Orders') }}</span>
                        @if(App\Models\Notification::countOrder() > 0)
                            <span class="badge badge-danger" style="font-size: 11px; padding: 2px 7px; border-radius: 10px; margin-right: 15px;">{{ App\Models\Notification::countOrder() }}</span>
                        @endif
                    </a>
                </li>
                <li class="{{request()->input('type') == 'Accepted' ? 'active' : ''}}">
                    <a class="sub-link" href="{{ route('back.order.index').'?type='.'Accepted' }}">
                        <span class="sub-item">{{ __('Accepted Orders') }}</span>
                    </a>
                </li>
                <li class="{{request()->input('type') == 'Send to Delivery House' ? 'active' : ''}}">
                    <a class="sub-link" href="{{ route('back.order.index').'?type='.'Send to Delivery House' }}">
                        <span class="sub-item">{{ __('Send to Delivery House') }}</span>
                    </a>
                </li>
                <li class="{{request()->input('type') == 'In Progress' ? 'active' : ''}}">
                    <a class="sub-link" href="{{ route('back.order.index').'?type='.'In Progress' }}">
                        <span class="sub-item">{{ __('Delivery in Progress') }}</span>
                    </a>
                </li>

                <li class="{{request()->input('type') == 'Delivered' ? 'active' : ''}}">
                    <a class="sub-link" href="{{ route('back.order.index').'?type='.'Delivered' }}">
                        <span class="sub-item">{{ __('Delivered Orders') }}</span>
                    </a>
                </li>
                <li class="{{request()->input('type') == 'Canceled' ? 'active' : ''}}">
                    <a class="sub-link" href="{{ route('back.order.index').'?type='.'Canceled' }}">
                        <span class="sub-item">{{ __('Canceled Orders') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <li class="nav-item">
        <a data-toggle="collapse" href="#category">
            <i class="fas fa-list-alt"></i>
            <p>{{ __('Manage Categories') }}</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="category">
            <ul class="nav nav-collapse">
                <li>
                    <a class="sub-link" href="{{ route('back.category.index') }}">
                        <span class="sub-item">{{ __('Categories') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.subcategory.index') }}">
                        <span class="sub-item">{{ __('Sub categories') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.childcategory.index') }}">
                        <span class="sub-item">{{ __('Child categories') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <li class="nav-item">
        <a  href="{{ route('back.transaction.index') }}">
            <i class="fas fa-random"></i>
          <p>{{ __('Transactions') }}</p>
        </a>
    </li>

    <li class="nav-item">
        <a data-toggle="collapse" href="#ecommerce">
            <i class="fas fa-newspaper"></i>
            <p>{{ __('Ecommerce') }}</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="ecommerce">
            <ul class="nav nav-collapse">
                <li>
                    <a class="sub-link" href="{{ route('back.code.index') }}">
                      <span class="sub-item">{{ __('Set Coupons') }}</span></a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.shipping.index') }}">
                        <span class="sub-item">{{ __('Free Delivery') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.state.index') }}">
                        <span class="sub-item">{{ __('State Charge') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.tax.index') }}">
                        <span class="sub-item">{{ __('Tax') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.currency.index') }}">
                        <span class="sub-item">{{ __('Currency') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.setting.payment') }}">
                        <span class="sub-item">{{ __('Payment Methods') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <li class="nav-item">
        <a href="{{ route('back.user.index') }}">
          <i class="fas fa-users"></i>
          <p>{{ __('Customer List') }}</p></a>
    </li>

    <li class="nav-item {{ request()->is('admin/checkout-message*') || request()->is('admin/vendor-announcements*') ? 'active submenu' : '' }}">
        <a data-toggle="collapse" href="#messages_menu">
            <i class="fas fa-comment-alt"></i>
            <p>{{ __('Messages') }}</p>
            <span class="caret"></span>
        </a>
        <div class="collapse {{ request()->is('admin/checkout-message*') || request()->is('admin/vendor-announcements*') ? 'show' : '' }}" id="messages_menu">
            <ul class="nav nav-collapse">
                <li class="{{ request()->is('admin/checkout-message*') ? 'active' : '' }}">
                    <a class="sub-link" href="{{ route('back.checkout.message') }}">
                        <span class="sub-item">{{ __('Popup Messages') }}</span>
                    </a>
                </li>
                <li class="{{ request()->is('admin/vendor-announcements*') ? 'active' : '' }}">
                    <a class="sub-link" href="{{ route('back.announcement.index') }}">
                        <span class="sub-item">{{ __('Announcement for All Vendors') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <li class="nav-item">
        <a href="{{ route('back.ticket.index') }}">
            <i class="fas fa-comments"></i>
          <p>{{ __('Manages Tickets') }}</p></a>
    </li>
    
    <li class="nav-item">
        <a data-toggle="collapse" href="#content">
            <i class="fas fa-tasks"></i>
            <p>{{ __('Manage Site') }}</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="content">
            <ul class="nav nav-collapse">
                <li>
                    <a class="sub-link" href="{{ route('back.setting.system') }}">
                        <span class="sub-item">{{ __('General Settings') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.menu.index') }}">
                        <span class="sub-item">{{ __('Menu Builder') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.homePage') }}">
                        <span class="sub-item">{{ __('Home Page') }}</span>
                    </a>
                </li>
                <li>
                    <a  class="sub-link" href="{{ route('back.slider.index') }}">
                        <span class="sub-item">{{ __('Sliders') }}</span>
                    </a>
                </li>

                <li>
                    <a class="sub-link" href="{{ route('back.service.index') }}">
                        <span class="sub-item">{{ __('Services') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.setting.section') }}">
                        <span class="sub-item">{{ __('Visibility') }}</span>
                    </a>
                </li>

                <li>
                    <a class="sub-link" href="{{ route('back.setting.social') }}">
                        <span class="sub-item">{{ __('Social Login') }}</span>
                    </a>
                </li>

                <li>
                    <a class="sub-link" href="{{ route('back.setting.email') }}">
                        <span class="sub-item">{{ __('Email Settings') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.setting.sms') }}">
                        <span class="sub-item">{{ __('SMS Settings') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.setting.whatsapp') }}">
                        <span class="sub-item">{{ __('WhatsApp Settings') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.subscribers.announcement') }}">
                      <span class="sub-item">{{ __('Announcement') }}</span></a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.cookie.alert') }}">
                      <span class="sub-item">{{ __('Cookies Alert') }}</span></a>
                </li>

                <li>
                    <a class="sub-link" href="{{ route('back.setting.maintainance') }}">
                      <span class="sub-item">{{ __('Maintainance') }}</span></a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('admin.sitemap.index') }}">
                      <span class="sub-item">{{ __('Sitemap') }}</span></a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.language.index') }}">
                      <span class="sub-item">{{ __('Language') }}</span></a>
                </li>
            </ul>
        </div>
    </li>

    <li class="nav-item">
        <a data-toggle="collapse" href="#faqs">
            <i class="fas fa-question-circle"></i>
            <p>{{ __('Manage Faqs') }}</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="faqs">
            <ul class="nav nav-collapse">
                <li>
                    <a class="sub-link" href="{{ route('back.fcategory.index') }}">
                        <span class="sub-item">{{ __('Categories') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.faq.index') }}">
                        <span class="sub-item">{{ __('Faqs') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <li class="nav-item">
        <a data-toggle="collapse" href="#post">
            <i class="fas fa-rss-square"></i>
            <p>{{ __('Manage Blogs') }}</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="post">
            <ul class="nav nav-collapse">
                <li>
                    <a class="sub-link" href="{{ route('back.bcategory.index') }}">
                        <span class="sub-item">{{ __('Categories') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.post.index') }}">
                        <span class="sub-item">{{ __('Blogs') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
        <a href="{{ route('back.page.index') }}">
            <i class="fas fa-book"></i>
            <p>{{ __('Manages Pages') }}</p>
        </a>
    </li>


    <li class="nav-item">
        <a href="{{ route('back.subscribers.index') }}">
            <i class="fab fa-telegram-plane"></i>
            <p>{{ __('Subscribers List') }}</p>
        </a>
    </li>

    <li class="nav-item">
        <a data-toggle="collapse" href="#user">
            <i class="far fa-user"></i>
            <p>{{ __('System User') }}</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="user">
            <ul class="nav nav-collapse">
                <li>
                    <a class="sub-link" href="{{ route('back.role.index') }}">
                        <span class="sub-item">{{ __('Role') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.staff.index') }}">
                        <span class="sub-item">{{ __('System User') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
        <a data-toggle="collapse" href="#backup">
            <i class="fas fa-hdd"></i>
            <p>{{ __('System Backup') }}</p>
            <span class="caret"></span>
        </a>
        <div class="collapse" id="backup">
            <ul class="nav nav-collapse">
                <li>
                    <a class="sub-link" href="{{ route('back.system.backup') }}">
                        <span class="sub-item">{{ __('System Backup') }}</span>
                    </a>
                </li>
                <li>
                    <a class="sub-link" href="{{ route('back.database.backup') }}">
                        <span class="sub-item">{{ __('Database Backup') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </li>

    <li class="nav-item">
        <a href="{{ route('front.cache.clear') }}">
            <i class="fas fa-broom"></i>
            <p>{{ __('Cache Clear') }}</p>
        </a>
    </li>

</ul>
