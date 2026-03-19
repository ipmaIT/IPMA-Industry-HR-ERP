@extends('layouts.app')

@section('content')
<?php
use \Carbon\Carbon;
?>
<div class="col-sm-12 row">
@include('sales.salesdept.navhr')
	<div class="row justify-content-center">
		<div class="table-responsive">
			<h2>Customer Order &nbsp; <a href="{{ route('sale.create') }}" class="btn btn-sm btn-outline-secondary" > <span class="mdi mdi-point-of-sale"></span>Add Order </a></h2>
			<table class="table table-sm table-hover m-3" id="sales" style="font: 12px roboto-flex;">
				<thead>
					<tr>
						<th>ID</th>
						<th>Date</th>
						<th>Customer</th>
						<th>Delivery Date</th>
						<th>Special Request</th>
						<th>Urgency</th>
						<th>Approved By</th>
						<th>Send Date</th>
						<th>Amend</th>
						<th>#</th>
					</tr>
				</thead>
				<tbody>
					@foreach($sales as $sale)
						<tr>
							<td>{{ $sale->belongstosalesby->sales_by.'-'.str_pad( $sale->no, 3, "0", STR_PAD_LEFT ).'/'.$sale->year }}</td>
							<td>{{ Carbon::parse($sale->date_order)->format('j M Y') }}</td>
							<td {!! ($sale->belongstocustomer?->customer)?'data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-html="true" data-bs-title="'.$sale->belongstocustomer?->customer.'"':NULL !!}>
								{{ Str::limit($sale->belongstocustomer?->customer, 10, ' >') }}
							</td>
							<td>{{ Carbon::parse($sale->delivery_at)->format('j M Y') }}</td>
							<td {!! ($sale->special_request)?'data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-html="true" data-bs-title="'.nl2br($sale->special_request).'"':NULL !!}>
								{!! Str::limit(nl2br($sale->special_request), 10, ' >') !!}
							</td>
							<td>{!! ($sale->urgency==1)?'<i class="fa-regular fa-circle-check fa-beat fa-1x"></i>':'<i class="fa-regular fa-circle-xmark fa-beat fa-1x"></i>' !!}</td>
							<td>
								{!!
									is_null($sale->approved_by)?'<button type="button" class="btn btn-sm btn-outline-secondary sale-approve" data-id="'.$sale->id.'"><i class="fa-solid fa-signature fa-beat"></i></button>':Carbon::parse($sale->approved_date)->format('j F Y')

								!!}
							</td>
							<td>
								{!!
									is_null($sale->confirm)?
											(
												is_null($sale->approved_by)?
													'<p>Please get approval before proceed</p>':
													'<button type="button" class="btn btn-sm btn-outline-secondary sale-send" data-id="'.$sale->id.'"><i class="fa-regular fa-paper-plane fa-beat"></i></button>'
											)
										:
										Carbon::parse($sale->confirm_date)->format('j F Y')
								!!}
							</td>
							<td {!! ($sale->amend)?'data-bs-toggle="tooltip" data-bs-custom-class="custom-tooltip" data-bs-html="true" data-bs-title="'.nl2br($sale->amend).'"':NULL !!}>
								<p class="mb-2"><button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#amend_{{$sale->id}}"><i class="fa-solid fa-hammer fa-beat"></i></button></p>
								{!! ($sale->amend)?Str::limit($sale->amend, 7, '>>'):null !!}
								<div class="modal modal-lg fade" id="amend_{{ $sale->id}}" tabindex="-1" aria-labelledby="Amend_{{ $sale->belongstosalesby->sales_by.'-'.str_pad( $sale->no, 3, 0, STR_PAD_LEFT ).'/'.$sale->year }}" aria-hidden="true">
									<div class="modal-dialog modal-dialog-centered">
										<div class="modal-content">
											<div class="modal-header">
												<h1 class="modal-title fs-5" id="Amend_{{ $sale->belongstosalesby->sales_by.'-'.str_pad( $sale->no, 3, 0, STR_PAD_LEFT ).'/'.$sale->year }}">Modal title</h1>
												<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
											</div>
											<div class="modal-body">
												<form method="POST" action="{{ route('saleamend', $sale) }}" accept-charset="UTF-8" id="form" autocomplete="off" class="" enctype="multipart/form-data">
													@csrf
													@method('PATCH')
												<div class="form-group row m-2 {{ $errors->has('amend') ? 'has-error' : '' }}">
													<label for="nam" class="col-form-label col-sm-4">Amendment : </label>
													<div class="col-sm-8">
														<textarea name="amend" id="nam" class="form-control form-control-sm col-sm-12 @error('amend') is-invalid @enderror" placeholder="Amendment">{{ old('amend', @$sale->amend) }}</textarea>
													</div>
												</div>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
												<button type="submit" class="btn btn-sm btn-primary">Save changes</button>
											</div>
												</form>
										</div>
									</div>
								</div>

							</td>
							<td>
								{!!
									!is_null($sale->approved_by)?
									NULL: '
									<div class="btn-group btn-group-sm" role="group">
										<a href="'.route('sale.edit', $sale->id).'" class="btn btn-sm btn-outline-secondary">
											<i class="fa-regular fa-pen-to-square fa-beat"></i>
										</a>
										<button class="btn btn-sm btn-outline-secondary" data-id="'.$sale->id.'">
											<i class="fa-solid fa-trash-can fa-beat" style="color: red;"></i>
										</button>
									</div>
								' !!}
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
@endsection

@section('js')
window.data = {
	route: {
	},
	url: {
		saleapproved: '{{ url('saleapproved') }}',
		salesend: '{{ url('salesend') }}',
	},
	old: {
	},
};

@endsection
