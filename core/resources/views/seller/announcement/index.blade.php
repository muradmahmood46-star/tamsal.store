@extends('master.seller')

@section('content')
<div class="container-fluid">

    <!-- Page Title & Header -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body py-3">
            <div class="d-sm-flex align-items-center justify-content-between">
                <div>
                    <h3 class="mb-1 bc-title font-weight-bold text-dark">
                        <i class="fas fa-bullhorn text-warning mr-2"></i><b>{{ __('Announcements') }}</b>
                    </h3>
                    <p class="text-muted small mb-0">{{ __('Official notices, policy updates, and important broadcasts from Tamsal Admin.') }}</p>
                </div>
                <div class="mt-2 mt-sm-0">
                    <span class="badge badge-light border px-3 py-2 text-dark font-weight-bold" style="font-size: 12px; border-radius: 8px;">
                        <i class="fas fa-broadcast-tower text-primary mr-1"></i> {{ __('Broadcast for All Vendors') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    <!-- Announcements Cards Stream -->
    <div class="row">
        <div class="col-lg-12">
            @forelse($announcements as $item)
            @php
                $borderLeftColor = '#3b82f6';
                $badgeClass = 'badge-info text-white';
                $badgeIcon = 'fa-info-circle';
                $badgeLabel = 'General Announcement';
                $cardBg = '#ffffff';

                if ($item->badge_type == 'warning') {
                    $borderLeftColor = '#f59e0b';
                    $badgeClass = 'badge-warning text-dark';
                    $badgeIcon = 'fa-exclamation-triangle';
                    $badgeLabel = 'Important Notice';
                } elseif ($item->badge_type == 'danger') {
                    $borderLeftColor = '#ef4444';
                    $badgeClass = 'badge-danger text-white';
                    $badgeIcon = 'fa-exclamation-circle';
                    $badgeLabel = 'Urgent Alert';
                } elseif ($item->badge_type == 'success') {
                    $borderLeftColor = '#10b981';
                    $badgeClass = 'badge-success text-white';
                    $badgeIcon = 'fa-check-circle';
                    $badgeLabel = 'Good News / Update';
                } elseif ($item->badge_type == 'primary') {
                    $borderLeftColor = '#6366f1';
                    $badgeClass = 'badge-primary text-white';
                    $badgeIcon = 'fa-bullhorn';
                    $badgeLabel = 'Official Broadcast';
                }
            @endphp
            <div class="card mb-3 shadow-sm border-0" style="border-left: 5px solid {{ $borderLeftColor }} !important; border-radius: 8px; transition: all 0.2s ease;">
                <div class="card-body p-3 p-md-4">
                    <!-- Top row: Category Badge and Timestamp -->
                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-2" style="gap: 8px;">
                        <div>
                            <span class="badge {{ $badgeClass }} px-2 py-1 font-weight-bold" style="font-size: 11.5px; border-radius: 6px;">
                                <i class="fas {{ $badgeIcon }} mr-1"></i> {{ __($badgeLabel) }}
                            </span>
                        </div>
                        <div class="text-muted small d-flex align-items-center" style="font-size: 12px;">
                            <i class="far fa-calendar-alt text-secondary mr-1"></i>
                            <span class="font-weight-600 text-dark mr-2">{{ $item->created_at ? $item->created_at->format('M d, Y') : '' }}</span>
                            <i class="far fa-clock text-secondary mr-1"></i>
                            <span>{{ $item->created_at ? $item->created_at->format('h:i A') : '' }}</span>
                            <span class="text-muted ml-1 font-italic">({{ $item->created_at ? $item->created_at->diffForHumans() : '' }})</span>
                        </div>
                    </div>

                    <!-- Announcement Title -->
                    <h4 class="font-weight-bold text-dark mb-2" style="font-size: 17px; line-height: 1.4;">
                        {{ $item->title }}
                    </h4>

                    <hr class="my-2" style="opacity: 0.15;">

                    <!-- Announcement Message Body -->
                    <div class="announcement-content text-secondary mt-3" style="font-size: 14.5px; line-height: 1.7; white-space: pre-line; word-break: break-word;">{!! nl2br(e($item->message)) !!}</div>
                </div>
            </div>
            @empty
            <div class="card shadow-sm border-0 text-center py-5">
                <div class="card-body py-5">
                    <div class="mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle text-muted" style="width: 80px; height: 80px;">
                            <i class="fas fa-bullhorn fa-2x text-muted opacity-50"></i>
                        </span>
                    </div>
                    <h5 class="font-weight-bold text-dark mb-1">{{ __('No Announcements Yet') }}</h5>
                    <p class="text-muted small mb-0" style="max-width: 420px; margin: 0 auto;">
                        {{ __('There are currently no official announcements posted by the administration. Any future updates, notices, and policy changes will be displayed here.') }}
                    </p>
                </div>
            </div>
            @endforelse

            @if($announcements->hasPages())
            <div class="d-flex justify-content-end mt-3">
                {{ $announcements->links() }}
            </div>
            @endif
        </div>
    </div>

</div>
@endsection
