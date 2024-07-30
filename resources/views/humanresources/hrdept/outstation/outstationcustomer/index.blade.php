@extends('layouts.app')

@section('content')
<?php

use App\Models\Customer;
$no = 1;

?>

<div class="container row align-items-start justify-content-center">
	@include('humanresources.hrdept.navhr')

	<h4>Customer&nbsp;<a class="btn btn-sm btn-outline-secondary" href="{{ route('outstationcustomer.create') }}"><i class="fa-solid fa-person-digging fa-beat"></i> Add Customer</a></h4>
	<div class="table-responsive m-3">
		<table class="table table-hover table-sm" id="nowoutstation" style="font-size:12px;">
			<thead>
				<tr>
					<th>NO</th>
					<th>Company</th>
					<th>Customer Name</th>
					<th>Address</th>
					<th>Phone</th>
					<th>Fax</th>
					<th>#</th>
				</tr>
			</thead>
			<tbody>
				@foreach(Customer::orderBy('customer', 'ASC')->get() as $key => $customer)
				<tr>
					<td>
						{{ $no++ }}
					</td>
					<td>
						{{ $customer->customer }}
					</td>
					<td>
						{{ $customer->contact }}
					</td>
					<td>
						{{ $customer->address }}
					</td>
					<td>
						{{ $customer->phone }}
					</td>
					<td>
						{{ $customer->fax }}
					</td>
					<td>
						<a href="{{ route('outstationcustomer.edit', $customer->id) }}" class="btn btn-sm btn-outline-secondary"><i class="fa-regular fa-pen-to-square"></i></a>
						<button type="button" id="out" class="btn btn-sm btn-outline-secondary text-danger delete_button" data-id="{{ $customer->id }}"><i class="fa-regular fa-trash-can"></i></button>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>

</div>
@endsection

@section('js')
/////////////////////////////////////////////////////////////////////////////////////////
// tooltip
$(document).ready(function(){
$('[data-bs-toggle="tooltip"]').tooltip();
});

/////////////////////////////////////////////////////////////////////////////////////////
// datatables
$.fn.dataTable.moment( 'D MMM YYYY' );
$.fn.dataTable.moment( 'D MMM YYYY h:mm a' );
$('#nowoutstation,#lastoutstation').DataTable({
	"lengthMenu": [ [100, 250, 500, -1], [100, 250, 500, "All"] ],
	"columnDefs": [ { type: 'date', 'targets': [3, 4] } ],
	"order": [[4, "desc"], [3, "desc"]],	// sorting the 5th column descending
	responsive: true
})
.on( 'length.dt page.dt order.dt search.dt', function ( e, settings, len ) {
	$(document).ready(function(){
		$('[data-bs-toggle="tooltip"]').tooltip();
	});}
);
@endsection