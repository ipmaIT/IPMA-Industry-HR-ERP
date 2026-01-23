@extends('layouts.app')

@section('content')
<div class="container row">
@include('sales.salesdept.navhr')
	<div class="row justify-content-center">
		<h2>Add Customer Order</h2>
		<form method="POST" action="{{ route('sale.store') }}" accept-charset="UTF-8" id="form" autocomplete="off" class="" enctype="multipart/form-data">
			@csrf
			@include('sales.sales._form')
		</form>
	</div>
</div>
@endsection

@section('js')
	@include('sales.sales._js')
@endsection
