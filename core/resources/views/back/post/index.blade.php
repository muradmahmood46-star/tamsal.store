@extends('master.back')

@section('content')

<!-- Start of Main Content -->
<div class="container-fluid">

	<!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 bc-title"><b>{{ __('Manage Blogs') }}</b> </h3>

                <div class="right">
                  <a class="btn btn-primary  btn-sm" href="{{route('back.post.create')}}"><i class="fas fa-plus"></i> {{ __('Add') }}</a>
                    <form class="d-inline-block" action="{{route('back.bulk.delete')}}" method="get">
                      <input type="hidden" value="" name="ids[]" id="bulk_delete">
                      <input type="hidden" value="posts" name="table">
                      <button class="btn btn-danger btn-sm">{{__('Delete')}}</button>
                    </form>
                </div>
              </div>

        </div>
    </div>

    <!-- Home Page Blogs Section Toggle Card -->
    <div class="card mb-4 shadow-sm border-0" style="background: #f8fafc; border: 1px solid #e2e8f0 !important; border-radius: 10px;">
        <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div class="d-flex align-items-center">
                    <div class="mr-3 text-primary">
                        <i class="fas fa-rss-square" style="font-size: 28px;"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 font-weight-bold text-dark">{{ __('Home Page Blogs Section') }}</h5>
                        <span class="text-muted small">{{ __('Toggle ON/OFF to show or hide the "Our Blog" section on the homepage for both mobile and PC.') }}</span>
                    </div>
                </div>
                <div class="mt-2 mt-sm-0 d-flex align-items-center">
                    <span class="mr-3 font-weight-bold {{ $setting->is_blogs == 1 ? 'text-success' : 'text-danger' }}" id="blog-toggle-status-text" style="font-size: 13px;">
                        {{ $setting->is_blogs == 1 ? __('Active (Showing on Home Page)') : __('Inactive (Hidden from Home Page)') }}
                    </span>
                    <label class="switch-primary mb-0">
                        <input type="checkbox" class="switch switch-bootstrap status" id="toggle_home_blogs" name="is_blogs" value="1" {{ $setting->is_blogs == 1 ? 'checked' : '' }}>
                        <span class="switch-body"></span>
                    </label>
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
                            <th> <input type="checkbox" data-target="blog-bulk-delete" class="form-control bulk_all_delete"> </th>
                            <th>{{ __('Image') }}</th>
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('Category') }}</th>
							<th>{{ __('Actions') }}</th>
						</tr>
					</thead>

					<tbody>
              @include('back.post.table',compact('datas'))
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
          <h3 class="modal-title" id="exampleModalLabel">{{ __('Confirm Delete?') }}</h3>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
		</div>

		<!-- Modal Body -->
        <div class="modal-body">
			{{ __('You are going to delete this post. All contents related with this post will be lost.') }} {{ __('Do you want to delete it?') }}
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
    $(document).on('change', '#toggle_home_blogs', function() {
        var $this = $(this);
        var status = $this.is(':checked') ? 1 : 0;
        var $statusText = $('#blog-toggle-status-text');
        
        $.ajax({
            url: '{{ route("back.post.toggle.home") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: status
            },
            success: function(response) {
                if (status == 1) {
                    $statusText.text('{{ __("Active (Showing on Home Page)") }}').removeClass('text-danger').addClass('text-success');
                } else {
                    $statusText.text('{{ __("Inactive (Hidden from Home Page)") }}').removeClass('text-success').addClass('text-danger');
                }
                $.notify({
                    icon: 'flaticon-alarm-1',
                    title: '{{ __("Success") }}',
                    message: response.message,
                },{
                    type: 'secondary',
                    placement: {
                        from: "bottom",
                        align: "right"
                    },
                    time: 1000,
                });
            },
            error: function(xhr) {
                $.notify({
                    icon: 'flaticon-error',
                    title: '{{ __("Error") }}',
                    message: '{{ __("Failed to update blog visibility status.") }}',
                },{
                    type: 'danger',
                    placement: {
                        from: "bottom",
                        align: "right"
                    },
                    time: 1000,
                });
            }
        });
    });
</script>
@endsection
