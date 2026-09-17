@extends('master.back')

@section('content')

<!-- Start of Main Content -->
<div class="container-fluid">

	<!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 bc-title"><b>{{ __('Categories') }}</b></h3>
                <a class="btn btn-primary btn-sm" href="{{route('back.category.create')}}"><i class="fas fa-plus"></i> {{ __('Add') }}</a>
            </div>
        </div>
    </div>

    <!-- Buyer Category Display Limit Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('back.category.limit.update') }}" method="POST" class="d-flex align-items-center justify-content-between flex-wrap">
                @csrf
                <div class="d-flex align-items-center mb-2 mb-sm-0">
                    <i class="fas fa-sliders-h fa-2x text-primary mr-3"></i>
                    <div>
                        <h6 class="mb-0 font-weight-bold">{{ __('Buyer Panel Category Limit') }}</h6>
                        <small class="text-muted">{{ __('Maximum number of categories to display on the buyer website (default: 50). Sellers and Admin will always see all categories.') }}</small>
                    </div>
                </div>
                <div class="d-flex align-items-center mt-2 mt-sm-0">
                    <div class="input-group" style="width: 220px;">
                        <input type="number" name="buyer_category_limit" class="form-control" value="{{ \App\Models\Setting::first()->buyer_category_limit ?? 50 }}" min="1" max="500" required>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary btn-sm px-3">{{ __('Save Limit') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Create Table Btn --}}

	<!-- DataTales -->
	<div class="card shadow mb-4">
		<div class="card-body">
			@include('alerts.alerts')
			<div class="gd-responsive-table">
				<table class="table table-bordered table-striped" id="admin-table" width="100%" cellspacing="0">

					<thead>
						<tr>
							<th>{{ __('Image') }}</th>
                            <th>{{ __('Name') }}</th>
							<th>{{ __('Status') }}</th>
							<th>{{ __('Actions') }}</th>
						</tr>
					</thead>

					<tbody>
              @include('back.category.table',compact('datas'))
					</tbody>

				</table>
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
			{{ __('You are going to delete this category. All contents related with this category will be lost.') }} {{ __('Do you want to delete it?') }}
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
