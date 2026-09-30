@extends('master.back')

@section('styles')
<style>
    /* Table Styling & Vertical Alignment */
    #admin-table th, 
    #admin-table td {
        vertical-align: middle !important;
    }
    .admin-product-thumb {
        width: 44px;
        height: 44px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
    }
    .admin-product-name-text {
        font-weight: 600;
        font-size: 13px;
        line-height: 1.35;
        color: #1e293b;
        word-break: break-word;
    }

    /* Mobile Responsive Compact Row Height */
    @media (max-width: 767.98px) {
        .gd-responsive-table {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
        }
        #admin-table {
            font-size: 11.5px !important;
        }
        #admin-table th, 
        #admin-table td {
            padding: 5px 4px !important;
            line-height: 1.2 !important;
            height: auto !important;
        }
        .admin-product-thumb {
            width: 32px !important;
            height: 32px !important;
            border-radius: 4px !important;
        }
        .gallery-count-badge {
            display: none !important;
        }
        .admin-product-name-col {
            max-width: 120px !important;
            min-width: 85px !important;
            width: 110px !important;
            padding: 4px !important;
        }
        .admin-product-name-text {
            font-size: 11px !important;
            line-height: 1.15 !important;
            max-height: 2.3em !important;
            overflow: hidden !important;
            display: -webkit-box !important;
            -webkit-line-clamp: 2 !important;
            -webkit-box-orient: vertical !important;
            text-overflow: ellipsis !important;
            word-break: break-word !important;
        }
        .text-truncate-mobile {
            display: inline-block !important;
            max-width: 100% !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        #admin-table .btn-sm {
            padding: 2px 5px !important;
            font-size: 10px !important;
            line-height: 1.15 !important;
        }
        #admin-table .badge {
            font-size: 9.5px !important;
            padding: 2px 4px !important;
        }
        #admin-table td input.bulk-item,
        #admin-table th input.bulk_all_delete {
            width: 14px !important;
            height: 14px !important;
            margin: 0 auto !important;
        }
    }
</style>
@endsection

@section('content')

<!-- Start of Main Content -->
<div class="container-fluid">

	<!-- Page Heading -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-0 bc-title"><b>{{ __('Stock Out Products') }}</b></h3>
                </div>
        </div>
    </div>

 
	<!-- DataTales -->
	<div class="card shadow mb-4">
		<div class="card-body">
            @include('alerts.alerts')
         
            <br>
			<div class="gd-responsive-table">
				<table class="table table-bordered table-striped" id="admin-table" width="100%" cellspacing="0">

					<thead>
						<tr>
							<th> <input type="checkbox" data-target="product-bulk-delete" class="form-control bulk_all_delete"> </th>
							<th>{{ __('Image') }}</th>
                            <th width="30%">{{ __('Name') }}</th>
                            <th>{{ __('Price') }}</th>
							<th>{{ __('Status') }}</th>
							<th>{{ __('Type') }}</th>
							<th>{{ __('Item Type') }}</th>
							<th>{{ __('Actions') }}</th>
						</tr>
					</thead>

					<tbody>
                        @include('back.item.table',compact('datas'))
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
			{{ __('You are going to delete this item. All contents related with this item will be lost.') }} {{ __('Do you want to delete it?') }}
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



