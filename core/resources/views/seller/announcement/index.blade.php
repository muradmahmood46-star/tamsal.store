@extends('master.seller')

@section('content')
<div class="container-fluid">

    <!-- Notice Board Header -->
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #ffffff;">
        <div class="card-body p-4">
            <div class="d-md-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <div class="d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 56px; height: 56px; min-width: 56px; border-radius: 14px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #ffffff; font-size: 24px;">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <div>
                        <h3 class="mb-1 font-weight-bold text-white" style="font-size: 22px; letter-spacing: -0.5px;">
                            {{ __('Vendor Notice Board') }}
                        </h3>
                        <p class="mb-0 text-white-50" style="font-size: 13.5px;">
                            {{ __('Official announcements, operational updates, and policy notifications from Tamsal Admin.') }}
                        </p>
                    </div>
                </div>
                <div>
                    <span class="badge px-3 py-2 text-white font-weight-bold shadow-sm" style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(4px); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 30px; font-size: 12px;">
                        <i class="fas fa-broadcast-tower text-warning mr-1"></i> {{ __('Official Admin Broadcast') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    <!-- Announcements List Stream -->
    <div class="row">
        <div class="col-lg-12">
            @forelse($announcements as $item)
            @php
                $themeColor = '#3b82f6';
                $badgeBg = '#dbeafe';
                $badgeTextColor = '#1e40af';
                $badgeIcon = 'fa-info-circle';
                $badgeLabel = 'Information';
                $cardBorderColor = '#93c5fd';

                if ($item->badge_type == 'warning') {
                    $themeColor = '#f59e0b';
                    $badgeBg = '#fef3c7';
                    $badgeTextColor = '#92400e';
                    $badgeIcon = 'fa-exclamation-triangle';
                    $badgeLabel = 'Important Notice';
                    $cardBorderColor = '#fcd34d';
                } elseif ($item->badge_type == 'danger') {
                    $themeColor = '#ef4444';
                    $badgeBg = '#fee2e2';
                    $badgeTextColor = '#991b1b';
                    $badgeIcon = 'fa-exclamation-circle';
                    $badgeLabel = 'Urgent Alert';
                    $cardBorderColor = '#fca5a5';
                } elseif ($item->badge_type == 'success') {
                    $themeColor = '#10b981';
                    $badgeBg = '#d1fae5';
                    $badgeTextColor = '#065f46';
                    $badgeIcon = 'fa-check-circle';
                    $badgeLabel = 'Update / Good News';
                    $cardBorderColor = '#6ee7b7';
                } elseif ($item->badge_type == 'primary') {
                    $themeColor = '#6366f1';
                    $badgeBg = '#e0e7ff';
                    $badgeTextColor = '#3730a3';
                    $badgeIcon = 'fa-bullhorn';
                    $badgeLabel = 'Official Broadcast';
                    $cardBorderColor = '#a5b4fc';
                }
            @endphp
            <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px; border-left: 6px solid {{ $themeColor }} !important; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; background: #ffffff;">
                
                <!-- Notice Header Bar -->
                <div class="card-header bg-white border-0 pt-3 pb-2 px-3 px-md-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between" style="gap: 10px;">
                        
                        <!-- Priority Pill & Official Flag -->
                        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                            <span class="badge px-3 py-1 font-weight-bold" style="background-color: {{ $badgeBg }}; color: {{ $badgeTextColor }}; font-size: 12px; border-radius: 20px; letter-spacing: 0.3px;">
                                <i class="fas {{ $badgeIcon }} mr-1"></i> {{ __($badgeLabel) }}
                            </span>
                            <span class="text-muted small d-none d-sm-inline" style="font-size: 12px;">
                                <i class="fas fa-thumbtack text-danger mr-1"></i> {{ __('Official Notice') }}
                            </span>
                        </div>

                        <!-- Date & Time Info -->
                        <div class="text-muted small d-flex align-items-center" style="font-size: 12.5px; background: #f8fafc; padding: 4px 12px; border-radius: 20px; border: 1px solid #edf2f7;">
                            <i class="far fa-calendar-alt text-primary mr-1"></i>
                            <span class="font-weight-600 text-dark mr-2">{{ $item->created_at ? $item->created_at->format('M d, Y') : '-' }}</span>
                            <span class="text-muted mr-2">•</span>
                            <i class="far fa-clock text-secondary mr-1"></i>
                            <span class="text-dark">{{ $item->created_at ? $item->created_at->format('h:i A') : '' }}</span>
                            @if($item->created_at)
                            <span class="text-muted ml-1 font-italic">({{ $item->created_at->diffForHumans() }})</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Notice Body -->
                <div class="card-body px-3 px-md-4 pt-2 pb-4">
                    <!-- Title -->
                    <h4 class="font-weight-bold text-dark mb-3" style="font-size: 18px; line-height: 1.45; color: #0f172a !important;">
                        {{ $item->title }}
                    </h4>

                    <!-- Message Container Box -->
                    <div class="p-3 p-md-4" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; position: relative;">
                        <div style="color: #334155; font-size: 14.5px; line-height: 1.55; word-break: break-word; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">{!! nl2br(e($item->message)) !!}</div>
                    </div>
                </div>

                <!-- Notice Footer -->
                <div class="card-footer bg-white border-top py-2 px-3 px-md-4 d-flex align-items-center justify-content-between" style="border-color: #f1f5f9 !important;">
                    <div class="text-muted small" style="font-size: 12px;">
                        <i class="fas fa-shield-alt text-success mr-1"></i> {{ __('Issued by Tamsal Administration') }}
                    </div>
                    <div class="text-muted small" style="font-size: 11.5px;">
                        <i class="fas fa-check-double text-primary mr-1"></i> {{ __('Verified Broadcast') }}
                    </div>
                </div>

            </div>
            @empty
            <div class="card shadow-sm border-0 text-center py-5" style="border-radius: 12px; background: #ffffff;">
                <div class="card-body py-5">
                    <div class="mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background-color: #f1f5f9; color: #94a3b8;">
                            <i class="fas fa-bullhorn fa-2x"></i>
                        </span>
                    </div>
                    <h5 class="font-weight-bold text-dark mb-2" style="font-size: 18px;">{{ __('No Announcements Available') }}</h5>
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
@endsection
