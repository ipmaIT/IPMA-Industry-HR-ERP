@extends('layouts.app')

@section('content')
<div class="col-sm-12 row">
	@include('humanresources.hrdept.navhr')
	<h4>Add Customer</h4>
	{!! Form::open(['route' => ['outstationcustomer.store'], 'id' => 'form', 'autocomplete' => 'off', 'files' => true]) !!}

	<div class="form-group row mb-3 {{ $errors->has('customer') ? 'has-error' : '' }}">
		{{Form::label('customer', 'Company Name : ', ['class' => 'col-sm-2 col-form-label'])}}
		<div class="col-md-10">
			{{ Form::text('customer', @$value, ['class' => 'form-control form-control-sm col-max', 'id' => 'customer', 'placeholder' => 'Company Name', 'autocomplete' => 'off']) }}
		</div>
	</div>

	<div class="form-group row mb-3 {{ $errors->has('contact') ? 'has-error' : '' }}">
		{{ Form::label( 'contact', 'Customer Name : ', ['class' => 'col-sm-2 col-form-label'] ) }}
		<div class="col-md-10">
			{{ Form::text('contact', @$value, ['class' => 'form-control form-control-sm col-max', 'id' => 'contact', 'placeholder' => 'Customer Name', 'autocomplete' => 'off']) }}
		</div>
	</div>

	<div class="form-group row mb-3 {{ $errors->has('phone') ? 'has-error' : '' }}">
		{{ Form::label( 'phone', 'Phone Num : ', ['class' => 'col-sm-2 col-form-label'] ) }}
		<div class="col-md-10">
			{{ Form::text('phone', @$value, ['class' => 'form-control form-control-sm col-auto', 'id' => 'phone', 'placeholder' => 'Phone Num', 'autocomplete' => 'off']) }}
		</div>
	</div>

	<div class="form-group row mb-3 {{ $errors->has('fax') ? 'has-error' : '' }}">
		{{ Form::label( 'fax', 'Fax Num : ', ['class' => 'col-sm-2 col-form-label'] ) }}
		<div class="col-md-10">
			{{ Form::text('fax', @$value, ['class' => 'form-control form-control-sm col-auto', 'id' => 'fax', 'placeholder' => 'Fax Num', 'autocomplete' => 'off']) }}
		</div>
	</div>

	<div class="form-group row mb-3 {{ $errors->has('address') ? 'has-error' : '' }}">
		{{ Form::label( 'address', 'Address : ', ['class' => 'col-sm-2 col-form-label'] ) }}
		<div class="col-md-10">
			{{ Form::textarea('address', @$value, ['class' => 'form-control form-control-sm col-max', 'id' => 'address', 'placeholder' => 'Address', 'autocomplete' => 'off', 'cols' => '120', 'rows' => '3']) }}
		</div>
	</div>

	<div class="form-group row mb-3 {{ $errors->has('latitude') ? 'has-error' : '' }}">
		{{ Form::label( 'latitude', 'Latitude : ', ['class' => 'col-sm-2 col-form-label'] ) }}
		<div class="col-md-10">
			{{ Form::text('latitude', @$value, ['class' => 'form-control form-control-sm col-auto', 'id' => 'latitude', 'placeholder' => 'Latitude', 'autocomplete' => 'off']) }}
		</div>
	</div>

	<div class="form-group row mb-3 {{ $errors->has('longitude') ? 'has-error' : '' }}">
		{{ Form::label( 'longitude', 'Longitude : ', ['class' => 'col-sm-2 col-form-label'] ) }}
		<div class="col-md-10">
			{{ Form::text('longitude', @$value, ['class' => 'form-control form-control-sm col-auto', 'id' => 'longitude', 'placeholder' => 'Longitude', 'autocomplete' => 'off']) }}
		</div>
	</div>

	<div class="form-group row mb-3 g-3 p-2">
		<div class="col-sm-10 offset-sm-2">
			{!! Form::button('Add', ['class' => 'btn btn-sm btn-outline-secondary', 'type' => 'submit']) !!}
		</div>
	</div>
	{{ Form::close() }}

</div>
@endsection

@section('js')
/////////////////////////////////////////////////////////////////////////////////////////
// bootstrap validator

$('#form').bootstrapValidator({
	feedbackIcons: {
		valid: '',
		invalid: '',
		validating: ''
	},
	fields: {
		'customer': {
			validators: {
				notEmpty: {
					message: 'Please Insert Company Name.'
				},
			}
		},
		'contact': {
			validators: {
				notEmpty: {
					message: 'Please Insert Customer Name'
				},
			}
		},
		'phone': {
			validators: {
				regexp: {
					regexp: /^\d+$/,
					message: 'Please Insert a Valid Phone Number.'
				},
			}
		},
		'fax': {
			validators: {
				regexp: {
					regexp: /^\d+$/,
					message: 'Please Insert a Valid Fax Number.'
				},
			}
		},
	}
});
@endsection
