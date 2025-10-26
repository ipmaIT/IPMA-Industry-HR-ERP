@extends('layouts.app')

@section('content')
<?php
use \App\Models\HumanResources\OptWorkingHour;

use \Carbon\Carbon;
?>

<div class="col-sm-12 row">
	@include('humanresources.hrdept.navhr')
	<h4>Generate Working Hour For A Year</h4>
	<form method="POST" action="{{ route('workinghour.store') }}" accept-charset="UTF-8" id="form" autocomplete="off" class="" enctype="multipart/form-data">
		@csrf
	<div class="form-group row {{ ($errors->has('effective_date_start') || $errors->has('effective_date_end')) ? ' has-error' : '' }} mb-3 g-3">
		<label for="yea" class="col-form-label col-sm-2">Ramadhan Duration : </label>
		<div class="col-sm-5">
			<input type="text" name="effective_date_start" value="{{ old('effective_date_start') }}" id="effective_date_start" class="form-control form-control-sm col-sm-12 @error('effective_date_start') is-invalid @enderror" placeholder="Ramadhan Start">
		</div>
		<div class="col-sm-5">
			<input type="text" name="effective_date_end" value="{{ old('effective_date_end') }}" id="effective_date_end" class="form-control form-control-sm col-sm-12 @error('effective_date_end') is-invalid @enderror" placeholder="Ramadhan End">
		</div>
	</div>

	<div class="form-group row">
		<div class="col-sm-10 offset-sm-2">
			<button type="submit" class="btn btn-sm btn-outline-secondary">Generate Next Year Working Hour</button>
		</div>
	</div>
	</form>

</div>
@endsection

@section('js')
/////////////////////////////////////////////////////////////////////////////////////////
//date
$('#effective_date_start').datetimepicker({
	format: 'YYYY-MM-DD'
})
.on("dp.change dp.show dp.update", function (e) {
	var minDate = $('#effective_date_start').val();
	$('#effective_date_end').datetimepicker('minDate', minDate);
	$('#form').bootstrapValidator('revalidateField', 'effective_date_start');
});


$('#effective_date_end').datetimepicker({
	format: 'YYYY-MM-DD',
	useCurrent: false //Important! See issue #1075
})
.on("dp.change dp.show dp.update", function (e) {
	var maxDate = $('#effective_date_end').val();
	$('#effective_date_start').datetimepicker('maxDate', maxDate);
	$('#form').bootstrapValidator('revalidateField', 'effective_date_end');
});


/////////////////////////////////////////////////////////////////////////////////////////
// bootstrap validator

$('#form').bootstrapValidator({
	fields: {
		'effective_date_start': {
			validators: {
				notEmpty: {
					message: 'Please insert ramadhan date start. '
				},
				date: {
					format: 'YYYY-MM-DD',
					message: 'Please insert ramadhan date start. '
				},
				remote: {
					type: 'POST',
					url: '{{ route('yearworkinghourstart') }}',
					message: 'The duration of Ramadhan month for this year is already exist. Please choose another year',
					data: function(validator) {
								return {
											_token: '{!! csrf_token() !!}',
											effective_date_start: $('#effective_date_start').val(),
								};
							},
					delay: 1,		// wait 0.001 seconds
				},
			}
		},
		'effective_date_end': {
			validators: {
				notEmpty: {
					message: 'Please insert ramadhan date end. '
				},
				date: {
					format: 'YYYY-MM-DD',
					message: 'Please insert ramadhan date end. '
				},
				remote: {
					type: 'POST',
					url: '{{ route('yearworkinghourend') }}',
					message: 'The duration of Ramadhan month for this year is already exist. Please choose another year',
					data: function(validator) {
								return {
											_token: '{!! csrf_token() !!}',
											effective_date_end: $('#effective_date_end').val(),
								};
							},
					// delay: 1,		// wait 0.001 seconds
				},
			}
		},
	}
});
@endsection
