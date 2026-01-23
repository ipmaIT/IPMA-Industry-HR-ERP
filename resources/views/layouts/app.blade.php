<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">
<?php
use \Carbon\Carbon;

$currentYear = Carbon::now()->year;
?>
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
	<link href="{{ asset('css/app.css') }}" rel="stylesheet">
	<!-- <link href="{{ asset('bootstrap.css') }}" rel="stylesheet"> -->
	@stack('styles')
</head>
	<!-- <body class="bg-secondary bg-opacity-10"> -->
	<body class="d-flex flex-column bg-secondary bg-opacity-10 min-vh-100 ">

		@include('layouts.navigation')

			<div class="container-fluid mx-auto my-2">

				<div class="col-sm-12 align-items-top justify-content-center m-0">
					@include('layouts.nav')
				</div>

		</div>

		<div class="container-fluid d-flex flex-fill p-1 mx-auto">

			<div class="container mx-auto border border-success rounded">

				<div class="col-sm-12 mx-auto">
					@include('layouts.messages')
				</div>

				<main class="col-sm-12 mx-auto row justify-content-center m-0">
					@yield('content')
				</main>

			</div>

		</div>

		<div class="container-fluid row mt-2">
			<div class="col-sm-12 d-flex justify-content-center">
				<p class="m-0 fs-6 text-sm text-light-emphasis">
					<a href="{{ config('app.url') }}">{{ config('app.name') }}</a> develop using <a href="">Laravel v.{{ app()->version() }}</a></p>
			</div>
			<div class="col-sm-12 d-flex justify-content-center">
				<small class="m-0 fw-lighter fs-6 text-body-secondary">Made with love by Dhiauddin and Tan</small>
			</div>
		</div>
	</body>

	<script>
  	window.FontAwesomeConfig = { autoReplaceSvg: false, observeMutations: false };
	</script>
	<script src="{{ mix('js/app.js') }}"></script>
	<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
	<script src="{{ asset('js/ckeditor/adapters/jquery.js') }}"></script>
	<script src="{{ asset('js/jquery-chained/jquery.chained.js') }}"></script>
	<script src="{{ asset('js/jquery-chained/jquery.chained.remote.js') }}"></script>
	<script type="module">
		jQuery.noConflict ();
		(function($){
			$(document).ready(function(){
				@section('js')
				@show
			});
		})(jQuery);
	</script>
	@stack('scripts')
</html>
