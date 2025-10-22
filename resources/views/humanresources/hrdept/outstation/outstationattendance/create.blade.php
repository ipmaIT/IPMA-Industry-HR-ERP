@extends('layouts.app')

@section('content')
<div class="col-sm-12 row align-items-start justify-content-center">
	@include('humanresources.hrdept.navhr')
	<h4>Add Staff For Outstation Attendance</h4>
	<div class="col-sm-12 row">
	<form method="POST" action="{{ route('hroutstationattendance.store') }}" accept-charset="UTF-8" id="form" autocomplete="off" class="" enctype="multipart/form-data">
		@csrf

		<div class="form-group row m-3 @error('date_attend') is-invalid @enderror">
			<label for="date" class="col-sm-4 col-form-label">Attend Date : </label>
			<div class="col-sm-8" style="position:relative;">
				<input type="text" name="date_attend" value="{{ old('date_attend') }}" id="date" class="form-control form-control-sm col-sm-12 @error('date_attend') is-invalid @enderror" placeholder="Date Attend">
			</div>
		</div>

		<div class="form-group row m-3 @error('outstation_id') has-error @enderror">
			<label for="loc" class="col-sm-4 col-form-label">Location : </label>
			<div class="col-sm-8">
				<select name="outstation_id" id="loc" class="form-select form-select-sm col-sm-5"></select>
			</div>
		</div>

		<div class="form-group row m-3 @error('staff_id.*') has-error @enderror">
			<label for="staff" class="col-sm-4 col-form-label">Staff : </label>
			<div class="col-sm-8">
				<select name="staff_id[]" id="staff" class="form-select form-select-sm col-sm-5" multiple="multiple"></select>
			</div>
		</div>

		<div class="form-group row m-3 @error('in') has-error @enderror">
			<label for="in" class="col-sm-4 col-form-label">In : </label>
			<div class="col-sm-8" style="position:relative;">
				<input type="text" name="in" value="{{ old('in') }}" id="in" class="form-control form-control-sm col-sm-12 @error('in') is-invalid @enderror" placeholder="In">
			</div>
		</div>

		<div class="form-group row m-3 @error('out') has-error @enderror">
			<label for="out" class="col-sm-4 col-form-label">Out : </label>
			<div class="col-sm-8" style="position:relative;">
				<input type="text" name="out" value="{{ old('out') }}" id="out" class="form-control form-control-sm col-sm-12 @error('out') is-invalid @enderror" placeholder="Out">
			</div>
		</div>

		<div class="form-group row m-3 {{ $errors->has('in') ? 'has-error' : Null }}">
			<label for="remarks" class="col-sm-4 col-form-label">Remarks : </label>
			<div class="col-sm-8">
				<textarea name="remarks" id="remarks" class="form-control form-control-sm col-sm-12 @error('remarks') is-invalid @enderror">{{ old('remarks') }}</textarea>
			</div>
		</div>

		<div class="offset-sm-4 col-sm-8">
			<button type="submit" class="btn btn-sm btn-outline-secondary">Generate Attendance</button>
		</div>

		</form>
	</div>
</div>
@endsection

@section('js')
/////////////////////////////////////////////////////////////////////////////////////////
//date
$('#date').datetimepicker({
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
	useCurrent: true,
})
.on('dp.change dp.update', function(e) {
	// console.log(e);

	//enable select 2 for backup
	$('#loc').select2({
		placeholder: 'Please Choose',
		width: '100%',
		ajax: {
			url: '{{ route('outstationattendancelocation') }}',
			// data: { '_token': '{!! csrf_token() !!}' },
			type: 'POST',
			dataType: 'json',
			data: function (params) {
				var query = {
					_token: '{!! csrf_token() !!}',
					date_attend: $('#date').val(),
					search: params.term,
					type: 'public'
				}
				return query;
			}
		},
		allowClear: true,
		closeOnSelect: true,
	});

	// get staff
	$('#loc').on('change, select2:select', function (e) {
		// console.log($('#loc').val());

		$('#staff').select2({
			placeholder: 'Please Choose',
			width: '100%',
			ajax: {
				url: '{{ route('outstationattendancestaff') }}',
				// data: { '_token': '{!! csrf_token() !!}' },
				type: 'POST',
				dataType: 'json',
				data: function (params) {
					var query = {
						_token: '{!! csrf_token() !!}',
						outstation_id: $('#loc').val(),
						date_attend: $('#date').val(),
						search: params.term,
					}
					return query;
				}
			},
			allowClear: true,
			closeOnSelect: true,
		});
	});
});

/////////////////////////////////////////////////////////////////////////////////////////
$('#in, #out').datetimepicker({
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
	format: 'h:mm A',
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
		'staff_id[]': {
			validators: {
				notEmpty: {
					message: 'Please choose '
				},
			}
		},
		'date_attend': {
			validators: {
				notEmpty: {
					message: 'Please insert date. '
				},
				date: {
					format: 'YYYY-MM-DD',
					message: 'Please insert date. '
				},
			}
		},
		'outstation_id': {
			validators: {
				notEmpty: {
					message: 'Please choose. '
				},
			}
		},
	}
});
@endsection
