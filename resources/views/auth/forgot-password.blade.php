@extends('layouts.app')
@section('content')
<form method="POST" action="{{ route('password.email') }}" id='form' autocomplete="off" enctype="multipart/form-data">
	@csrf
	<div class="card">
		<div class="card-header">
			<h3>Forgot Password</h3>
		</div>
		<div class="card-body">
			<p>Forgot your password? No problem. Just let us know your username and we will email you a password reset link that will allow you to choose a new one.</p>
			<div class="form-group row m-1 @error('username') has-error @enderror">
				<label for="username" class="col-form-label col-sm-2">Username : </label>
				<div class="col-sm-6 my-auto">
					<input type="text" name="username" value="{{ old('username', @$variable->username) }}" id="username" class="form-control form-control-sm @error('username') is-invalid @enderror" placeholder="Username">
					@error('username')
						<div class="invalid-feedback">
							{{ $message }}
						</div>
					@enderror
				</div>
			</div>
		</div>
		<div class="card-footer d-flex justify-content-end">
			<button type="submit" class="btn btn-sm btn-outline-secondary m-0 col-auto">Email Password Reset Link</button>
		</div>
	</div>
</form>
@endsection

@section('js')
@endsection
