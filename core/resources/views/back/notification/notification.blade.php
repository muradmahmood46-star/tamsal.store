@extends('master.back')

@section('content')

<!-- Start of Main Content -->
<div class="container-fluid">

	<!-- Page Heading -->
    <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px;">
        <div class="card-body py-3">
            <div class="d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-0 text-dark font-weight-bold">
                        <i class="fas fa-bell text-primary mr-2"></i> {{ __('Notifications List') }}
                    </h4>
                    <p class="text-muted small mb-0">{{ __('View and manage all system alerts, new registrations, and order notifications.') }}</p>
                </div>
                <div class="mt-2 mt-sm-0">
                    <a class="btn btn-outline-danger btn-sm" id="clear-notf" data-href="{{ route('back.notifications.clear') }}" href="javascript:;" onclick="return confirm('{{ __('Are you sure you want to clear all notifications?') }}')">
                        <i class="fas fa-trash-alt mr-1"></i> {{ __('Clear All') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

	<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
		<div class="card-body p-0">
			@include('alerts.alerts')
            
            <div class="list-group list-group-flush">
                @forelse(App\Models\Notification::with(['user', 'order'])->orderby('id','desc')->get() as $notf)
                    @if($notf->user_id != null)
                        <div class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 px-3 px-md-4">
                            <a class="d-flex align-items-center text-decoration-none text-dark flex-grow-1 mr-3" href="{{ route('back.user.show',$notf->user_id) }}">
                                <div class="mr-3 flex-shrink-0">
                                    <div class="icon-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px; border-radius: 50%;">
                                        <i class="fas fa-user-plus" style="font-size: 16px;"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="font-weight-bold" style="font-size: 14px;">{{ __('A new user has registered.') }}</div>
                                    @if($notf->user && ($notf->user->first_name || $notf->user->email))
                                        <div class="text-muted small">
                                            {{ trim($notf->user->first_name . ' ' . $notf->user->last_name) ?: $notf->user->email }}
                                        </div>
                                    @endif
                                    <div class="text-muted" style="font-size: 11px; margin-top: 2px;">
                                        <i class="fas fa-clock mr-1"></i> {{ $notf->created_at ? $notf->created_at->diffForHumans() : '' }}
                                    </div>
                                </div>
                            </a>
                            <div class="flex-shrink-0">
                                <a class="btn btn-outline-danger btn-sm rounded-circle" href="{{route('back.notification.delete',$notf->id)}}" title="{{ __('Delete Notification') }}" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-trash-alt" style="font-size: 12px;"></i>
                                </a>
                            </div>
                        </div>
                    @endif
                    @if($notf->order_id != null)
                        <div class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 px-3 px-md-4">
                            <a class="d-flex align-items-center text-decoration-none text-dark flex-grow-1 mr-3" href="{{ route('back.order.invoice',$notf->order_id) }}">
                                <div class="mr-3 flex-shrink-0">
                                    <div class="icon-circle bg-success text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px; border-radius: 50%;">
                                        <i class="fas fa-shopping-cart" style="font-size: 16px;"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="font-weight-bold" style="font-size: 14px;">{{ __('You have received a new order.') }}</div>
                                    @if($notf->order && $notf->order->transaction_number)
                                        <div class="text-primary font-weight-bold small">
                                            #{{ $notf->order->transaction_number }}
                                        </div>
                                    @endif
                                    <div class="text-muted" style="font-size: 11px; margin-top: 2px;">
                                        <i class="fas fa-clock mr-1"></i> {{ $notf->created_at ? $notf->created_at->diffForHumans() : '' }}
                                    </div>
                                </div>
                            </a>
                            <div class="flex-shrink-0">
                                <a class="btn btn-outline-danger btn-sm rounded-circle" href="{{route('back.notification.delete',$notf->id)}}" title="{{ __('Delete Notification') }}" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-trash-alt" style="font-size: 12px;"></i>
                                </a>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-bell-slash fa-3x mb-3 text-secondary d-block"></i>
                        <h5>{{__('No Notifications Found')}}</h5>
                    </div>
                @endforelse
            </div>
		</div>
	</div>

</div>

</div>
<!-- End of Main Content -->

{{-- DELETE MODAL --}}

  <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="confirm-deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">

		<!-- Modal Header -->
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">{{ __('Confirm Delete?') }}</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
		</div>

		<!-- Modal Body -->
        <div class="modal-body">
			{{ __('You are going to delete this feature. All contents related with this feature will be lost.') }} {{ __('Do you want to delete it?') }}
		</div>

		<!-- Modal footer -->
        <div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
			<form action="" class="d-inline btn-ok" method="POST">

                @csrf

                @method('DELETE')

                <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>

			</form>
		</div>

      </div>
    </div>
  </div>

{{-- DELETE MODAL ENDS --}}

@endsection



