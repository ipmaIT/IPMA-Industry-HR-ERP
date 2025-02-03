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
	<title>{!! config('app.name') !!}</title>
	<link href="{{ asset('images/logo.png') }}" type="image/x-icon" rel="icon" />
	<meta name="keywords" content="erp system, erp" />
	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<!-- Styles -->
	<link href="{{ URL::asset('css/app.css') }}" rel="stylesheet">
	@stack('styles')
</head>
	<body class="bg-secondary bg-opacity-10">
		<div class="container-fluid row min-vh-100 align-items-center justify-content-center mx-auto">
			<header class="row align-self-start justify-content-center m-0">
				@include('layouts.navigation')
				<div class="col-sm-12 row align-self-start justify-content-center m-0">
					<div class="col-sm-4 text-center m-0">
						<a class="blog-header-logo text-body-emphasis text-decoration-none" href="{{ url('/') }}">{!! config('app.name') !!}</a>
					</div>
					<div class="col-sm-12 align-items-start justify-content-center m-0">
						@include('layouts.nav')
					</div>
					<div class="col-sm-12 align-items-start justify-content-center m-0">
						@include('layouts.messages')
					</div>
				</div>
			</header>
			<main class="col-sm-8 row justify-content-center m-0">
				<article class="blog-post d-flex justify-content-center align-items-center">
					@yield('content')
				</article>
			</main>
			<div class="container py-3 align-self-end text-center text-body-secondary bg-body-tertiary mx-auto m-0" >
				<p>{{ config('app.name') }} made from <a href="">Bootstrap</a> & <a href="">Laravel v.{{ app()->version() }}</a> by <a href="{{ url('/') }}">IPMA Industry Sdn Bhd</a>.</p>
			</div>
		</div>
	</body>

	<script src="{{ mix('js/fullcalendar/index.global.js') }}"></script>
	<script src="{{ mix('js/chart.js/dist/chart.umd.js') }}"></script>
	<script src="{{ mix('js/app.js') }}"></script>
	<script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
	<script src="{{ asset('js/ckeditor/adapters/jquery.js') }}"></script>
	<script >
		jQuery.noConflict ();
		(function($){
			$(document).ready(function(){
				@section('js')
				@show
			});
		})(jQuery);
	</script>
	<script>
		@section('nonjquery')
		@show
	</script>
	@stack('scripts')
</html>
