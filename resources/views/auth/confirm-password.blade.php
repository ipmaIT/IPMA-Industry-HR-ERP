@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('password.confirm') }}">
	@csrf
	<div class="card">
		<div class="card-header">
			<h3>Password Confirmation</h3>
		</div>
		<div class="card-body">
			<p>{{ __('This is a secure area of the application. Please confirm your password before continuing.') }}</p>
			<div class="form-group row m-1 @error('password') has-error @enderror">
				<label for="pass" class="col-form-label col-sm-2">Password : </label>
				<div class="col-sm-6 my-auto">
					<input type="password" name="password" value="{{ old('password', @$variable->password) }}" id="pass" class="form-control form-control-sm @error('password') is-invalid @enderror" placeholder="Password">
					@error('password')
						<div class="invalid-feedback">
							{{ $message }}
						</div>
					@enderror
				</div>
			</div>
		</div>
		<div class="card-footer">
			<div class="d-flex justify-content-end">
				<button type="submit" class="'btn btn-sm btn-outline-secondary">Confirm</button>
			</div>
		</div>
	</div>
</form>
@endsection
@section('js')
@endsection
