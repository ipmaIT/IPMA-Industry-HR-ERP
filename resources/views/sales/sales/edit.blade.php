@extends('layouts.app')

@section('content')
<div class="container row">
@include('sales.salesdept.navhr')
	<div class="row justify-content-center">
		<h2>Edit Customer Order</h2>
		<form method="POST" action="{{ route('sale.update', $sale) }}" accept-charset="UTF-8" id="form" autocomplete="off" class="needs-validation" enctype="multipart/form-data">
			@csrf
			@method('PATCH')
			@include('sales.sales._form')
		</form>
	</div>
</div>
@endsection

@section('js')
	@include('sales.sales._js')
@endsection


