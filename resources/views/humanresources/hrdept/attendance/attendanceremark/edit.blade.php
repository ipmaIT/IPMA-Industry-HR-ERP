@extends('layouts.app')

@section('content')
<?php
use App\Models\Staff;
use App\Models\Login;
$s = Staff::where('active', 1)->get();
foreach ($s as $v) {
		$ls[$v->id] = Login::where([['active', 1], ['staff_id', $v->id]])->first()?->username.'  '.$v->name;
}
?>
<div class="container row align-items-start justify-content-center">
	@include('humanresources.hrdept.navhr')
	<h4>Add Remarks Attendance</h4>
  <form method="POST" action="{{ route('attendanceremark.update', $attendanceremark->id) }}" accept-charset="UTF-8" id="form" autocomplete="off" class="" enctype="multipart/form-data">
  @csrf
  @method('PATCH')

	<div class="form-group row m-3 {{ $errors->has('staff_id') ? 'has-error' : NULL }}">
		<label for="sta" class="col-sm-4 col-form-label">Staff : </label>
		<div class="col-md-8">
			<select name="staff_id" id="sta" class="form-select form-select-sm">
				<option value="">Please choose</option>
				@foreach($ls as $k => $v)
				<option value="{{ $k }}" {{ ($k == old('staff_id', $attendanceremark->staff_id))?'selected':NULL }}>{{ $v }}</option>
				@endforeach
			</select>
		</div>
	</div>

	<div class="form-group row m-3 {{ $errors->has('date_from') ? 'has-error' : NULL }}">
		<label for="from" class="col-sm-4 col-form-label">From : </label>
		<div class="col-md-8" style="position: relative;">
			<input type="text" name="date_from" value="{{ old('date_from', $attendanceremark->date_from) }}" id="from" class="form-control form-control-sm @error('date_from') 'is-invalid' @enderror" placeholder="Date From">
		</div>
	</div>

	<div class="form-group row m-3 {{ $errors->has('date_to') ? 'has-error' : NULL }}">
		<label for="to" class="col-sm-4 col-form-label">To : </label>
		<div class="col-md-8" style="position: relative;">
			<input type="text" name="date_to" value="{{ old('date_to', $attendanceremark->date_to) }}" id="to" class="form-control form-control-sm @error('date_to') 'is-invalid' @enderror" placeholder="Date To">
		</div>
	</div>

	<div class="form-group row m-3 {{ $errors->has('attendance_remarks') ? 'has-error' : NULL }}">
		<label for="ar" class="col-sm-4 col-form-label">Attendance Remarks : </label>
		<div class="col-md-8">
			<textarea name="attendance_remarks" id="ar" class="form-control form-control-sm @error('attendance_remarks') 'is-invalid' @enderror">{{ old('attendance_remarks', $attendanceremark->attendance_remarks) }}</textarea>
		</div>
	</div>

	<div class="form-group row m-3 {{ $errors->has('hr_attendance_remarks') ? 'has-error' : NULL }}">
		<label for="hrar" class="col-sm-4 col-form-label">HR Attendance Remarks : </label>
		<div class="col-md-8">
			<textarea name="hr_attendance_remarks" id="hrar" class="form-control form-control-sm @error('hr_attendance_remarks') 'is-invalid' @enderror">{{ old('hr_attendance_remarks', $attendanceremark->hr_attendance_remarks) }}</textarea>
		</div>
	</div>

	<div class="form-group row m-3 {{ $errors->has('remarks') ? 'has-error' : NULL }}">
		<label for="rem" class="col-sm-4 col-form-label">Remarks : </label>
		<div class="col-md-8">
			<textarea name="remarks" id="rem" class="form-control form-control-sm @error('remarks') 'is-invalid' @enderror">{{ old('remarks', $attendanceremark->remarks) }}</textarea>
		</div>
	</div>

	<div class="col-sm-8 offset-sm-4">
		<button type="submit" class="btn btn-sm btn-outline-secondary">Update Remarks</button>
	</div>
	</form>
</div>
@endsection

@section('js')
/////////////////////////////////////////////////////////////////////////////////////////
$('#sta').select2({
	placeholder: 'Please choose',
	allowClear: true,
	closeOnSelect: true,
});

/////////////////////////////////////////////////////////////////////////////////////////
//date
$('#from').datetimepicker({
	icons: {
		time: "fas fas-regular fa-clock fa-beat",
		date: "fas fas-regular fa-calendar fa-beat",
		up: "fa-regular fa-circle-up fa-beat",
		down: "fa-regular fa-circle-down fa-beat",
		previous: 'fas fas-regular fa-arrow-left fa-beat',
		next: 'fas fas-regular fa-arrow-right fa-beat',
		today: 'fas fas-regular fa-calenday-day fa-beat',
		clear: 'fas fas-regular fa-broom-wide fa-beat',
		close: 'fas fas-regular fa-rectangle-xmark fa-beat'
	},
	format:'YYYY-MM-DD',
	// useCurrent: false,
})
.on("dp.change dp.show dp.update", function (e) {
	$('#to').datetimepicker('minDate', $('#from').val());
	$('#form').bootstrapValidator('revalidateField', 'date_from');
});


$('#to').datetimepicker({
	icons: {
		time: "fas fas-regular fa-clock fa-beat",
		date: "fas fas-regular fa-calendar fa-beat",
		up: "fa-regular fa-circle-up fa-beat",
		down: "fa-regular fa-circle-down fa-beat",
		previous: 'fas fas-regular fa-arrow-left fa-beat',
		next: 'fas fas-regular fa-arrow-right fa-beat',
		today: 'fas fas-regular fa-calenday-day fa-beat',
		clear: 'fas fas-regular fa-broom-wide fa-beat',
		close: 'fas fas-regular fa-rectangle-xmark fa-beat'
	},
	format: 'YYYY-MM-DD',
	// useCurrent: false //Important! See issue #1075
})
.on("dp.change dp.show dp.update", function (e) {
	$('#from').datetimepicker('maxDate', $('#to').val());
	$('#form').bootstrapValidator('revalidateField', 'date_to');
});


/////////////////////////////////////////////////////////////////////////////////////////
// bootstrap validator
$('#form').bootstrapValidator({
	feedbackIcons: {
		valid: '',
		invalid: '',
		validating: ''
	},
	fields: {
		'staff_id': {
			validators: {
				notEmpty: {
					message: 'Please choose '
				},
			}
		},
		'date_from': {
			validators: {
				notEmpty: {
					message: 'Please insert date from. '
				},
				date: {
					format: 'YYYY-MM-DD',
					message: 'Please insert date from. '
				},
			}
		},
		'date_to': {
			validators: {
				notEmpty: {
					message: 'Please insert date to. '
				},
				date: {
					format: 'YYYY-MM-DD',
					message: 'Please insert date to. '
				},
			}
		},
	}
});
@endsection
