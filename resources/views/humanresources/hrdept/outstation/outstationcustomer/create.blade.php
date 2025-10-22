@extends('layouts.app')

@section('content')
<div class="col-sm-12 row">
	@include('humanresources.hrdept.navhr')
	<h4>Add Customer</h4>
	<form method="POST" action="{{ route('outstationcustomer.store') }}" accept-charset="UTF-8" id="form" autocomplete="off" class="" enctype="multipart/form-data">
		@csrf

	<div class="form-group row mb-3 @error('customer') has-error @enderror">
		<label for="customer" class="col-form-label col-sm-2">Company Name : </label>
		<div class="col-md-10">
			<input type="text" name="customer" value="{{ old('customer') }}" id="customer" class="form-control form-control-sm col-sm-12 @error('customer') is-invalid @enderror" placeholder="Company Name">
		</div>
	</div>

	<div class="form-group row mb-3 @error('contact') has-error @enderror">
		<label for="contact" class="col-form-label col-sm-2">Customer Name : </label>
		<div class="col-md-10">
			<input type="text" name="contact" value="{{ old('contact') }}" id="contact" class="form-control form-control-sm col-sm-12 @error('contact') is-invalid @enderror" placeholder="Customer Name">
		</div>
	</div>

	<div class="form-group row mb-3 @error('phone') has-error @enderror">
		<label for="phone" class="col-form-label col-sm-2">Phone Num : </label>
		<div class="col-md-10">
			<input type="text" name="phone" value="{{ old('phone') }}" id="phone" class="form-control form-control-sm col-sm-12 @error('phone') is-invalid @enderror" placeholder="Phone Num">
		</div>
	</div>

	<div class="form-group row mb-3 @error('fax') has-error @enderror">
		<label for="fax" class="col-form-label col-sm-2">Fax Num : </label>
		<div class="col-md-10">
			<input type="text" name="fax" value="{{ old('fax') }}" id="fax" class="form-control form-control-sm col-sm-12 @error('fax') is-invalid @enderror" placeholder="Fax Number">
		</div>
	</div>

	<div class="form-group row mb-3 @error('address') has-error @enderror">
		<label for="address" class="col-form-label col-sm-2">Address : </label>
		<div class="col-md-10">
			<textarea name="address" id="address" class="form-control form-control-sm col-sm-12 @error('address') is-invalid @enderror" placeholder="Address">{{ old('address') }}</textarea>
		</div>
	</div>

	<div class="form-group row mb-3 @error('latitude') has-error @enderror">
		<label for="latitude" class="col-form-label col-sm-2">Latitude : </label>
		<div class="col-md-10">
			<input type="text" name="latitude" value="{{ old('latitude') }}" id="latitude" class="form-control form-control-sm col-sm-12 @error('latitude') is-invalid @enderror" placeholder="Latitude">
		</div>
	</div>

	<div class="form-group row mb-3 @error('longitude') has-error @enderror">
		<label for="longitude" class="col-form-label col-sm-2">Longitude : </label>
		<div class="col-md-10">
			<input type="text" name="longitude" value="{{ old('longitude') }}" id="longitude" class="form-control form-control-sm col-sm-12 @error('longitude') is-invalid @enderror" placeholder="Longitude">
		</div>
	</div>

	<div class="form-group row mb-3 g-3 p-2">
		<div class="col-sm-10 offset-sm-2">
			<button type="submit" class="btn btn-sm btn-outline-secondary">Add</button>
		</div>
	</div>
</form>

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
