@extends('layouts.app')

@section('content')
<?php
use \App\Models\HumanResources\OptWorkingHour;

use \Carbon\Carbon;
?>

<div class="col-sm-12 row">
	@include('humanresources.hrdept.navhr')
	<h4>Edit Working Hour</h4>
	<form method="POST" action="{{ route('workinghour.update', $workinghour) }}" accept-charset="UTF-8" id="form" autocomplete="off" class="" enctype="multipart/form-data">
		@csrf
		@method('PATCH')

			<div class="form-group row {{ $errors->has('time') ? 'has-error' : '' }} mb-3 g-3">
				<label for="dstart1" class="col-form-label col-sm-2">Time : </label>
				<div class=" col-sm-2">
					<input type="text" name="time_start_am" value="{{ old('time_start_am', $workinghour->time_start_am) }}" id="tsa" class="form-control form-control-sm col-sm-12 @error('time_start_am') is-invalid @enderror" placeholder="1st Half Time Start">
				</div>
				<div class="col-sm-2">
					<input type="text" name="time_end_am" value="{{ old('time_end_am', $workinghour->time_end_am) }}" id="tea" class="form-control form-control-sm col-sm-12 @error('time_end_am') is-invalid @enderror" placeholder="1st Half Time End">
				</div>
				<div class="col-sm-2">
					<input type="text" name="time_start_pm" value="{{ old('time_start_pm', $workinghour->time_start_pm) }}" id="tsp" class="form-control form-control-sm col-sm-12 @error('time_start_pm') is-invalid @enderror" placeholder="2nd Half Time Start">
				</div>
				<div class="col-sm-2">
					<input type="text" name="time_end_pm" value="{{ old('time_end_pm', $workinghour->time_end_pm) }}" id="tep" class="form-control form-control-sm col-sm-12 @error('time_end_pm') is-invalid @enderror" placeholder="2nd Half Time End">
				</div>
			</div>

			<div class="form-group row {{ $errors->has('date') ? 'has-error' : '' }}  mb-3 g-3">
				<label for="dstart2" class="col-form-label col-sm-2">Effective Date : </label>
				<div class="col-sm-5">
					<input type="text" name="effective_date_start" value="{{ old('effective_date_start', $workinghour->effective_date_start) }}" id="eds" class="form-control form-control-sm col-sm-12 @error('effective_date_start') is-invalid @enderror" placeholder="Effective Date Start">
				</div>
				<div class="col-sm-5">
					<input type="text" name="effective_date_end" value="{{ old('effective_date_end', $workinghour->effective_date_end) }}" id="ede" class="form-control form-control-sm col-sm-12 @error('effective_date_end') is-invalid @enderror" placeholder="Effective Date End">
				</div>
			</div>

			<div class="form-group row {{ $errors->has('time') ? 'has-error' : '' }}  mb-3 g-3">
				<label for="dstart3" class="col-form-label col-sm-2">Remarks : </label>
				<div class="col-sm-10">
					<textarea name="remarks" id="dstart3" class="form-control form-control-sm col-sm-12 @error('remarks') is-invalid @enderror" placeholder="Remarks">{{ old('remarks', $workinghour->remarks) }}</textarea>
				</div>
			</div>

			<div class="form-group row  mb-3 g-3">
				<div class="col-sm-10 offset-sm-2">
					<button type="submit" class="btn btn-sm btn-outline-secondary">Submit</button>
				</div>
			</div>

	</form>

</div>
@endsection

@section('js')
/////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////
// time
$('#tsa').datetimepicker({
	format: 'h:mm A',
	// enabledHours: [8, 9, 10, 11, 12],
	useCurrent: false, //Important! See issue #1075
})
.on('dp.change dp.show dp.update', function(){
	$('#form').bootstrapValidator('revalidateField', 'time_start_am');
});

