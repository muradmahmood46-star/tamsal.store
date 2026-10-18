@extends('master.seller')

@section('content')

<div class="container-fluid">

	<!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 text-dark"><b>{{ __('My Brands') }}</b></h3>
                <a class="btn btn-primary btn-sm font-weight-bold" href="{{ route('seller.brand.create') }}">
                    <i class="fas fa-plus mr-1"></i> {{ __('Add Brand') }}
                </a>
            </div>
        </div>
    </div>

<style>
    .brand-table-wrap {
        width: 100% !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
        border: 1px solid #ebedf2;
        border-radius: 6px;
    }
    .brand-table {
        min-width: 750px !important;
        width: 100% !important;
        margin-bottom: 0 !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
    }
    .brand-table th,
    .brand-table td {
        vertical-align: middle !important;
        white-space: nowrap !important;
    }
    .brand-table th {
        font-size: 11.5px !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.4px !important;
        background-color: #f8fafc !important;
        padding: 12px 14px !important;
        color: #475569 !important;
        border-bottom: 2px solid #e2e8f0 !important;
    }
    .brand-table td {
        font-size: 13px !important;
        padding: 10px 14px !important;
        color: #1e293b !important;
    }

    /* Fixed Column Widths to prevent overlap on any screen */
    .brand-table th.brand-col-name,
    .brand-table td.brand-col-name {
        width: 240px !important;
        min-width: 220px !important;
        max-width: 260px !important;
        text-align: left !important;
        padding-left: 14px !important;
        padding-right: 14px !important;
        white-space: nowrap !important;
    }
    .brand-table th.brand-col-logo,
    .brand-table td.brand-col-logo {
        width: 120px !important;
        min-width: 110px !important;
        max-width: 130px !important;
        text-align: center !important;
    }
    .brand-table th.brand-col-slug,
    .brand-table td.brand-col-slug {
        width: 200px !important;
        min-width: 180px !important;
        max-width: 220px !important;
        text-align: left !important;
        padding-left: 14px !important;
        padding-right: 14px !important;
        white-space: nowrap !important;
    }
    .brand-table th.brand-col-status,
    .brand-table td.brand-col-status {
        width: 130px !important;
        min-width: 120px !important;
        text-align: center !important;
    }
    .brand-table th.brand-col-actions,
    .brand-table td.brand-col-actions {
        width: 120px !important;
        min-width: 110px !important;
        text-align: center !important;
    }

    @media (max-width: 767.98px) {
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            text-align: left !important;
            margin-bottom: 10px !important;
        }
        .dataTables_wrapper .dataTables_filter input {
            width: 100% !important;
            max-width: 220px !important;
            margin-left: 0 !important;
            margin-top: 5px !important;
            display: inline-block !important;
        }
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            text-align: center !important;
            margin-top: 8px !important;
        }
        .dataTables_wrapper .dataTables_paginate ul.pagination {
            justify-content: center !important;
            flex-wrap: wrap !important;
        }
    }
</style>

	<!-- DataTales -->
	<div class="card shadow mb-4">
		<div class="card-body">
			@include('alerts.alerts')
			<table class="table table-bordered table-striped table-hover brand-table" id="brand-admin-table" width="100%" cellspacing="0">
				<thead>
					<tr>
                        <th class="brand-col-name">{{ __('Name') }}</th>
                        @if(($setting->is_brand_image ?? 1) == 1)
                            <th class="brand-col-logo text-center">{{ __('Logo') }}</th>
                        @endif
                        <th class="brand-col-slug">{{ __('Slug') }}</th>
						<th class="brand-col-status text-center">{{ __('Status') }}</th>
						<th class="brand-col-actions text-center">{{ __('Actions') }}</th>
					</tr>
				</thead>
				<tbody>
                    @include('seller.brand.table', compact('datas'))
				</tbody>
			</table>
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
                {{ __('Are you sure you want to delete this brand? Products linked to this brand may be affected.') }}
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

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        if ($('#brand-admin-table').length > 0) {
            $('#brand-admin-table').DataTable({
                responsive: false,
                ordering: false,
                pageLength: 15,
                lengthMenu: [[15, 25, 50, 100, -1], [15, 25, 50, 100, "{{ __('All') }}"]],
                dom: "<'row add-btn-area'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>><'row'<'col-sm-12'<'brand-table-wrap'tr>>><'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                language: {
                    search: "{{ __('Search:') }}",
                    searchPlaceholder: "{{ __('Search brands...') }}",
                    lengthMenu: "{{ __('Show _MENU_ entries') }}",
                    info: "{{ __('Showing _START_ to _END_ of _TOTAL_ entries') }}",
                    infoEmpty: "{{ __('Showing 0 to 0 of 0 entries') }}",
                    infoFiltered: "{{ __('(filtered from _MAX_ total entries)') }}",
                    zeroRecords: "{{ __('No matching brands found') }}",
                    emptyTable: "{{ __('No brands found') }}",
                    paginate: {
                        first: "{{ __('First') }}",
                        last: "{{ __('Last') }}",
                        next: "{{ __('Next') }}",
                        previous: "{{ __('Previous') }}"
                    }
                }
            });
        }
    });
</script>
@endsection
