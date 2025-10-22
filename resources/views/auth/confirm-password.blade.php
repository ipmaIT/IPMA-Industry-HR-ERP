@extends('layouts.app')

@section('content')
	<div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
		{{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
	</div>

	<form method="POST" action="{{ route('password.confirm') }}">
		@csrf

		<!-- Password -->
		<div>
			<label for="password" class="col-sm-2 col-form-label col-form-label-sm">Password : </label>
			<input type="password" name="password" value="" class="block mt-1 w-full" id="password" placeholder="Username">
			<x-input-error :messages="$errors->get('password')" class="mt-2" />
			<div class="mt-2">{{ $errors->get('password') }}</div>
		</div>

		<div class="flex justify-end mt-4">
			<button type="submit" class="'btn btn-sm btn-outline-secondary">Confirm</button>
		</div>
	</form>
@endsection
@section('js')
@endsection
