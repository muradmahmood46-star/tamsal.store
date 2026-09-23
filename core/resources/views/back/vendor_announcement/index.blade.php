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
                                        <div class="d-flex justify-content-end align-items-center" style="gap: 6px;">
                                            <button type="button" class="btn btn-sm btn-outline-primary px-2 py-1" data-toggle="modal" data-target="#editModal{{ $item->id }}" title="{{ __('Edit') }}" style="min-width: 34px; min-height: 32px;">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger px-2 py-1" data-toggle="modal" data-target="#deleteModal{{ $item->id }}" title="{{ __('Delete') }}" style="min-width: 34px; min-height: 32px;">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
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

    <!-- Modals defined outside table-responsive to ensure 100% reliable mobile functionality -->
    @foreach($announcements as $item)
    <!-- Edit Modal -->
    <div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title font-weight-bold text-white" id="editModalLabel{{ $item->id }}">
                        <i class="fas fa-edit mr-2"></i>{{ __('Edit Announcement') }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('back.announcement.update', $item->id) }}" method="POST">
                    @csrf
                    <div class="modal-body p-3 p-md-4">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-dark">{{ __('Announcement Title / Subject') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" value="{{ $item->title }}" required style="font-size: 14px; height: 42px;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-dark">{{ __('Category / Priority') }}</label>
                                    <select name="badge_type" class="form-control" style="font-size: 14px; height: 42px;">
                                        <option value="info" {{ $item->badge_type == 'info' ? 'selected' : '' }}>ℹ️ Info / General</option>
                                        <option value="warning" {{ $item->badge_type == 'warning' ? 'selected' : '' }}>⚠️ Warning / Important</option>
                                        <option value="danger" {{ $item->badge_type == 'danger' ? 'selected' : '' }}>🚨 Urgent / Alert</option>
                                        <option value="success" {{ $item->badge_type == 'success' ? 'selected' : '' }}>✅ Success / Update</option>
                                        <option value="primary" {{ $item->badge_type == 'primary' ? 'selected' : '' }}>📢 Official Broadcast</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-dark">{{ __('Message Content') }} <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control" rows="8" required style="font-size: 14px; line-height: 1.6;">{{ $item->message }}</textarea>
                            <small class="text-muted mt-1 d-block">{{ __('Changes will be immediately updated for all vendors.') }}</small>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-3">
                        <button type="button" class="btn btn-secondary px-3" data-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary font-weight-bold px-4">
                            <i class="fas fa-save mr-1"></i> {{ __('Save Changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-danger text-white py-3">
                    <h5 class="modal-title font-weight-bold text-white" id="deleteModalLabel{{ $item->id }}">
                        <i class="fas fa-exclamation-triangle mr-2"></i>{{ __('Confirm Deletion') }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center bg-danger-light rounded-circle text-danger" style="width: 60px; height: 60px; background-color: #fee2e2;">
                            <i class="fas fa-trash-alt fa-2x text-danger"></i>
                        </span>
                    </div>
                    <h5 class="font-weight-bold text-dark mb-2">{{ __('Delete this announcement?') }}</h5>
                    <p class="text-muted mb-3" style="font-size: 14px;">
                        {{ __('This announcement will be permanently removed and no longer visible to vendors.') }}
                    </p>
                    <div class="alert alert-light border text-left p-3 mb-0" style="border-radius: 8px;">
                        <span class="badge badge-secondary mb-1">{{ strtoupper($item->badge_type) }}</span>
                        <div class="font-weight-bold text-dark" style="font-size: 14px;">{{ $item->title }}</div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-3 justify-content-center">
                    <button type="button" class="btn btn-secondary px-4 mr-2" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <form action="{{ route('back.announcement.delete', $item->id) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger font-weight-bold px-4">
                            <i class="fas fa-trash-alt mr-1"></i> {{ __('Yes, Delete') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach

</div>
@endsection
