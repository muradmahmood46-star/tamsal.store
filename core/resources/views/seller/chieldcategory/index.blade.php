@extends('master.seller')

@section('content')

<!-- Start of Main Content -->
<div class="container-fluid">

	<!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <div>
                    <h3 class="mb-0 text-dark"><b><i class="fas fa-sitemap text-primary mr-2"></i> {{ __('Child Categories') }}</b></h3>
                    <p class="text-muted small mb-0">{{ __('Manage all marketplace child categories under subcategories.') }}</p>
                </div>
                <a href="{{ route('seller.childcategory.create') }}" class="btn btn-sm btn-primary font-weight-bold">
                    <i class="fas fa-plus mr-1"></i> {{ __('Add Child Category') }}
                </a>
            </div>
        </div>
    </div>

	<!-- DataTales -->
	<div class="card shadow mb-4">
		<div class="card-body">
			@include('alerts.alerts')
			<div class="gd-responsive-table">
				<table class="table table-bordered table-striped" id="admin-table" style="min-width: 850px;" width="100%" cellspacing="0">

					<thead>
						<tr>
							<th>{{ __('Category') }}</th>
							<th>{{ __('Subcategory') }}</th>
                            <th>{{ __('Childcategory Name') }}</th>
							<th>{{ __('Status') }}</th>
							<th>{{ __('Actions') }}</th>
						</tr>
					</thead>

					<tbody>
                        @include('seller.chieldcategory.table', compact('datas'))
					</tbody>

				</table>
			</div>
		</div>
	</div>

</div>

{{-- DELETE MODAL --}}
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="confirm-deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('Confirm Delete?') }}</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                {{ __('You are going to delete this child category. All contents related with this child category will be lost.') }} {{ __('Do you want to delete it?') }}
            </div>
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
