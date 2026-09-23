@extends('master.back')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 bc-title">
                    <i class="fas fa-bullhorn text-primary mr-2"></i><b>{{ __('Vendor Announcements') }}</b>
                </h3>
                <ul class="nav nav-pills mt-2 mt-sm-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('back.checkout.message') }}">
                            <i class="fas fa-comment-alt mr-1"></i> {{ __('Popup Messages') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('back.announcement.index') }}">
                            <i class="fas fa-bullhorn mr-1"></i> {{ __('Announcement for All Vendors') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    @include('alerts.alerts')

    <!-- Post New Announcement Form -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 font-weight-bold text-primary">
                        <i class="fas fa-plus-circle mr-1"></i> {{ __('Post New Announcement to All Vendors') }}
                    </h5>
                    <span class="badge badge-info text-white px-2 py-1" style="font-size: 11px;">
                        <i class="fas fa-broadcast-tower mr-1"></i> {{ __('Broadcasts to All Registered Vendors') }}
                    </span>
                </div>
                <div class="card-body">
                    <form action="{{ route('back.announcement.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="announcement_title"><b>{{ __('Announcement Title / Subject') }}</b> <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="announcement_title" class="form-control" placeholder="e.g. Important Update: New Delivery Schedule & Guidelines" required value="{{ old('title') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="badge_type"><b>{{ __('Announcement Category / Priority') }}</b></label>
                                    <select name="badge_type" id="badge_type" class="form-control">
                                        <option value="info" {{ old('badge_type') == 'info' ? 'selected' : '' }}>ℹ️ Info / General Announcement</option>
                                        <option value="warning" {{ old('badge_type') == 'warning' ? 'selected' : '' }}>⚠️ Warning / Important Notice</option>
                                        <option value="danger" {{ old('badge_type') == 'danger' ? 'selected' : '' }}>🚨 Urgent / Policy Alert</option>
                                        <option value="success" {{ old('badge_type') == 'success' ? 'selected' : '' }}>✅ Success / Good News</option>
                                        <option value="primary" {{ old('badge_type') == 'primary' ? 'selected' : '' }}>📢 Official Broadcast</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-2">
                            <label for="announcement_message"><b>{{ __('Announcement Message Content') }}</b> <span class="text-danger">*</span></label>
                            <textarea name="message" id="announcement_message" class="form-control" rows="6" placeholder="{{ __('Type your full announcement message here. Paragraph breaks and formatting will be neatly preserved for all vendors...') }}" required style="font-size: 14px; line-height: 1.6;">{{ old('message') }}</textarea>
                            <small class="text-muted">{{ __('This message will be instantly visible to all vendors in their vendor dashboard Announcements section.') }}</small>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary px-4 py-2 font-weight-bold shadow-sm">
                                <i class="fas fa-paper-plane mr-1"></i> {{ __('Post Announcement to All Vendors') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Previously Posted Announcements List -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 font-weight-bold text-dark">
                        <i class="fas fa-history mr-1 text-secondary"></i> {{ __('Previously Posted Announcements') }}
                    </h5>
                    <span class="badge badge-secondary px-2 py-1">{{ $announcements->total() }} {{ __('Total') }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-items-center mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th style="min-width: 140px;">{{ __('Category') }}</th>
                                    <th style="min-width: 250px;">{{ __('Title & Content Preview') }}</th>
                                    <th style="min-width: 180px;">{{ __('Posted Date & Time') }}</th>
                                    <th style="min-width: 130px;">{{ __('Vendor Views') }}</th>
                                    <th style="min-width: 130px;" class="text-right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($announcements as $key => $item)
                                @php
                                    $badgeClass = 'badge-info';
                                    $badgeIcon = 'fa-info-circle';
                                    $badgeLabel = 'Info';
                                    if ($item->badge_type == 'warning') {
                                        $badgeClass = 'badge-warning text-dark';
                                        $badgeIcon = 'fa-exclamation-triangle';
                                        $badgeLabel = 'Important Notice';
                                    } elseif ($item->badge_type == 'danger') {
                                        $badgeClass = 'badge-danger';
                                        $badgeIcon = 'fa-exclamation-circle';
                                        $badgeLabel = 'Urgent Alert';
                                    } elseif ($item->badge_type == 'success') {
                                        $badgeClass = 'badge-success';
                                        $badgeIcon = 'fa-check-circle';
                                        $badgeLabel = 'Good News';
                                    } elseif ($item->badge_type == 'primary') {
                                        $badgeClass = 'badge-primary';
                                        $badgeIcon = 'fa-bullhorn';
                                        $badgeLabel = 'Official';
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $announcements->firstItem() + $key }}</td>
                                    <td>
                                        <span class="badge {{ $badgeClass }} px-2 py-1" style="font-size: 11px;">
                                            <i class="fas {{ $badgeIcon }} mr-1"></i> {{ __($badgeLabel) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-dark mb-1" style="font-size: 14.5px;">
                                            {{ $item->title }}
                                        </div>
                                        <div class="text-muted small" style="max-width: 450px; white-space: normal; line-height: 1.4;">
                                            {{ \Illuminate\Support\Str::limit($item->message, 120) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-dark" style="font-size: 13px;">
                                            <i class="far fa-calendar-alt text-muted mr-1"></i> {{ $item->created_at ? $item->created_at->format('M d, Y') : '-' }}
                                        </div>
                                        <div class="text-muted small">
                                            <i class="far fa-clock mr-1"></i> {{ $item->created_at ? $item->created_at->format('h:i A') : '' }} ({{ $item->created_at ? $item->created_at->diffForHumans() : '' }})
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-light border px-2 py-1 text-muted font-weight-bold" style="font-size: 12px;">
                                            <i class="fas fa-eye text-primary mr-1"></i> {{ $item->views_count }} {{ __('views') }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <div class="d-flex justify-content-end align-items-center" style="gap: 5px;">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#editModal{{ $item->id }}" title="{{ __('Edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#deleteModal{{ $item->id }}" title="{{ __('Delete') }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>

                                        <!-- Edit Modal -->
                                        <div class="modal fade text-left" id="editModal{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-light">
                                                        <h5 class="modal-title font-weight-bold">
                                                            <i class="fas fa-edit text-primary mr-1"></i> {{ __('Edit Announcement') }}
                                                        </h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('back.announcement.update', $item->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <div class="form-group">
                                                                        <label><b>{{ __('Title') }}</b> <span class="text-danger">*</span></label>
                                                                        <input type="text" name="title" class="form-control" value="{{ $item->title }}" required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label><b>{{ __('Category / Priority') }}</b></label>
                                                                        <select name="badge_type" class="form-control">
                                                                            <option value="info" {{ $item->badge_type == 'info' ? 'selected' : '' }}>ℹ️ Info / General Announcement</option>
                                                                            <option value="warning" {{ $item->badge_type == 'warning' ? 'selected' : '' }}>⚠️ Warning / Important Notice</option>
                                                                            <option value="danger" {{ $item->badge_type == 'danger' ? 'selected' : '' }}>🚨 Urgent / Policy Alert</option>
                                                                            <option value="success" {{ $item->badge_type == 'success' ? 'selected' : '' }}>✅ Success / Good News</option>
                                                                            <option value="primary" {{ $item->badge_type == 'primary' ? 'selected' : '' }}>📢 Official Broadcast</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group mt-2">
                                                                <label><b>{{ __('Message Content') }}</b> <span class="text-danger">*</span></label>
                                                                <textarea name="message" class="form-control" rows="7" required style="font-size: 14px; line-height: 1.6;">{{ $item->message }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                                                            <button type="submit" class="btn btn-primary font-weight-bold">
                                                                <i class="fas fa-save mr-1"></i> {{ __('Save Changes') }}
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Delete Confirmation Modal -->
                                        <div class="modal fade text-left" id="deleteModal{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title font-weight-bold text-white">
                                                            <i class="fas fa-exclamation-triangle mr-1"></i> {{ __('Confirm Deletion') }}
                                                        </h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body py-4">
                                                        <p class="mb-0 text-dark" style="font-size: 15px;">
                                                            {{ __('Are you sure you want to delete this announcement?') }}
                                                        </p>
                                                        <div class="alert alert-light border mt-3 mb-0">
                                                            <strong>{{ $item->title }}</strong>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <form action="{{ route('back.announcement.delete', $item->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                                                            <button type="submit" class="btn btn-danger font-weight-bold">
                                                                <i class="fas fa-trash-alt mr-1"></i> {{ __('Yes, Delete') }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-bullhorn fa-3x mb-3 text-muted d-block opacity-50"></i>
                                        <p class="mb-0 font-weight-bold" style="font-size: 16px;">{{ __('No announcements posted yet.') }}</p>
                                        <small>{{ __('Use the form above to broadcast your first announcement to all vendors.') }}</small>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($announcements->hasPages())
                <div class="card-footer bg-white d-flex justify-content-end py-3">
                    {{ $announcements->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
