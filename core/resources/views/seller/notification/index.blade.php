@php
    $notifCount = $notifications->count();
@endphp

@if($notifCount > 0)
    <div class="notf-header d-flex align-items-center justify-content-between px-3 py-2 border-bottom" style="background: #f8fafc;">
        <span class="font-weight-bold text-dark" style="font-size: 13px;">
            <i class="fas fa-bell text-primary mr-1"></i> {{ __('Notifications') }} ({{ $notifCount }})
        </span>
        <a class="badge badge-light border text-danger px-2 py-1" id="clear-vendor-notf" data-href="{{ route('seller.notifications.clear') }}" href="javascript:;" style="font-size: 11px; cursor: pointer; text-decoration: none;">
            <i class="fas fa-trash-alt mr-1"></i> {{ __('Clear All') }}
        </a>
    </div>

    <div class="notf-list-scrollable" style="max-height: 320px; overflow-y: auto; -webkit-overflow-scrolling: touch;">
        @foreach($notifications as $notf)
            @php
                $badgeBg = 'bg-primary';
                if ($notf->badge_color == 'success') $badgeBg = 'bg-success';
                elseif ($notf->badge_color == 'warning') $badgeBg = 'bg-warning text-dark';
                elseif ($notf->badge_color == 'danger') $badgeBg = 'bg-danger';
                elseif ($notf->badge_color == 'info') $badgeBg = 'bg-info';
            @endphp
            <a class="dropdown-item d-flex align-items-start py-2 px-3 border-bottom text-wrap" href="{{ $notf->link ?: 'javascript:;' }}" style="transition: background 0.15s; text-decoration: none;">
                <div class="mr-3 flex-shrink-0 mt-1">
                    <div class="icon-circle {{ $badgeBg }} text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 36px; height: 36px; border-radius: 50%;">
                        <i class="{{ $notf->icon ?: 'fas fa-bell' }}" style="font-size: 14px;"></i>
                    </div>
                </div>
                <div style="min-width: 0; flex-grow: 1;">
                    <div class="font-weight-bold text-dark" style="font-size: 12.5px; line-height: 1.3;">
                        {{ $notf->title }}
                    </div>
                    @if($notf->message)
                        <div class="text-muted small mt-1" style="font-size: 11.5px; line-height: 1.35;">
                            {{ Str::limit($notf->message, 120) }}
                        </div>
                    @endif
                    <div class="small text-muted" style="font-size: 10.5px; margin-top: 3px;">
                        <i class="fas fa-clock mr-1"></i> {{ $notf->created_at ? $notf->created_at->diffForHumans() : '' }}
                    </div>
                </div>
            </a>
        @endforeach
    </div>
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
