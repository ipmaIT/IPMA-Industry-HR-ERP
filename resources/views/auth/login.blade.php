@extends('layouts.app')

@section('content')
	<form method="POST" action="{{ route('login') }}" id='form' class="needs-validation" autocomplete="off" enctype="multipart/form-data">
	@csrf
	<div class="mb-3 row">
		<div class="form-group row {{ $errors->has('username') ? 'has-error' : '' }}">
			<label for="username" class="col-auto col-form-label col-form-label-sm">Username : </label>
			<div class="col-sm-6">
				<input type="text" name="username" value="{{ old('username') }}" id="username" class="form-control form-control-sm" id="username" placeholder="Username">
			</div>
		</div>
	</div>
	<div class="mb-3 row">
		<div class="form-group row {{ $errors->has('password') ? 'has-error' : '' }}">
			<label for="password" class="col-auto col-form-label col-form-label-sm">Password : </label>
			<div class="col-sm-6">
				<input type="password" name="password" class="form-control form-control-sm" id="password" placeholder="Password">
			</div>
		</div>
	</div>
	<div class="mb-3 row">
		<div class="pretty p-svg p-round p-plain p-jelly">
			<input type="checkbox" name="remember" value="{{ old('remember') }}" class="form-check-input form-check-input-sm" id="remember_me">
			<div class="state p-success">
				<span class="svg"><i class="bi bi-check"></i></span>
				<label for="remember_me">{{ __('Remember me') }}</label>
			</div>
		</div>
	</div>
	<div class="flex items-center justify-end mt-4">
		<button type="submit" class="btn btn-sm btn-outline-secondary">Login</button>
		@if (Route::has('password.request'))
			<a class="" href="{{ route('password.request') }}">
				{{ __('Forgot your password?') }}
			</a>
		@endif
	</div>
	</form>
@endsection

@section('js')
/////////////////////////////////////////////////////////////////////////////////////////
// validator
$(document).ready(function() {
	$('#form').bootstrapValidator({
		feedbackIcons: {
			valid: 'fas fa-light fa-check',
			invalid: 'fas fa-sharp fa-light fa-xmark',
			validating: 'fas fa-duotone fa-spinner-third'
		},
		fields: {
			username: {
				validators: {
					notEmpty: {
						message: 'Please insert username'
					},
				}
			},
			password: {
				validators: {
					notEmpty : {
						message: 'Please insert password'
					},
				}
			},
		}
	})
	.find('[name="reason"]')
	// .ckeditor()
	// .editor
		.on('change', function() {
			// Revalidate the bio field
		$('#form').bootstrapValidator('revalidateField', 'reason');
		// console.log($('#reason').val());
	})
	;
});

/////////////////////////////////////////////////////////////////////////////////////////
@endsection
