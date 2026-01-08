@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('password.store') }}" accept-charset="UTF-8" id="form" autocomplete="off" class="needs-validation" enctype="multipart/form-data">
	@csrf
	<input type="hidden" name="token" value="{{ $request->route('token') }}">
	<div class="card">
		<div class="card-header">
			<h3>Reset Password</h3>
		</div>
		<div class="card-body">
			<?php
			$pass = App\Models\Staff::firstwhere('email', $request->email)->hasmanylogin()->firstWhere('active', 1)->username;
			?>
			<div class="mb-3 row">
				<div class="form-group row {{ $errors->has('username') ? 'has-error' : '' }}">
					<label for="username" class="col-form-label col-sm-4">Username : </label>
					<div class="col-auto my-auto">
						<input type="text" name="username" value="{{ old('username', @$pass) }}" id="username" class="form-control form-control-sm @error('username') is-invalid @enderror" placeholder="Username">
					</div>
				</div>
			</div>
			<div class="mb-3 row">
				<div class="form-group row {{ $errors->has('password') ? 'has-error' : '' }}">
					<label for="password" class="col-form-label col-sm-4">Password : </label>
					<div class="col-auto my-auto">
						<input type="password" name="password" value="{{ old('password') }}" id="id" class="form-control form-control-sm @error('password') is-invalid @enderror" placeholder="Password">
					</div>
				</div>
			</div>
			<div class=" row">
				<div class="form-group row {{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
					<label for="password_confirmation" class="col-form-label col-sm-4">Confirm Password : </label>
					<div class="col-auto my-auto">
						<input type="password" name="password_confirmation" value="{{ old('password_confirmation') }}" id="password_confirmation" class="form-control form-control-sm @error('password_confirmation') is-invalid @enderror" placeholder="Confirm Password">
					</div>
				</div>
			</div>

		</div>
		<div class="card-footer">
			<button type="submit" class="btn btn-sm btn-outline-secondary">Reset Password</button>
		</div>
	</div>
</form>
@endsection

@section('js')
@endsection
