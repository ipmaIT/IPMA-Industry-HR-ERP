@extends('layouts.app')

@section('content')

<?php
use \App\Models\HumanResources\OptWorkingHour;
use \App\Models\Staff;
use \App\Models\Customer;

use \Carbon\Carbon;

$staffs = Staff::join('logins', 'staffs.id', '=', 'logins.staff_id')
			->where('staffs.active', 1)
			->where('logins.active', 1)
			->where(function ($query) {
				$query->where('staffs.div_id', '!=', 2)
				->orWhereNull('staffs.div_id');
			})
			->select('staffs.id as staffID', 'staffs.*', 'logins.*')
			->orderBy('logins.username', 'asc')
			->get();

$c = Customer::orderBy('customer')->pluck('customer', 'id')->toArray();
?>

<div class="col-sm-12 row">
	@include('humanresources.hrdept.navhr')
	<h4>Add Customer</h4>
	{!! Form::open(['route' => ['outstationcustomer.store'], 'id' => 'form', 'autocomplete' => 'off', 'files' => true]) !!}













	<div class="form-group row mb-3 {{ $errors->has('staff_id') ? 'has-error' : '' }}">
		{{Form::label('customer', 'Company Name : ', ['class' => 'col-sm-2 col-form-label'])}}
		<div class="col-md-10">
			{{ Form::text('customer', @$value, ['class' => 'form-control form-control-sm col-max', 'id' => 'customer', 'placeholder' => 'Company Name', 'autocomplete' => 'off']) }}
		</div>
	</div>

	<div class="form-group row mb-3 {{ $errors->has('customer_id') ? 'has-error' : '' }}">
		{{ Form::label( 'contact', 'Customer Name : ', ['class' => 'col-sm-2 col-form-label'] ) }}
		<div class="col-md-10">
			{{ Form::text('contact', @$value, ['class' => 'form-control form-control-sm col-max', 'id' => 'contact', 'placeholder' => 'Customer Name', 'autocomplete' => 'off']) }}
		</div>
	</div>

	<div class="form-group row mb-3 {{ $errors->has('customer_id') ? 'has-error' : '' }}">
		{{ Form::label( 'phone', 'Phone Num : ', ['class' => 'col-sm-2 col-form-label'] ) }}
		<div class="col-md-10">
			{{ Form::text('phone', @$value, ['class' => 'form-control form-control-sm col-auto', 'id' => 'phone', 'placeholder' => 'Phone Num', 'autocomplete' => 'off']) }}
		</div>
	</div>

	<div class="form-group row mb-3 {{ $errors->has('customer_id') ? 'has-error' : '' }}">
		{{ Form::label( 'fax', 'Fax Num : ', ['class' => 'col-sm-2 col-form-label'] ) }}
		<div class="col-md-10">
			{{ Form::text('fax', @$value, ['class' => 'form-control form-control-sm col-auto', 'id' => 'fax', 'placeholder' => 'Fax Num', 'autocomplete' => 'off']) }}
		</div>
	</div>

	<div class="form-group row mb-3 {{ $errors->has('date_from') ? 'has-error' : '' }}">
		{{ Form::label( 'address', 'Address : ', ['class' => 'col-sm-2 col-form-label'] ) }}
		<div class="col-md-10">
			{{ Form::textarea('address', @$value, ['class' => 'form-control form-control-sm col-max', 'id' => 'address', 'placeholder' => 'Address', 'autocomplete' => 'off', 'cols' => '120', 'rows' => '3']) }}
		</div>
	</div>

	<div class="form-group row mb-3 {{ $errors->has('customer_id') ? 'has-error' : '' }}">
		{{ Form::label( 'latitude', 'Phone Num : ', ['class' => 'col-sm-2 col-form-label'] ) }}
		<div class="col-md-10">
			{{ Form::text('phone', @$value, ['class' => 'form-control form-control-sm col-auto', 'id' => 'phone', 'placeholder' => 'Phone Num', 'autocomplete' => 'off']) }}
		</div>
	</div>

	<div class="form-group row mb-3 {{ $errors->has('customer_id') ? 'has-error' : '' }}">
		{{ Form::label( 'longitude', 'Phone Num : ', ['class' => 'col-sm-2 col-form-label'] ) }}
		<div class="col-md-10">
			{{ Form::text('phone', @$value, ['class' => 'form-control form-control-sm col-auto', 'id' => 'phone', 'placeholder' => 'Phone Num', 'autocomplete' => 'off']) }}
		</div>
	</div>

	<div class="form-group row mb-3 g-3 p-2">
		<div class="col-sm-10 offset-sm-2">
			{!! Form::button('Add Data', ['class' => 'btn btn-sm btn-outline-secondary', 'type' => 'submit']) !!}
		</div>
	</div>
	{{ Form::close() }}

