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
					<td style="max-width: 30px; overflow: hidden;">
						{{ $no++ }}
					</td>
					<td style="min-width: 215px; max-width: 220px; overflow: hidden;">
						{{ $customer->customer }}
					</td>
					<td style="min-width: 175px; max-width: 180px; overflow: hidden;">
						{{ $customer->contact }}
					</td>
					<td style="overflow: hidden;">
						{{ $customer->address }}
					</td>
					<td style="min-width: 95px; max-width: 100px; overflow: hidden;">
						{{ $customer->phone }}
					</td>
					<td style="min-width: 95px; max-width: 100px; overflow: hidden;">
						{{ $customer->fax }}
					</td>
					<td style="min-width: 75px; max-width: 80px; overflow: hidden;">
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



/////////////////////////////////////////////////////////////////////////////////////////
// ajax post delete row
$(document).on('click', '.delete_button', function(e){

	var outId = $(this).data('id');
	SwalDelete(outId);
	e.preventDefault();
});

function SwalDelete(outId){
	swal.fire({
		title: 'Are you sure?',
		text: "It will be deleted permanently!",
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Yes, delete it!',
		showLoaderOnConfirm: true,

		preConfirm: function() {
			return new Promise(function(resolve) {
				$.ajax({
					url: '{{ url('outstationcustomer') }}' + '/' + outId,
					type: 'DELETE',
					data: {
							_token : $('meta[name=csrf-token]').attr('content'),
							id: outId,
					},
					dataType: 'json'
				})
				.done(function(response){
					swal.fire('Deleted!', response.message, response.status)
					.then(function(){
						window.location.reload(true);
					});
					//$('#delete_product_' + outId).parent().parent().remove();
				})
				.fail(function(){
					swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
				})
			});
		},
		allowOutsideClick: false
	})
	.then((result) => {
		if (result.dismiss === swal.DismissReason.cancel) {
			swal.fire('Cancelled', 'Your data is safe from delete', 'info')
		}
	});
}
@endsection