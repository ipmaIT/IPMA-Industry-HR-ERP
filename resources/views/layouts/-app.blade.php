<?php
use \Carbon\Carbon;
$currentYear = Carbon::now()->year;
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="" type="image/x-icon" rel="icon" />
	<meta name="description" content="">
	<meta name="keywords" content="erp system, erp" />
	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>{!! config('app.name') !!}</title>
	<link href="{{ asset('images/logo.png') }}" type="image/x-icon" rel="icon" />
	<!-- Styles -->
	<link href="{{ mix('css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
	@stack('styles')
</head>
<body class="bg-primary-sibtle bg-opacity-75 min-vh-100 d-flex flex-column" data-route="{{ Route::currentRouteName() }}">

	@include('layouts.navigation')

	<div class="container-fluid flex-fill d-flex flex-column">

			@include('layouts.nav')

			<div class="container-fluid p-1 mx-auto d-flex justify-content-between flex-fill">

				<div class="col-sm-1 m-0">
				</div>

				<div class="col-sm-10 m-0 my-2 p-1 row align-self-center border border-success rounded">

					<div class="col-sm-12 mx-auto">
						@include('layouts.messages')
					</div>

					<div class="col-sm-12 m-0 my-2 p-1 d-flex justify-content-center">
						@yield('content')
					</div>

				</div>

				<div class="col-sm-1 m-0">
				</div>

			</div>

		</div>

	</div>

	<!-- footer -->
	<div class="container m-0 mx-auto py-1 align-self-end text-center text-sm text-light-emphasis">
		<a href="{{ config('app.url') }}">&copy; {{ config('app.name') }}</a> develop using <a href="">Laravel v.{{ app()->version() }}</a>
		<br/>
		<small class="m-0 fw-lighter fs-6 text-body-secondary">Made with love by Dhiauddin and Tan</small>
	</div>
	<!-- footer end -->

</body>

	<script>
	</script>
	<script src="{{ mix('js/app.js') }}"></script>
	<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
	<script src="{{ asset('js/ckeditor/adapters/jquery.js') }}"></script>
	<script src="{{ asset('js/jquery-chained/jquery.chained.js') }}"></script>
	<script src="{{ asset('js/jquery-chained/jquery.chained.remote.js') }}"></script>
	<script>
		(function($){
			$(document).ready(function(){
				@section('js')
				@show
			});
		})(jQuery);
	</script>
	@stack('scripts')
</html>