</div>
@endsection

@section('js')
/////////////////////////////////////////////////////////////////////////////////////////
$('#loc').select2({
	placeholder: 'Please choose',
	allowClear: true,
	closeOnSelect: true,
	width: '100%',
});

/////////////////////////////////////////////////////////////////////////////////////////
//date
$('#from').datetimepicker({
	icons: {
		time: "fas fas-regular fa-clock fa-beat",
		date: "fas fas-regular fa-calendar fa-beat",
		up: "fa-regular fa-circle-up fa-beat",
		down: "fa-regular fa-circle-down fa-beat",
		previous: 'fas fas-regular fa-arrow-left fa-beat',
		next: 'fas fas-regular fa-arrow-right fa-beat',
		today: 'fas fas-regular fa-calenday-day fa-beat',
		clear: 'fas fas-regular fa-broom-wide fa-beat',
		close: 'fas fas-regular fa-rectangle-xmark fa-beat'
	},
	format:'YYYY-MM-DD',
	// useCurrent: false,
})
.on("dp.change dp.show dp.update", function (e) {
	var minDate = $('#from').val();
	$('#to').datetimepicker('minDate', minDate);
	$('#form').bootstrapValidator('revalidateField', 'date_from');
});


$('#to').datetimepicker({
	icons: {
		time: "fas fas-regular fa-clock fa-beat",
		date: "fas fas-regular fa-calendar fa-beat",
		up: "fa-regular fa-circle-up fa-beat",
		down: "fa-regular fa-circle-down fa-beat",
		previous: 'fas fas-regular fa-arrow-left fa-beat',
		next: 'fas fas-regular fa-arrow-right fa-beat',
		today: 'fas fas-regular fa-calenday-day fa-beat',
		clear: 'fas fas-regular fa-broom-wide fa-beat',
		close: 'fas fas-regular fa-rectangle-xmark fa-beat'
	},
	format: 'YYYY-MM-DD',
	// useCurrent: false //Important! See issue #1075
})
.on("dp.change dp.show dp.update", function (e) {
	var maxDate = $('#to').val();
	$('#from').datetimepicker('maxDate', maxDate);
	$('#form').bootstrapValidator('revalidateField', 'date_to');
});


/////////////////////////////////////////////////////////////////////////////////////////
// bootstrap validator

$('#form').bootstrapValidator({
	feedbackIcons: {
		valid: '',
		invalid: '',
		validating: ''
	},
	fields: {
		'staff_id[]': {
			validators: {
				notEmpty: {
					message: 'Please choose '
				},
			}
		},
		'date_from': {
			validators: {
				notEmpty: {
					message: 'Please insert date start. '
				},
				date: {
					format: 'YYYY-MM-DD',
					message: 'Please insert date start. '
				},
			}
		},
		'date_to': {
			validators: {
				notEmpty: {
					message: 'Please insert date end. '
				},
				date: {
					format: 'YYYY-MM-DD',
					message: 'Please insert date end. '
				},
			}
		},
	}
});
@endsection
