@extends('master.seller')

@section('content')
<div class="container-fluid">

    <!-- Notice Board Hero Banner -->
    <div class="card mb-4 border-0 shadow-lg announcement-hero-banner" style="border-radius: 16px; background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #1e293b 100%); color: #ffffff; position: relative; overflow: hidden;">
        <!-- Decorative Ambient Light Circles -->
        <div style="position: absolute; top: -40px; right: -40px; width: 180px; height: 180px; background: radial-gradient(circle, rgba(245, 158, 11, 0.3) 0%, transparent 70%); border-radius: 50%; pointer-events: none;"></div>
        <div style="position: absolute; bottom: -50px; left: 10%; width: 200px; height: 200px; background: radial-gradient(circle, rgba(99, 102, 241, 0.25) 0%, transparent 70%); border-radius: 50%; pointer-events: none;"></div>
        
        <div class="card-body p-4 p-md-4 position-relative" style="z-index: 2;">
            <div class="d-md-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <div class="d-flex align-items-center justify-content-center mr-3 shadow-lg announcement-hero-icon" style="width: 60px; height: 60px; min-width: 60px; border-radius: 16px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #ffffff; font-size: 26px; box-shadow: 0 8px 25px rgba(245, 158, 11, 0.45) !important;">
                        <i class="fas fa-bullhorn animated-horn"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                            <h3 class="mb-0 font-weight-bold text-white" style="font-size: 22px; letter-spacing: -0.5px;">
                                {{ __('Vendor Notice Board') }}
                            </h3>
                            <span class="badge px-2 py-1 font-weight-bold" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: #fff; font-size: 11px; border-radius: 20px; box-shadow: 0 2px 10px rgba(239, 68, 68, 0.4);">
                                <span class="live-pulse-dot mr-1"></span> {{ __('LIVE BROADCASTS') }}
                            </span>
                        </div>
                        <p class="mb-0 text-white-50 mt-1" style="font-size: 13.5px;">
                            {{ __('Official announcements, operational updates, and policy notifications from Tamsal Administration.') }}
                        </p>
                    </div>
                </div>
                <div>
                    <span class="badge px-3 py-2 text-white font-weight-bold shadow-sm d-inline-flex align-items-center" style="background: rgba(255, 255, 255, 0.12); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.22); border-radius: 30px; font-size: 12.5px;">
                        <i class="fas fa-broadcast-tower text-warning mr-2"></i> {{ __('Admin to All Vendors') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    <!-- Announcements List Stream -->
    <div class="row">
        <div class="col-lg-12">
            @forelse($announcements as $key => $item)
            @php
                // Vibrant color schemes per category
                $primaryColor = '#3b82f6';
                $gradientHeader = 'linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)';
                $badgeBg = '#eff6ff';
                $badgeTextColor = '#1d4ed8';
                $badgeBorder = '#bfdbfe';
                $badgeIcon = 'fa-info-circle';
                $badgeLabel = 'Information';
                $contentBg = 'linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%)';
                $contentBorder = '#e2e8f0';
                $iconGlow = 'rgba(59, 130, 246, 0.25)';

                if ($item->badge_type == 'warning') {
                    $primaryColor = '#f59e0b';
                    $gradientHeader = 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)';
                    $badgeBg = '#fffbeb';
                    $badgeTextColor = '#b45309';
                    $badgeBorder = '#fde68a';
                    $badgeIcon = 'fa-exclamation-triangle';
                    $badgeLabel = 'Important Notice';
                    $contentBg = 'linear-gradient(180deg, #fffdf5 0%, #fffbeb 100%)';
                    $contentBorder = '#fef3c7';
                    $iconGlow = 'rgba(245, 158, 11, 0.3)';
                } elseif ($item->badge_type == 'danger') {
                    $primaryColor = '#ef4444';
                    $gradientHeader = 'linear-gradient(135deg, #ef4444 0%, #b91c1c 100%)';
                    $badgeBg = '#fef2f2';
                    $badgeTextColor = '#b91c1c';
                    $badgeBorder = '#fecaca';
                    $badgeIcon = 'fa-bell';
                    $badgeLabel = 'Urgent Alert';
                    $contentBg = 'linear-gradient(180deg, #fffafa 0%, #fef2f2 100%)';
                    $contentBorder = '#fee2e2';
                    $iconGlow = 'rgba(239, 68, 68, 0.35)';
                } elseif ($item->badge_type == 'success') {
                    $primaryColor = '#10b981';
                    $gradientHeader = 'linear-gradient(135deg, #10b981 0%, #047857 100%)';
                    $badgeBg = '#ecfdf5';
                    $badgeTextColor = '#047857';
                    $badgeBorder = '#a7f3d0';
                    $badgeIcon = 'fa-check-circle';
                    $badgeLabel = 'Good News / Update';
                    $contentBg = 'linear-gradient(180deg, #f7fdfa 0%, #ecfdf5 100%)';
                    $contentBorder = '#d1fae5';
                    $iconGlow = 'rgba(16, 185, 129, 0.25)';
                } elseif ($item->badge_type == 'primary') {
                    $primaryColor = '#6366f1';
                    $gradientHeader = 'linear-gradient(135deg, #6366f1 0%, #4338ca 100%)';
                    $badgeBg = '#eef2ff';
                    $badgeTextColor = '#4338ca';
                    $badgeBorder = '#c7d2fe';
                    $badgeIcon = 'fa-bullhorn';
                    $badgeLabel = 'Official Broadcast';
                    $contentBg = 'linear-gradient(180deg, #fafafe 0%, #eef2ff 100%)';
                    $contentBorder = '#e0e7ff';
                    $iconGlow = 'rgba(99, 102, 241, 0.3)';
                }
            @endphp
            <div class="card mb-4 border-0 announcement-card shadow-sm" style="border-radius: 14px; overflow: hidden; background: #ffffff; animation: fadeInUp 0.4s ease forwards; animation-delay: {{ $key * 0.08 }}s;">
                
                <!-- Colorful Top Accent Banner Strip -->
                <div style="height: 4px; background: {{ $gradientHeader }};"></div>

                <!-- Notice Header Row -->
                <div class="card-header bg-white border-0 pt-3 pb-2 px-3 px-md-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap: 10px;">
                        
                        <!-- Category Badge with Pulse Indicator -->
                        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                            <span class="badge px-3 py-1 font-weight-bold d-inline-flex align-items-center category-pill" style="background-color: {{ $badgeBg }}; color: {{ $badgeTextColor }}; border: 1px solid {{ $badgeBorder }}; font-size: 12px; border-radius: 20px; letter-spacing: 0.3px;">
                                <i class="fas {{ $badgeIcon }} mr-1"></i> {{ __($badgeLabel) }}
                            </span>
                            <span class="badge badge-light border text-muted px-2 py-1 small" style="font-size: 11.5px; border-radius: 6px;">
                                <i class="fas fa-thumbtack text-danger mr-1"></i> {{ __('Official Announcement') }}
                            </span>
                        </div>

                        <!-- Date & Time Info Pill -->
                        <div class="announcement-timestamp-badge d-flex align-items-center px-3 py-1 rounded-pill">
                            <i class="far fa-calendar-alt text-primary mr-1"></i>
                            <span class="font-weight-600 text-dark mr-2" style="font-size: 12.5px;">{{ $item->created_at ? $item->created_at->format('M d, Y') : '-' }}</span>
                            <span class="text-muted mr-2">•</span>
                            <i class="far fa-clock text-secondary mr-1"></i>
                            <span class="text-dark" style="font-size: 12.5px;">{{ $item->created_at ? $item->created_at->format('h:i A') : '' }}</span>
                            @if($item->created_at)
                            <span class="text-muted ml-1 font-italic small">({{ $item->created_at->diffForHumans() }})</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Notice Body -->
                <div class="card-body px-3 px-md-4 pt-1 pb-3">
                    <!-- Title -->
                    <div class="d-flex align-items-baseline mb-2">
                        <span class="announcement-bullet mr-2" style="background-color: {{ $primaryColor }};"></span>
                        <h4 class="font-weight-bold text-dark mb-0" style="font-size: 18px; line-height: 1.45; color: #0f172a !important;">
                            {{ $item->title }}
                        </h4>
                    </div>

                    <!-- Message Container Box with Subtle Tint & Left Accent Border -->
                    <div class="p-3 p-md-4 rounded-lg mt-3 announcement-message-box" style="background: {{ $contentBg }}; border: 1px solid {{ $contentBorder }}; border-left: 5px solid {{ $primaryColor }} !important; border-radius: 12px; position: relative; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                        
                        <!-- Quotation Icon Graphic Watermark -->
                        <div style="position: absolute; right: 15px; top: 12px; opacity: 0.12; font-size: 32px; color: {{ $primaryColor }}; pointer-events: none;">
                            <i class="fas fa-quote-right"></i>
                        </div>

                        <!-- Message Text with Natural Spacing & Clean Typography -->
                        <div class="announcement-text" style="color: #1e293b; font-size: 14.5px; line-height: 1.6; word-break: break-word; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                            {!! nl2br(e($item->message)) !!}
                        </div>
                    </div>
                </div>

                <!-- Notice Footer -->
                <div class="card-footer bg-white border-top py-2 px-3 px-md-4 d-flex align-items-center justify-content-between" style="border-color: #f1f5f9 !important;">
                    <div class="text-muted small d-flex align-items-center" style="font-size: 12px;">
                        <i class="fas fa-shield-alt text-success mr-1"></i> 
                        <span>{{ __('Broadcasted by Tamsal Administration') }}</span>
                    </div>
                    <div class="text-muted small d-flex align-items-center" style="font-size: 11.5px;">
                        <span class="status-indicator-dot mr-1" style="background-color: {{ $primaryColor }};"></span>
                        <span>{{ __('Verified Notice') }}</span>
                    </div>
                </div>

            </div>
            @empty
            <div class="card shadow-sm border-0 text-center py-5" style="border-radius: 16px; background: #ffffff;">
                <div class="card-body py-5">
                    <div class="mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 85px; height: 85px; background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%); color: #64748b;">
                            <i class="fas fa-bullhorn fa-2x"></i>
                        </span>
                    </div>
                    <h5 class="font-weight-bold text-dark mb-2" style="font-size: 19px;">{{ __('No Announcements Available') }}</h5>
                    <p class="text-muted small mb-0" style="max-width: 440px; margin: 0 auto; line-height: 1.6;">
                        {{ __('There are currently no active announcements from Administration. When official notices or updates are posted, they will appear here on your notice board.') }}
                    </p>
                </div>
            </div>
            @endforelse

            @if($announcements->hasPages())
            <div class="d-flex justify-content-center justify-content-md-end mt-4 mb-3">
                {{ $announcements->links() }}
            </div>
            @endif
        </div>
    </div>

</div>

<style>
/* Animations & Visual Enhancements */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(12px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulseGlow {
    0% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
    }
    70% {
        transform: scale(1);
        box-shadow: 0 0 0 6px rgba(239, 68, 68, 0);
    }
    100% {
        transform: scale(0.95);
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
    }
}

@keyframes hornWiggle {
    0%, 100% { transform: rotate(0deg); }
    20% { transform: rotate(-10deg); }
    40% { transform: rotate(10deg); }
    60% { transform: rotate(-5deg); }
    80% { transform: rotate(5deg); }
}

.animated-horn {
    display: inline-block;
    animation: hornWiggle 3s infinite ease-in-out;
}

.live-pulse-dot {
    display: inline-block;
    width: 7px;
    height: 7px;
    background-color: #ffffff;
    border-radius: 50%;
    animation: pulseGlow 1.8s infinite;
}

.announcement-card {
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    border: 1px solid rgba(0,0,0,0.06) !important;
}

.announcement-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
}

.announcement-bullet {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    min-width: 8px;
}

.announcement-timestamp-badge {
    background: #f8fafc;
    border: 1px solid #edf2f7;
}

.status-indicator-dot {
    display: inline-block;
    width: 6px;
    height: 6px;
    border-radius: 50%;
}

.category-pill {
    transition: transform 0.2s ease;
}

.announcement-card:hover .category-pill {
    transform: scale(1.03);
}
</style>
@endsection
