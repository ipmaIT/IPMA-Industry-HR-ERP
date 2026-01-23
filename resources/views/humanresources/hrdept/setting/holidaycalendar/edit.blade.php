@extends('layouts.app')

@section('content')
<?php
use \App\Models\HumanResources\OptWorkingHour;

use \Carbon\Carbon;
?>

<div class="col-sm-12 row">
	@include('humanresources.hrdept.navhr')
	<h4>Add Holiday Calendar</h4>
	<form method="POST" action="{{ route('holidaycalendar.update', $holidaycalendar) }}" accept-charset="UTF-8" id="form" autocomplete="off" class="" enctype="multipart/form-data">
		@csrf
		@method('PATCH')
		<div class="row mb-3 g-3 " style="position: relative">
			<label for="yea" class="col-form-label col-sm-2">Date Range : </label>
			<div class="form-group col-sm-5 {{ $errors->has('date_start')?'has-error':'' }}">
				<input type="text" name="date_start" value="{{ old('date_start', $holidaycalendar->date_start) }}" id="dstart" class="form-control form-control-sm col-sm-12 @error('date_start') is-invalid @enderror" placeholder="Date Start">
			</div>
			<div class="form-group col-sm-5 {{ $errors->has('date_end')?'has-error':'' }}">
				<input type="text" name="date_end" value="{{ old('date_end', $holidaycalendar->date_end) }}" id="dend" class="form-control form-control-sm col-sm-12 @error('date_end') is-invalid @enderror" placeholder="Date End">
			</div>
		</div>

		<div class="form-group row mb-3 g-3 {{ $errors->has('holiday')?'has-error':'' }}">
			<label for="hol" class="col-form-label col-sm-2">Holiday : </label>
			<div class="col-sm-10">
				<input type="text" name="holiday" value="{{ old('holiday', $holidaycalendar->holiday) }}" id="hol" class="form-control form-control-sm col-sm-12 @error('holiday') is-invalid @enderror" placeholder="Holiday">
			</div>
		</div>

		<div class="form-group row mb-3 g-3 {{ $errors->has('remarks')?'has-error':'' }}">
			<label for="rem" class="col-form-label col-sm-2">Remarks : </label>
			<div class="col-sm-10">
				<textarea name="remarks" id="rem" class="form-control form-control-sm col-sm-12 @error('remarks') is-invalid @enderror" placeholder="Remarks">{{ old('remarks', $holidaycalendar->remarks) }}</textarea>
			</div>
		</div>

		<div class="form-group row mb-3 g-3">
			<div class="col-sm-10 offset-sm-2">
				<button type="submit" class="btn btn-sm btn-outline-secondary">Update Holiday</button>
			</div>
		</div>
	</form>

</div>
@endsection

@section('js')
/////////////////////////////////////////////////////////////////////////////////////////
$('#dstart').datetimepicker({
	format: 'YYYY-MM-DD',
	useCurrent: false, //Important! See issue #1075
})
.on("dp.change dp.show dp.update", function (e) {
	var minDate = $('#dstart').val();
	$('#dend').datetimepicker('minDate', minDate);
	$('#form').bootstrapValidator('revalidateField', 'date_start');
});


$('#dend').datetimepicker({
	format: 'YYYY-MM-DD',
	useCurrent: false, //Important! See issue #1075
})
.on("dp.change dp.show dp.update", function (e) {
	var maxDate = $('#dend').val();
	$('#dstart').datetimepicker('maxDate', maxDate);
	$('#form').bootstrapValidator('revalidateField', 'date_end');
});

/////////////////////////////////////////////////////////////////////////////////////////
// bootstrap validator
$('#form').bootstrapValidator({
	fields: {
		'date_start': {
			validators: {
				notEmpty: {
					message: 'Please insert holiday date start. '
				},
				date: {
					format: 'YYYY-MM-DD',
					message: 'Please insert holiday date start. '
				},
				// remote: {
				// 	type: 'POST',
				// 	url: '{{ route('hcaldstart') }}',
				// 	message: 'The date is already exist. Please choose another date. ',
				// 	data: function(validator) {
				// 				return {
				// 							_token: '{!! csrf_token() !!}',
				// 							date_start: $('#dstart').val(),
				// 				};
				// 			},
				// 	//delay: 1,		// wait 0.001 seconds
				// },
			}
		},
		'date_end': {
			validators: {
				notEmpty: {
					message: 'Please insert holiday date end. '
				},
				date: {
					format: 'YYYY-MM-DD',
					message: 'Please insert holiday date end. '
				},
				// remote: {
				// 	type: 'POST',
				// 	url: '{{ route('hcaldend') }}',
				// 	message: 'The date is already exist. Please choose another date. ',
				// 	data: function(validator) {
				// 				return {
				// 							_token: '{!! csrf_token() !!}',
				// 							date_end: $('#dend').val(),
				// 				};
				// 			},
				// 	delay: 1,		// wait 0.001 seconds
				// },
			}
		},
		'holiday': {
			validators: {
				notEmpty: {
					message: 'Please insert the name of the holiday. '
				}
			}
		},
	}
});

@endsection
