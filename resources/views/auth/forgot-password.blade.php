@extends('layouts.app')
@section('content')
<div class="col-sm-12 row">
	<p class="mb-3">
		Forgot your password? No problem. Just let us know your username and we will email you a password reset link that will allow you to choose a new one.
	</p>
	<form method="POST" action="{{ route('password.email') }}" id='form' autocomplete="off" enctype="multipart/form-data">
		@csrf
		<div class="col-sm-8 row mx-auto">
			<div class="form-group row {{ $errors->has('username') ? 'has-error' : '' }}">
				<label for="username" class="col-sm-2 col-form-label col-form-label-sm">Username : </label>
				<div class="col-sm-10">
					<input type="text" name="username" value="{{ old('username') }}" class="form-control form-control-sm col-auto" id="username" placeholder="Username">
				</div>
			</div>
			<button type="submit" class="btn btn-sm btn-outline-secondary m-3 col-auto">Email Password Reset Link</button>
		</div>
	</form>
</div>

@endsection

@section('js')
@endsection
