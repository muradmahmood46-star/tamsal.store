@extends('master.back')

@section('content')

<!-- Start of Main Content -->
<div class="container-fluid">

	<!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <h3 class="mb-0 bc-title"><b>{{ __('Brands') }}</b></h3>
                <div class="d-flex align-items-center mt-2 mt-sm-0">
                    <div class="d-flex align-items-center mr-4" style="background: #f8f9fa; padding: 6px 14px; border-radius: 20px; border: 1px solid #e3e6f0;">
                        <span class="mr-2 font-weight-bold text-dark" style="font-size: 13px;">
                            <i class="fas fa-image mr-1 text-primary"></i> {{ __('Brand Picture') }}:
                        </span>
                        <label class="switch-primary mb-0" style="vertical-align: middle;">
                            <input type="checkbox" id="brand-image-toggle" class="switch switch-bootstrap" 
                                {{ ($setting->is_brand_image ?? 1) == 1 ? 'checked' : '' }}>
                            <span class="switch-body"></span>
                        </label>
                    </div>
                    <a class="btn btn-primary btn-sm font-weight-bold" href="{{route('back.brand.create')}}">
                        <i class="fas fa-plus mr-1"></i> {{ __('Add Brand') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

	<!-- DataTales -->
	<div class="card shadow mb-4">
		<div class="card-body">
			@include('alerts.alerts')
			<div class="gd-responsive-table">
				<table class="table table-bordered table-striped" id="admin-table" width="100%" cellspacing="0">

					<thead>
						<tr>
                            <th>{{ __('Name') }}</th>
                            @if(($setting->is_brand_image ?? 1) == 1)
                                <th>{{ __('Logo') }}</th>
                            @endif
                            <th>{{ __('Slug') }}</th>
							<th>{{ __('Status') }}</th>
							<th>{{ __('Popular') }}</th>
							<th>{{ __('Actions') }}</th>
						</tr>
					</thead>

					<tbody>
                        @include('back.brand.table',compact('datas'))
					</tbody>

				</table>
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
			{{ __('You are going to delete this brand. All contents related with this brand will be lost.') }} {{ __('Do you want to delete it?') }}
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

@section('scripts')
<script>
    $(document).on('change', '#brand-image-toggle', function() {
        var isChecked = $(this).is(':checked') ? 1 : 0;
        var toggleUrl = "{{ route('back.brand.image.toggle', ':status') }}".replace(':status', isChecked);
        
        $.ajax({
            url: toggleUrl,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (typeof SuccessNotification === 'function') {
                    SuccessNotification(response.message || "{{ __('Status Updated Successfully.') }}");
                }
                setTimeout(function() {
                    window.location.reload();
                }, 400);
            },
            error: function() {
                if (typeof DangerNotification === 'function') {
                    DangerNotification("{{ __('Something went wrong. Please try again.') }}");
                }
            }
        });
    });
</script>
@endsection
