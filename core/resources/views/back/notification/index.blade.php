@php
    App\Models\Notification::ensureColumns();
    $allNotifs = App\Models\Notification::with(['user', 'order', 'deposit.seller', 'deposit.user'])->orderby('id','desc')->get();
    $notifCount = $allNotifs->count();
@endphp

@if($notifCount > 0)
    <div class="notf-header d-flex align-items-center justify-content-between px-3 py-2 border-bottom" style="background: #f8fafc;">
        <span class="font-weight-bold text-dark" style="font-size: 13px;">
            <i class="fas fa-bell text-primary mr-1"></i> {{ __('Notifications') }} ({{ $notifCount }})
        </span>
        <a class="badge badge-light border text-danger px-2 py-1" id="clear-notf" data-href="{{ route('back.notifications.clear') }}" href="javascript:;" style="font-size: 11px; cursor: pointer; text-decoration: none;">
            <i class="fas fa-trash-alt mr-1"></i> {{ __('Clear All') }}
        </a>
    </div>

    <div class="notf-list-scrollable" style="max-height: 280px; overflow-y: auto; -webkit-overflow-scrolling: touch;">
        @foreach($allNotifs as $notf)
            @if($notf->deposit_id != null)
                <a class="dropdown-item d-flex align-items-center py-2 px-3 border-bottom text-wrap" href="{{ route('back.deposit_request.index', ['search' => ($notf->deposit && $notf->deposit->txn_id) ? $notf->deposit->txn_id : '']) }}" style="transition: background 0.15s; text-decoration: none;">
                    <div class="mr-3 flex-shrink-0">
                        <div class="icon-circle bg-warning text-white d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; border-radius: 50%;">
                            <i class="fas fa-hand-holding-usd" style="font-size: 14px;"></i>
                        </div>
                    </div>
                    <div style="min-width: 0; flex-grow: 1;">
                        <div class="font-weight-bold text-dark" style="font-size: 12.5px; line-height: 1.3;">
                            {{ __('New deposit request received.') }}
                        </div>
                        @if($notf->deposit)
                            <div class="text-muted text-truncate" style="font-size: 11.5px;">
                                @if($notf->deposit->seller && $notf->deposit->seller->shop_name)
                                    <span class="font-weight-600 text-dark">{{ $notf->deposit->seller->shop_name }}</span>
                                @elseif($notf->deposit->user)
                                    <span class="font-weight-600 text-dark">{{ trim($notf->deposit->user->first_name . ' ' . $notf->deposit->user->last_name) ?: $notf->deposit->user->email }}</span>
                                @endif
                                &bull; <span class="text-primary font-weight-bold">{{ \App\Helpers\PriceHelper::adminCurrency() }} {{ number_format((float)$notf->deposit->amount, 2) }}</span>
                            </div>
                        @endif
                        <div class="small text-muted" style="font-size: 10.5px; margin-top: 2px;">
                            <i class="fas fa-clock mr-1"></i> {{ $notf->created_at ? $notf->created_at->diffForHumans() : '' }}
                        </div>
                    </div>
                </a>
            @endif
            @if($notf->user_id != null)
                @php
                    $userLink = ($notf->user && $notf->user->id) ? route('back.user.show', $notf->user->id) : route('back.user.index');
                @endphp
                <a class="dropdown-item d-flex align-items-center py-2 px-3 border-bottom text-wrap" href="{{ $userLink }}" style="transition: background 0.15s; text-decoration: none;">
                    <div class="mr-3 flex-shrink-0">
                        <div class="icon-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; border-radius: 50%;">
                            <i class="fas fa-user-plus" style="font-size: 14px;"></i>
                        </div>
                    </div>
                    <div style="min-width: 0; flex-grow: 1;">
                        <div class="font-weight-bold text-dark" style="font-size: 12.5px; line-height: 1.3;">
                            {{ __('A new user has registered.') }}
                        </div>
                        @if($notf->user && ($notf->user->first_name || $notf->user->email))
                            <div class="text-muted text-truncate" style="font-size: 11.5px;">
                                {{ trim($notf->user->first_name . ' ' . $notf->user->last_name) ?: $notf->user->email }}
                            </div>
                        @endif
                        <div class="small text-muted" style="font-size: 10.5px; margin-top: 2px;">
                            <i class="fas fa-clock mr-1"></i> {{ $notf->created_at ? $notf->created_at->diffForHumans() : '' }}
                        </div>
                    </div>
                </a>
            @endif
            @if($notf->order_id != null)
                @php
                    $orderLink = ($notf->order && $notf->order->id) ? route('back.order.invoice', $notf->order->id) : route('back.order.index');
                @endphp
                <a class="dropdown-item d-flex align-items-center py-2 px-3 border-bottom text-wrap" href="{{ $orderLink }}" style="transition: background 0.15s; text-decoration: none;">
                    <div class="mr-3 flex-shrink-0">
                        <div class="icon-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; border-radius: 50%;">
                            <i class="fas fa-shopping-cart" style="font-size: 14px;"></i>
                        </div>
                    </div>
                    <div style="min-width: 0; flex-grow: 1;">
                        <div class="font-weight-bold text-dark" style="font-size: 12.5px; line-height: 1.3;">
                            {{ __('You have received a new order.') }}
                        </div>
                        @if($notf->order && $notf->order->transaction_number)
                            <div class="text-primary font-weight-bold" style="font-size: 11.5px;">
                                #{{ $notf->order->transaction_number }}
                            </div>
                        @endif
                        <div class="small text-muted" style="font-size: 10.5px; margin-top: 2px;">
                            <i class="fas fa-clock mr-1"></i> {{ $notf->created_at ? $notf->created_at->diffForHumans() : '' }}
                        </div>
                    </div>
                </a>
            @endif
        @endforeach
    </div>

    <a class="dropdown-item text-center font-weight-bold text-primary py-2 bg-light border-top" href="{{ route('back.view.notification') }}" style="font-size: 12px; display: block; text-decoration: none;">
        {{ __('View All Notifications') }} <i class="fas fa-arrow-right ml-1"></i>
    </a>
@else
    <div class="notf-header d-flex align-items-center justify-content-between px-3 py-2 border-bottom" style="background: #f8fafc;">
        <span class="font-weight-bold text-dark" style="font-size: 13px;">
            <i class="fas fa-bell text-primary mr-1"></i> {{ __('Notifications') }}
        </span>
    </div>
    <div class="text-center py-4 px-3 text-muted">
        <i class="fas fa-bell-slash fa-2x mb-2 text-secondary d-block"></i>
        <span style="font-size: 12.5px;">{{ __('No new notifications') }}</span>
    </div>
@endif