$('#tea').datetimepicker({
	format: 'h:mm A',
	// enabledHours: [8, 9, 10, 11, 12],
	useCurrent: false, //Important! See issue #1075
})
.on('dp.change dp.show dp.update', function(){
	$('#form').bootstrapValidator('revalidateField', 'time_end_am');
});

$('#tsp').datetimepicker({
	format: 'h:mm A',
	// enabledHours: [13, 14, 15, 16, 17],
	useCurrent: false, //Important! See issue #1075
})
.on('dp.change dp.show dp.update', function(){
	$('#form').bootstrapValidator('revalidateField', 'time_start_pm');
});

$('#tep').datetimepicker({
	format: 'h:mm A',
	// enabledHours: [13, 14, 15, 16, 17],
	useCurrent: false, //Important! See issue #1075
})
.on('dp.change dp.show dp.update', function(){
	$('#form').bootstrapValidator('revalidateField', 'time_end_pm');
});

/////////////////////////////////////////////////////////////////////////////////////////
$('#eds').datetimepicker({
	format: 'YYYY-MM-DD',
	useCurrent: false, // Important! See issue #1075
})
.on('dp.change dp.show dp.update', function(){
	var mintar = $('#eds').val();
	$('#ede').datetimepicker( 'minDate', mintar );
	$('#form').bootstrapValidator('revalidateField', 'effective_date_start');
});

$('#ede').datetimepicker({
	format: 'YYYY-MM-DD',
	useCurrent: false, // Important! See issue #1075
})
.on('dp.change dp.show dp.update', function(){
	var maxtar = $('#ede').val();
	$('#eds').datetimepicker( 'maxDate', maxtar );
	$('#form').bootstrapValidator('revalidateField', 'effective_date_end');
});

/////////////////////////////////////////////////////////////////////////////////////////
// validator
$(document).ready(function() {
	$('#form').bootstrapValidator({
		fields: {
			time_start_am: {
				validators: {
					notEmpty: {
						message: 'Please insert time',
					},
					regexp: {
						regexp: /^([1-5]|[8-9]|1[0-2]):([0-5][0-9])\s([A|P]M|[a|p]m)$/i,
						message: 'The value is not a valid time',
					}
				}
			},
			time_end_am: {
				validators: {
					notEmpty: {
						message: 'Please insert time',
					},
					regexp: {
						regexp: /^([1-5]|[8-9]|1[0-2]):([0-5][0-9])\s([A|P]M|[a|p]m)$/i,
						message: 'The value is not a valid time',
					}
				}
			},
			time_start_pm: {
				validators: {
					notEmpty: {
						message: 'Please insert time',
					},
					regexp: {
						regexp: /^([1-5]|[8-9]|1[0-2]):([0-5][0-9])\s([A|P]M|[a|p]m)$/i,
						message: 'The value is not a valid time',
					}
				}
			},
			time_end_pm: {
				validators: {
					notEmpty: {
						message: 'Please insert time',
					},
					regexp: {
						regexp: /^([1-6]|[8-9]|1[0-2]):([0-5][0-9])\s([A|P]M|[a|p]m)$/i,
						message: 'The value is not a valid time',
					}
				}
			},
			effective_date_start: {
				validators: {
					notEmpty : {
						message: 'Please insert date start'
					},
					date: {
						format: 'YYYY-MM-DD',
						message: 'The value is not a valid date. '
					},
				}
			},
			effective_date_end: {
				validators: {
					notEmpty : {
						message: 'Please insert date end'
					},
					date: {
						format: 'YYYY-MM-DD',
						message: 'The value is not a valid date. '
					},
				}
			},
		}
	})
	// .find('[name="reason"]')
	// .ckeditor()
	// .editor
	// 	.on('change', function() {
	// 		// Revalidate the bio field
	// 	$('#form').bootstrapValidator('revalidateField', 'reason');
	// 	// console.log($('#reason').val());
	// })
	;
});
@endsection
