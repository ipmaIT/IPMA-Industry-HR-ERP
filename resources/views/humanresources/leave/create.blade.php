@extends('layouts.app')
@section('content')
<div class="container row align-items-start justify-content-center">
	<div class="table-responsive col-sm-12 m-5">
		<table class="table table-hover table-sm">
			<tbody>
				<tr class="">
					<td rowspan="3" class="text-danger w-25">Attention :</td>
					<td>
						Leave application must be at least <span class="font-weight-bold">THREE (3)</span> days in advance for <strong>"Annual Leave"</strong> and <strong>"Unpaid Leave"</strong>. Otherwise it will be considered as <strong>"Emergency Annual Leave"</strong> or <strong>"Emergency Unpaid Leave"</strong>
					</td>
				</tr>
				<tr>
					<td>
						<strong>"Time-Off"</strong> will consider as a <strong>"Leave"</strong>, if leave period exceed <strong>more than 2 hours</strong>.
					</td>
				</tr>
				<tr>
					<td>
						Application for <strong>"Sick Leave/Medical Certificate (MC)"</strong> or <strong>"Unpaid Medical Certificate (MC-UPL)"</strong> will only be <strong>considered VALID and ELIGIBLE</strong> if a sick/medical certificate is <strong>issued by a REGISTERED government hospital/clinic or panel clinic only.
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<!-- herecomes the hardest part, leave application -->

	<div class="col-sm-12 row">
		{{ Form::open(['route' => ['leave.store'], 'id' => 'form', 'autocomplete' => 'off', 'files' => true,  'data-toggle' => 'validator']) }}
		<h5 class="text-center">Leave Application</h5>

		<div class="form-group row m-2 {{ $errors->has('leave_id') ? 'has-error' : '' }}">
			{{ Form::label( 'leave_type_id', 'Leave Type : ', ['class' => 'col-sm-4 col-form-label'] ) }}
			<div class="col-sm-8">
				<select name="leave_type_id" id="leave_id" class="form-control form-control-sm"></select>
			</div>
		</div>

		<div class="form-group row m-2 {{ $errors->has('reason') ? 'has-error' : '' }}">
			{{ Form::label( 'reason', 'Reason : ', ['class' => 'col-sm-4 col-form-label'] ) }}
			<div class="col-sm-8">
				{{ Form::textarea('reason', @$value, ['class' => 'form-control form-control-sm ', 'id' => 'reason', 'placeholder' => 'Reason', 'autocomplete' => 'off']) }}
			</div>
		</div>

		<div id="wrapper">
		</div>

		<div class="form-group row m-2 {{ $errors->has('akuan') ? 'has-error' : '' }}">
			<div class="col-sm-8 offset-sm-4 form-check">
				{{ Form::checkbox('akuan', 1, @$value, ['class' => 'form-check-input ', 'id' => 'akuan1']) }}
				<label for="akuan1" class="form-check-label p-1 bg-warning text-danger rounded"><p>I hereby confirmed that all details and information filled in are <strong>CORRECT</strong> and <strong>CHECKED</strong> before sending.</p></label>
			</div>
		</div>

		<div class="form-group row mb-3">
			<div class="col-sm-8 offset-sm-4">
				{!! Form::button('Submit Application', ['class' => 'btn btn-sm btn-primary', 'type' => 'submit']) !!}
			</div>
		</div>
		{{ Form::close() }}
	</div>
</div>

@endsection
@section('js')
/////////////////////////////////////////////////////////////////////////////////////////
$('#leave_id').select2({
	placeholder: 'Please choose',
	allowClear: true,
	closeOnSelect: true,
	width: '100%',
	ajax: {
		url: '{{ route('leaveType.leaveType') }}',
		// data: { '_token': '{!! csrf_token() !!}' },
		type: 'POST',
		dataType: 'json',
		data: function () {
			var data = {
				id: {{ \Auth::user()->belongstostaff->id }},
				_token: '{!! csrf_token() !!}',
			}
			return data;
		}
	},
});

/////////////////////////////////////////////////////////////////////////////////////////
//enable ckeditor
// its working, i just disable it
// $(document).ready(function() {
// 	var editor = CKEDITOR.replace( 'reason', {});
// 	// editor is object of your CKEDITOR
// 	editor.on('change',function(){
// 	     // console.log();
// 	    $('#form').bootstrapValidator('revalidateField', 'reason');
// 	});
// });
// // with jquery adapter
// $('textarea#reason').ckeditor();

/////////////////////////////////////////////////////////////////////////////////////////
// start setting up the leave accordingly.
<?php
$user = \Auth::user()->belongstostaff;
$userneedbackup = $user->belongstoleaveapprovalflow?->backup_approval;
$setHalfDayMC = \App\Models\Setting::find(2)->active;
?>

/////////////////////////////////////////////////////////////////////////////////////////
//  global variable : ajax to get the unavailable date

function getUnavailableDates(type) {
	var result;
	$.ajax({
		url: "{{ route('leavedate.unavailabledate') }}",
		type: "POST",
		data: {
			id: {{ \Auth::user()->belongstostaff->id }},
			type: type,
			_token: '{!! csrf_token() !!}',
		},
		dataType: 'json',
		async: false, // synchronous
		success: function (response) {
			// response is already parsed into JS array/object
			result = response;
		},
		error: function (jqXHR, textStatus, errorThrown) {
			console.log(textStatus, errorThrown);
			result = []; // fallback in case of error
		}
	});
	return result;
}

/////////////////////////////////////////////////////////////////////////////////////////
// checking for overlapp leave on half day (if it is turn on)
function getUnblockhalfdayleave() {
	var result;
	$.ajax({
		url: "{{ route('unblockhalfdayleave.unblockhalfdayleave') }}",
		type: "POST",
		data: {
			id: {{ \Auth::user()->belongstostaff->id }},
			_token: '{!! csrf_token() !!}',
		},
		dataType: 'json',
		async: false, // synchronous
		success: function (response) {
			// response is already parsed into JS array/object
			result = response;
		},
		error: function (jqXHR, textStatus, errorThrown) {
			console.log(textStatus, errorThrown);
			result = []; // fallback in case of error
		}
	});
	return result;
}

const datetimeIcons = {
	time: "fas fa-regular fa-clock fa-beat",
	date: "fas fa-regular fa-calendar fa-beat",
	up: "fa-regular fa-circle-up fa-beat",
	down: "fa-regular fa-circle-down fa-beat",
	previous: 'fas fa-regular fa-arrow-left fa-beat',
	next: 'fas fa-regular fa-arrow-right fa-beat',
	today: 'fas fa-regular fa-calendar-day fa-beat',
	clear: 'fas fa-regular fa-broom-wide fa-beat',
	close: 'fas fa-regular fa-rectangle-xmark fa-beat'
};

function initDatepicker(selector, no) {
	let options = {
		icons: datetimeIcons,
		format: 'YYYY-MM-DD',
		useCurrent: false,
		disabledDates: getUnavailableDates(no),
	};
	if (no === 1) {
		options.minDate = moment().format('YYYY-MM-DD');
	}
	return $(selector).datetimepicker(options);
}

function getHalfdayInfo(selector) {
	let d = false, itime_start = 0, itime_end = 0;
	$.each(getUnblockhalfdayleave(), function() {
		if (this.date_half_leave == selector) {
			d = true;
			itime_start = this.time_start;
			itime_end = this.time_end;
			return false; // break
		}
	});
	return [d, itime_start, itime_end];
}

function getTimeLeave(date) {
	let result = null;
	$.ajax({
		url: "{{ route('leavedate.timeleave') }}",
		type: "POST",
		data: {
			date: date,
			_token: '{!! csrf_token() !!}',
			id: {{ \Auth::user()->belongstostaff->id }}
		},
		dataType: 'json',
		async: false, // blocking
		success: function (response) {
			result = response;
		},
		error: function(xhr, status, error) {
			console.error("Error fetching timeleave:", status, error);
		}
	});
	return result;
}

// 🔹 This is the reusable logic for half-day checks
function handleHalfDay(date) {
	let [d, itime_start, itime_end] = getHalfdayInfo(date);

	if (d === true) {
		$('#wrapperday').append(leave_cat);
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

		let obj = getTimeLeave(date);

		let toggle_time_start_am = '', toggle_time_start_pm = '';
		let checkedam = 'checked', checkedpm = 'checked';

		if (obj.time_start_am == itime_start) {
			toggle_time_start_am = 'disabled';
			checkedam = '';
			checkedpm = 'checked';
		}
		if (obj.time_start_pm == itime_start) {
			toggle_time_start_pm = 'disabled';
			checkedam = 'checked';
			checkedpm = '';
		}

		$('#wrappertest').append(toggle_time(obj));
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
	} else {
		$('#wrapperday').append(leave_cat);
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
	}
}

// 🔹 Generalized date change handler
function setupDateChange(selector, type, no, allowHalfDayMC = false) {
	initDatepicker(selector, no)
	.on('dp.change dp.update', function(e) {
		let dateVal = $(selector).val();

		// Revalidate field
		$('#form').bootstrapValidator('revalidateField',
		type === 'from' ? 'date_time_start' : 'date_time_end'
		);

		// Sync min/max dates
		if (type === 'from') {
			$('#to').datetimepicker('minDate', dateVal);
		} else {
			$('#from').datetimepicker('maxDate', dateVal);
		}

		// Half-day logic
		if ($('#from').val() === $('#to').val()) {
			if ($('.removehalfleave').length === 0) {
				if (no === 1 || allowHalfDayMC) {
					handleHalfDay(dateVal);
				}
			}
		} else {
			$('.removehalfleave').remove();
			$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
			$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
		}
	});
}

let replacementForm = `
	<?php
	$oi = \Auth::user()->belongstostaff->hasmanyleavereplacement()->where('leave_balance', '<>', 0)->get();
	?>
	<div class="form-group row m-2 {{ $errors->has('leave_id') ? 'has-error' : '' }}">
		<label for="nrla" class="col-sm-4 col-form-label">Please Choose Your Replacement Leave : </label>
		<div class="col-sm-8 nrl">
			<p>Total Replacement Leave = {{ $oi->sum('leave_balance') }} days</p>
			<select name="id" id="nrla" class="form-control form-select form-select-sm">
				<option value="">Please select</option>
			@foreach( $oi as $po )
				<option value="{{ $po->id }}" data-nrlbalance="{{ $po->leave_balance }}">On ${moment( '{{ $po->date_start }}', 'YYYY-MM-DD' ).format('ddd Do MMM YYYY')}, your leave balance = {{ $po->leave_balance }} day</option>
			@endforeach
			</select>
		</div>
	</div>
`;

let from = `
	<div class="form-group row m-2 {{ $errors->has('date_time_start') ? 'has-error' : '' }}">
		{{ Form::label('from', 'From : ', ['class' => 'col-sm-4 col-form-label']) }}
		<div class="col-sm-8 datetime" style="position: relative">
			{{ Form::text('date_time_start', @$value, ['class' => 'form-control form-control-sm', 'id' => 'from', 'placeholder' => 'From : ', 'autocomplete' => 'off']) }}
		</div>
	</div>
`;

let to = `
	<div class="form-group row m-2 {{ $errors->has('date_time_end') ? 'has-error' : '' }}">
		{{ Form::label('to', 'To : ', ['class' => 'col-sm-4 col-form-label']) }}
		<div class="col-sm-8 datetime" style="position: relative">
			{{ Form::text('date_time_end', @$value, ['class' => 'form-control form-control-sm', 'id' => 'to', 'placeholder' => 'To : ', 'autocomplete' => 'off']) }}
		</div>
	</div>
`;

let wrapperday = `
	<div class="form-group row m-2 {{ $errors->has('leave_cat') ? 'has-error' : '' }}" id="wrapperday">
		<div class="form-group col-sm-8 offset-sm-4 form-check {{ $errors->has('half_type_id') ? 'has-error' : '' }} removehalfleave"  id="wrappertest">
		</div>
	</div>
`;

let userneedbackup = `
	@if( $userneedbackup == 1 )
	<div class="form-group row m-2 {{ $errors->has('staff_id') ? 'has-error' : '' }}">
		{{ Form::label('backupperson', 'Replacement : ', ['class' => 'col-sm-4 col-form-label']) }}
		<div class="col-sm-8 backup">
			<select name="staff_id" id="backupperson" class="form-control form-select form-select-sm " placeholder="Please choose" autocomplete="off"></select>
		</div>
	</div>
	@endif
`;

let doc = `
	<div class="form-group row m-2 {{ $errors->has('document') ? 'has-error' : '' }}">
		{{ Form::label( 'doc', 'Upload Supporting Document : ', ['class' => 'col-sm-4 col-form-label'] ) }}
		<div class="col-sm-8 supportdoc">
			{{ Form::file( 'document', ['class' => 'form-control form-control-sm form-control-file', 'id' => 'doc', 'placeholder' => 'Supporting Document']) }}
		</div>
	</div>
`;

let suppdoc = `
	<div class="form-group row m-2 {{ $errors->has('documentsupport') ? 'has-error' : '' }}">
		<div class="offset-sm-4 col-sm-8 form-check">
			{{ Form::checkbox('documentsupport', 1, @$value, ['class' => 'form-check-input ', 'id' => 'suppdoc']) }}
			<label for="suppdoc" class="form-check-label p-1 bg-warning text-danger rounded">Please ensure you will submit <strong>Supporting Documents</strong> within <strong>3 Days</strong> after date leave.</label>
		</div>
	</div>
`;

let leave_cat = `
	<label for="leave_cat" class="col-sm-4 col-form-label removehalfleave">Leave Category : </label>
	<div class="col-sm-8 m-0 removehalfleave" id="halfleave">
		<div class="form-check form-check-inline removehalfleave" id="removeleavehalf">
			<input type="radio" name="leave_cat" value="1" id="radio1" class="removehalfleave" checked="checked">
			<label for="radio1" class="form-check-label removehalfleave m-2">Full Day Off</label>
		</div>
		<div class="form-check form-check-inline removehalfleave" id="appendleavehalf">
			<input type="radio" name="leave_cat" value="2" id="radio2" class="removehalfleave" >
			<label for="radio2" class="form-check-label removehalfleave m-2">Half Day Off</label>
		</div>
	</div>
	<div class="form-group col-sm-8 offset-sm-4 {{ $errors->has('half_type_id') ? 'has-error' : '' }} removehalfleave"  id="wrappertest">
	</div>
`;

function toggle_time(obj) {
	return `
	<div class="form-check form-check-inline removetest">
		<input type="radio" name="half_type_id" value="1/${obj.time_start_am}/${obj.time_end_am}" id="am" ${toggle_time_start_am} ${checkedam}>
		<label for="am" class="form-check-label m-2">
			${moment(obj.time_start_am, 'HH:mm:ss').format('h:mm a')} to ${moment(obj.time_end_am, 'HH:mm:ss').format('h:mm a')}
		</label>
	</div>
	<div class="form-check form-check-inline removetest">
		<input type="radio" name="half_type_id" value="2/${obj.time_start_pm}/${obj.time_end_pm}" id="pm" ${toggle_time_start_pm} ${checkedpm}>
		<label for="pm" class="form-check-label m-2">
			${moment(obj.time_start_pm, 'HH:mm:ss').format('h:mm a')} to ${moment(obj.time_end_pm, 'HH:mm:ss').format('h:mm a')}
		</label>
	</div>
	`;
}

function toggle_time_checked(obj){
	return `
		<div class="form-check form-check-inline removetest">
			<input type="radio" name="half_type_id" value="1/${obj.time_start_am}/${obj.time_end_am}" id="am" checked="checked">
			<label for="am" class="form-check-label m-2">${moment(obj.time_start_am, 'HH:mm:ss').format('h:mm a')} to ${moment(obj.time_end_am, 'HH:mm:ss').format('h:mm a')}</label>
		</div>
		<div class="form-check form-check-inline removetest">
			<input type="radio" name="half_type_id" value="2/${obj.time_start_pm}/${obj.time_end_pm}" id="pm">
			<label for="pm" class="form-check-label m-2">${moment(obj.time_start_pm, 'HH:mm:ss').format('h:mm a')} to ${moment(obj.time_end_pm, 'HH:mm:ss').format('h:mm a')}</label>
		</div>
	`;
};

/////////////////////////////////////////////////////////////////////////////////////////
// start here when user start to select the leave type option
$('#leave_id').on('change', function() {
	$selection = $(this).find(':selected');
	// console.log($selection.val());

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// annual leave & UPL
	if ($selection.val() == '1' || $selection.val() == '3') {
		$('#remove').remove();
		if($selection.val() == '3') {
			$('#wrapper').append(
				'<div id="remove">' +
					<!-- UNPAID LEAVE | UPL -->
					from +
					to +
					wrapperday +
					userneedbackup +
					doc +
					suppdoc +
				'</div>'
			);
		} else {
			$('#wrapper').append(
				'<div id="remove">' +
					<!-- ANNUAL LEAVE | AL -->
					from +
					to +
					wrapperday +
					userneedbackup +
				'</div>'
			);
		}

		@if( $userneedbackup == 1 )
		$('#form').bootstrapValidator('addField', $('.backup').find('[name="staff_id"]'));
		@endif
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_start"]'));
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_end"]'));
		if($selection.val() == '3') {
			$('#form').bootstrapValidator('addField', $('.supportdoc').find('[name="document"]'));
			$('#form').bootstrapValidator('addField', $('.suppdoc').find('[name="documentsupport"]'));
		}

		/////////////////////////////////////////////////////////////////////////////////////////
		//enable select 2 for backup
		$('#backupperson').select2({
			placeholder: 'Please Choose',
			width: '100%',
			ajax: {
				url: '{{ route('backupperson') }}',
				// data: { '_token': '{!! csrf_token() !!}' },
				type: 'POST',
				dataType: 'json',
				data: function (params) {
					var query = {
						id: {{ \Auth::user()->belongstostaff->id }},
						_token: '{!! csrf_token() !!}',
						date_from: $('#from').val(),
						date_to: $('#to').val(),
					}
					return query;
				}
			},
			allowClear: true,
			closeOnSelect: true,
		});

		/////////////////////////////////////////////////////////////////////////////////////////
		// start date
		setupDateChange('#from', 'from', 1);
		setupDateChange('#to', 'to', 1);

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable radio
		$(document).on('change', '#appendleavehalf :radio', function () {
			if (this.checked) {

				let obj = getTimeLeave($('#from').val());

				// checking so there is no double
				if( $('.removetest').length == 0 ) {
					$('#wrappertest').append(toggle_time_checked(obj));
					$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
					$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
				}
			}
		});

		$(document).on('change', '#removeleavehalf :radio', function () {
		//$('#removeleavehalf :radio').change(function() {
			if (this.checked) {
				$('.removetest').remove();
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
			}
		});
	}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($selection.val() == '2') {

		$('#remove').remove();
		$('#wrapper').append(
			'<div id="remove">' +
				<!-- mc leave -->
					from +
					to +
					@if($setHalfDayMC == 1)
					wrapperday +
					@endif
					doc +
					suppdoc +
			'</div>'
		);

		@if( $userneedbackup == 1 )
		// $('#form').bootstrapValidator('addField', $('.backup').find('[name="staff_id"]'));
		@endif
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_start"]'));
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_end"]'));
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
		$('#form').bootstrapValidator('addField', $('.supportdoc').find('[name="document"]'));
		$('#form').bootstrapValidator('addField', $('.suppdoc').find('[name="documentsupport"]'));

		/////////////////////////////////////////////////////////////////////////////////////////
		$('#backupperson').select2({
			placeholder: 'Please Choose',
			width: '100%',
			ajax: {
				url: '{{ route('backupperson') }}',
				// data: { '_token': '{!! csrf_token() !!}' },
				type: 'POST',
				dataType: 'json',
				data: function (params) {
					var query = {
						id: {{ \Auth::user()->belongstostaff->id }},
						_token: '{!! csrf_token() !!}',
						date_from: $('#from').val(),
						date_to: $('#to').val(),
					}
					return query;
				}
			},
			allowClear: true,
			closeOnSelect: true,
		});

		// enable datetime for the 1st one
		setupDateChange('#from', 'from', 2, {{ $setHalfDayMC }});
		setupDateChange('#to', 'to', 2, {{ $setHalfDayMC }});

		@if($setHalfDayMC == 1)
		// enable radio
		$(document).on('change', '#appendleavehalf :radio', function () {
			if (this.checked) {

				let obj = getTimeLeave($('#from').val());

				// checking so there is no double
				if( $('.removetest').length == 0 ) {
					$('#wrappertest').append(toggle_time_checked(obj));
					$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
					$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
				}
			}
		});

		$(document).on('change', '#removeleavehalf :radio', function () {
		//$('#removeleavehalf :radio').change(function() {
			if (this.checked) {
				$('.removetest').remove();
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
			}
		});
		@endif
	}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// replacement leave
<?php
$oi = \Auth::user()->belongstostaff->hasmanyleavereplacement()->where('leave_balance', '<>', 0)->get();
?>
	if ($selection.val() == '4') {
		$('#remove').remove();
		$('#wrapper').append(
			'<div id="remove">' +
				replacementForm +
				from +
				to +
				wrapperday +
				userneedbackup +
			'</div>'
		);

		/////////////////////////////////////////////////////////////////////////////////////////
		// more option
		$('#form').bootstrapValidator('addField', $('.nrl').find('[name="leave_id"]'));
		@if( $userneedbackup == 1 )
		$('#form').bootstrapValidator('addField', $('.backup').find('[name="staff_id"]'));
		@endif
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_start"]'));
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_end"]'));
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));


		/////////////////////////////////////////////////////////////////////////////////////////
		// enable select2 on nrla
		$('#nrla').select2({ placeholder: 'Please select', 	width: '100%',
		});

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable select2
		$('#backupperson').select2({
			placeholder: 'Please Choose',
			width: '100%',
			ajax: {
				url: '{{ route('backupperson') }}',
				// data: { '_token': '{!! csrf_token() !!}' },
				type: 'POST',
				dataType: 'json',
				data: function (params) {
					var query = {
						id: {{ \Auth::user()->belongstostaff->id }},
						_token: '{!! csrf_token() !!}',
						date_from: $('#from').val(),
						date_to: $('#to').val(),
					}
					return query;
				}
			},
			allowClear: true,
			closeOnSelect: true,
		});

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable datetime for the 1st one
		setupDateChange('#from', 'from', 1);
		setupDateChange('#to', 'to', 1);

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable radio
		$(document).on('change', '#appendleavehalf :radio', function () {
			if (this.checked) {

				let obj = getTimeLeave($('#from').val());

				// checking so there is no double
				if( $('.removetest').length == 0 ) {
					$('#wrappertest').append(toggle_time_checked(obj));
				}
			}
		});

		$(document).on('change', '#removeleavehalf :radio', function () {
		// $('#removeleavehalf :radio').change(function() {
			if (this.checked) {
				$('.removetest').remove();
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
			}
		});
	}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// maternity leave
	if ($selection.val() == '7') {

		$('#remove').remove();
		$('#wrapper').append(
			'<div id="remove">' +
			<!-- maternity leave -->
			from +
			to +
			'</div>'
		);


		/////////////////////////////////////////////////////////////////////////////////////////
		// more option
		//add bootstrapvalidator
		// more option
		$('#form').bootstrapValidator('addField', $('.nrl').find('[name="leave_id"]'));
		@if( $userneedbackup == 1 )
		// $('#form').bootstrapValidator('addField', $('.backup').find('[name="staff_id"]'));
		@endif
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_start"]'));
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_end"]'));
		$('#form').bootstrapValidator('addField', $('.supportdoc').find('[name="document"]'));
		$('#form').bootstrapValidator('addField', $('.suppdoc').find('[name="documentsupport"]'));

		/////////////////////////////////////////////////////////////////////////////////////////
		//enable select 2 for backup
		$('#backupperson').select2({
			placeholder: 'Please Choose',
			width: '100%',
			ajax: {
				url: '{{ route('backupperson') }}',
				// data: { '_token': '{!! csrf_token() !!}' },
				type: 'POST',
				dataType: 'json',
				data: function (params) {
					var query = {
						id: {{ \Auth::user()->belongstostaff->id }},
						_token: '{!! csrf_token() !!}',
						date_from: $('#from').val(),
						date_to: $('#to').val(),
					}
					return query;
				}
			},
			allowClear: true,
			closeOnSelect: true,
		});

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable datetime for the 1st one
		initDatepicker('#from', 1)
		.on('dp.change dp.update', function(e) {
			$('#form').bootstrapValidator('revalidateField', 'date_time_start');
			var minDate = $('#from').val();
			$('#to').datetimepicker('minDate', moment( minDate, 'YYYY-MM-DD').add(59, 'days').format('YYYY-MM-DD') );
			$('#to').val( moment( minDate, 'YYYY-MM-DD').add(59, 'days').format('YYYY-MM-DD') );
		});

		initDatepicker('#to', 1)
		.on('dp.change dp.update', function(e) {
			$('#form').bootstrapValidator('revalidateField', 'date_time_end');
			var maxDate = $('#to').val();

			// $('#from').datetimepicker('maxDate', moment( maxDate, 'YYYY-MM-DD').subtract(59, 'days').format('YYYY-MM-DD'));
			// $('#from').val( moment( maxDate, 'YYYY-MM-DD').subtract(59, 'days').format('YYYY-MM-DD') );
		});
	}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($selection.val() == '5' || $selection.val() == '6') {		// el-al and el-upl

		$('#remove').remove();
		$('#wrapper').append(
			'<div id="remove">' +
				<!-- emergency leave -->
				from +
				to +
				wrapperday +
				@if( $userneedbackup == 1 )
				'<div id="backupwrapper">' +
				'</div>' +
				@endif
				doc +
				suppdoc +
			'</div>'
		);
		/////////////////////////////////////////////////////////////////////////////////////////
		//add bootstrapvalidator
		// more option
		$('#form').bootstrapValidator('addField', $('.nrl').find('[name="leave_id"]'));
		@if( $userneedbackup == 1 )
			$('#form').bootstrapValidator('addField', $('.backup').find('[name="staff_id"]'));
		@endif
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_start"]'));
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_end"]'));
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
		$('#form').bootstrapValidator('addField', $('.supportdoc').find('[name="document"]'));
		$('#form').bootstrapValidator('addField', $('.suppdoc').find('[name="documentsupport"]'));

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable datetime for the 1st one
		initDatepicker('#from', 2)
		.on('dp.change dp.update', function(e) {
			$('#form').bootstrapValidator('revalidateField', 'date_time_start');
			var minDaten = $('#from').val();
			$('#to').datetimepicker('minDate', minDaten);

			if($('#from').val() === $('#to').val()) {
				if( $('.removehalfleave').length === 0) {

					////////////////////////////////////////////////////////////////////////////////////////
					// checking half day leave
					let [d, itime_start, itime_end] = getHalfdayInfo($('#from').val());
					// console.log(d);
					if(d === true) {
						$('#wrapperday').append(leave_cat);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));


						let obj = getTimeLeave($('#from').val());

						var checkedam = 'checked';
						var checkedpm = 'checked';
						if(obj.time_start_am == itime_start) {
							var toggle_time_start_am = 'disabled';
							var checkedam = '';
							var checkedpm = 'checked';
						}

						if(obj.time_start_pm == itime_start) {
							var toggle_time_start_pm = 'disabled';
							var checkedam = 'checked';
							var checkedpm = '';
						}
						$('#wrappertest').append(toggle_time(obj));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

					} else {
						$('#wrapperday').append(leave_cat);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
					}
				}
			}
			if($('#from').val() !== $('#to').val()) {
				$('.removehalfleave').remove();
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
			}

			@if( $userneedbackup == 1 )
			// enable backup if date from is greater or equal than today.
			// cari date now dulu
			if( $('#from').val() >= moment().format('YYYY-MM-DD') ) {
				// console.log( moment().add(1, 'days').format('YYYY-MM-DD') );
				// console.log($( '#rembackup').children().length + ' <= rembackup length' );
				if( $('#backupwrapper').children().length == 0 ) {
					$('#backupwrapper').append(userneedbackup);
					$('#form').bootstrapValidator('addField', $('.backup').find('[name="staff_id"]'));
					$('#backupperson').select2({
						placeholder: 'Please Choose',
						width: '100%',
						ajax: {
							url: '{{ route('backupperson') }}',
							// data: { '_token': '{!! csrf_token() !!}' },
							type: 'POST',
							dataType: 'json',
							data: function (params) {
								var query = {
									id: {{ \Auth::user()->belongstostaff->id }},
									_token: '{!! csrf_token() !!}',
									date_from: $('#from').val(),
									date_to: $('#to').val(),
								}
								return query;
							}
						},
						allowClear: true,
						closeOnSelect: true,
					});
				} else {
					$('#backupremove').remove();
				}
			} else {
				$('#form').bootstrapValidator('removeField', $('.backup').find('[name="staff_id"]'));
				$('#backupwrapper').children().remove();
			}
			@endif
		});
		// end date from

		initDatepicker('#to', 2)
		.on('dp.change dp.update', function(e) {
			$('#form').bootstrapValidator('revalidateField', 'date_time_end');
			var maxDate = $('#to').val();
			$('#from').datetimepicker('maxDate', maxDate);

			if($('#from').val() === $('#to').val()) {
				if( $('.removehalfleave').length === 0) {

					////////////////////////////////////////////////////////////////////////////////////////
					// checking half day leave
					let [d, itime_start, itime_end] = getHalfdayInfo($('#to').val());
					// console.log(d);
					if(d === true) {
						$('#wrapperday').append(leave_cat);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));


						let obj = getTimeLeave($('#from').val());

						var checkedam = 'checked';
						var checkedpm = 'checked';
						if(obj.time_start_am == itime_start) {
							var toggle_time_start_am = 'disabled';
							var checkedam = '';
							var checkedpm = 'checked';
						}

						if(obj.time_start_pm == itime_start) {
							var toggle_time_start_pm = 'disabled';
							var checkedam = 'checked';
							var checkedpm = '';
						}
						$('#wrappertest').append(toggle_time(obj));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

					} else {
						$('#wrapperday').append(leave_cat);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
					}
				}
			}
			if($('#from').val() !== $('#to').val()) {
				$('.removehalfleave').remove();
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
			}

			// check for user backup
			@if( $userneedbackup == 1 )
			// enable backup if date from is greater or equal than today.
			// cari date now dulu
			if( $('#from').val() >= moment().format('YYYY-MM-DD') ) {
				// console.log( moment().add(1, 'days').format('YYYY-MM-DD') );
				// console.log($( '#rembackup').children().length + ' <= rembackup length' );
				if( $('#backupwrapper').children().length == 0 ) {
					$('#backupwrapper').append(userneedbackup);
					$('#form').bootstrapValidator('addField', $('.backup').find('[name="staff_id"]'));
					$('#backupperson').select2({
						placeholder: 'Please Choose',
						width: '100%',
						ajax: {
							url: '{{ route('backupperson') }}',
							// data: { '_token': '{!! csrf_token() !!}' },
							type: 'POST',
							dataType: 'json',
							data: function (params) {
								var query = {
									id: {{ \Auth::user()->belongstostaff->id }},
									_token: '{!! csrf_token() !!}',
									date_from: $('#from').val(),
									date_to: $('#to').val(),
								}
								return query;
							}
						},
						allowClear: true,
						closeOnSelect: true,
					});
				}
				// else {
				// 	$('#backupremove').remove();
				// }
			} else {
				$('#form').bootstrapValidator('removeField', $('.backup').find('[name="staff_id"]'));
				$('#backupwrapper').children().remove();
			}
			@endif
		});
		// end date to

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable radio
		$(document).on('change', '#appendleavehalf :radio', function () {
			if (this.checked) {

				let obj = getTimeLeave($('#from').val());

				// checking so there is no double
				if( $('.removetest').length == 0 ) {
					$('#wrappertest').append(toggle_time_checked(obj));
				}
			}
		});

		$(document).on('change', '#removeleavehalf :radio', function () {
		//$('#removeleavehalf :radio').change(function() {
			if (this.checked) {
				$('.removetest').remove();
			}
		});
	}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($selection.val() == '9') { // time off

		$('#remove').remove();
		$('#wrapper').append(
			'<div id="remove">' +
				<!-- time off -->
				from +

				'<div class="form-group row m-2 {{ $errors->has('date_time_end') ? 'has-error' : '' }}">' +
					'{{ Form::label('to', 'Time : ', ['class' => 'col-sm-4 col-form-label']) }}' +
					'<div class="col-sm-8">' +
							'<div class="form-row time">' +
								'<div class="col-sm-8 m-2" style="position: relative">' +
									'{{ Form::text('time_start', @$value, ['class' => 'form-control form-control-sm', 'id' => 'start', 'placeholder' => 'From', 'autocomplete' => 'off']) }}' +
								'</div>' +
								'<div class="col-sm-8 m-2" style="position: relative">' +
									'{{ Form::text('time_end', @$value, ['class' => 'form-control form-control-sm', 'id' => 'end', 'placeholder' => 'To', 'autocomplete' => 'off']) }}' +
								'</div>' +
							'</div>' +
					'</div>' +
				'</div>' +
				userneedbackup +
				doc +
				suppdoc +
			'</div>'
		);
		/////////////////////////////////////////////////////////////////////////////////////////
		// more option
		//add bootstrapvalidator
		@if( $userneedbackup == 1 )
		$('#form').bootstrapValidator('addField', $('.backup').find('[name="staff_id"]'));
		@endif
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_start"]'));
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
		$('#form').bootstrapValidator('addField', $('.supportdoc').find('[name="document"]'));
		$('#form').bootstrapValidator('addField', $('.suppdoc').find('[name="documentsupport"]'));

		/////////////////////////////////////////////////////////////////////////////////////////
		//enable select 2 for backup
		$('#backupperson').select2({
			placeholder: 'Please Choose',
			width: '100%',
			ajax: {
				url: '{{ route('backupperson') }}',
				// data: { '_token': '{!! csrf_token() !!}' },
				type: 'POST',
				dataType: 'json',
				data: function (params) {
					var query = {
						id: {{ \Auth::user()->belongstostaff->id }},
						_token: '{!! csrf_token() !!}',
						date_from: $('#from').val(),
						date_to: $('#from').val(),
					}
					return query;
				}
			},
			allowClear: true,
			closeOnSelect: true,
		});

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable datetime for the 1st one
		initDatepicker('#from', 2)
		.on('dp.change ', function(e) {
			$('#form').bootstrapValidator('revalidateField', 'date_time_start');

			@if( $userneedbackup == 1 )
			// enable backup if date from is greater or equal than today.
			//cari date now dulu
			if( $('#from').val() >= moment().format('YYYY-MM-DD') ) {
				// console.log( moment().add(1, 'days').format('YYYY-MM-DD') );
				// console.log($( '#rembackup').children().length + ' <= rembackup length' );
				if( $('#backupwrapper').children().length == 0 ) {
					$('#backupwrapper').append(userneedbackup);
					$('#form').bootstrapValidator('addField', $('.backup').find('[name="staff_id"]'));
					$('#backupperson').select2({
						placeholder: 'Please Choose',
						width: '100%',
						ajax: {
							url: '{{ route('backupperson') }}',
							// data: { '_token': '{!! csrf_token() !!}' },
							type: 'POST',
							dataType: 'json',
							data: function (params) {
								var query = {
									id: {{ \Auth::user()->belongstostaff->id }},
									_token: '{!! csrf_token() !!}',
									date_from: $('#from').val(),
									date_to: $('#to').val(),
								}
								return query;
							}
						},
						allowClear: true,
						closeOnSelect: true,
					});
				}
			} else {
				$('#form').bootstrapValidator('removeField', $('.backup').find('[name="staff_id"]'));
				$('#backupwrapper').children().remove();
			}
			@endif
		});

		/////////////////////////////////////////////////////////////////////////////////////////
		// time start
		// need to get working hour for each user
		// lazy to implement this 1. :P
		// moment(obj.time_start_am, 'HH:mm:ss').format('h:mm a')
		// moment(obj.time_end_am, 'HH:mm:ss').format('h:mm a')
		// moment(obj.time_start_pm, 'HH:mm:ss').format('h:mm a')
		// moment(obj.time_end_pm, 'HH:mm:ss').format('h:mm a')

		$('#start').datetimepicker({
			icons: datetimeIcons,
			format: 'h:mm A',
			// enabledHours: [8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18],
		})
		.on('dp.change dp.update', function(e){
			$('#form').bootstrapValidator('revalidateField', 'time_start');
			// $('#end').datetimepicker('minDate', moment($('#start').val(), 'h:mm A'));
		});

		$('#end').datetimepicker({
			icons: datetimeIcons,
			format: 'h:mm A',
			// enabledHours: [8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18],
		})
		.on('dp.change dp.update', function(e){
			$('#form').bootstrapValidator('revalidateField', 'time_end');
			// $('#start').datetimepicker('minDate', moment($('#end').val(), 'h:mm A'));
		});
	}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($selection.val() == '11') {				// mc-upl

		$('#remove').remove();
		$('#wrapper').append(
			'<div id="remove">' +
				<!-- mc leave -->
				from +
				to +

				@if($setHalfDayMC == 1)
				wrapperday +
				@endif

				doc +
				suppdoc +
			'</div>'
		);

		//add bootstrapvalidator
		@if( $userneedbackup == 1 )
		$('#form').bootstrapValidator('addField', $('.backup').find('[name="staff_id"]'));
		@endif
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_start"]'));
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_end"]'));
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
		$('#form').bootstrapValidator('addField', $('.supportdoc').find('[name="document"]'));
		$('#form').bootstrapValidator('addField', $('.suppdoc').find('[name="documentsupport"]'));

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable datetime for the 1st one
		setupDateChange('#from', 'from', 2, {{ $setHalfDayMC }});
		setupDateChange('#to', 'to', 2, {{ $setHalfDayMC }});

		/////////////////////////////////////////////////////////////////////////////////////////
		@if($setHalfDayMC == 1)
		// enable radio
		$(document).on('change', '#appendleavehalf :radio', function () {
			if (this.checked) {

				let obj = getTimeLeave($('#from').val());

				// checking so there is no double
				if( $('.removetest').length == 0 ) {
					$('#wrappertest').append(toggle_time_checked(obj));
				}
			}
		});

		$(document).on('change', '#removeleavehalf :radio', function () {
		//$('#removeleavehalf :radio').change(function() {
			if (this.checked) {
				$('.removetest').remove();
			}
		});
		@endif
	}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// el replacement leave
<?php
$oi = \Auth::user()->belongstostaff->hasmanyleavereplacement()->where('leave_balance', '<>', 0)->get();
?>
	if ($selection.val() == '10') {

		$('#remove').remove();
		$('#wrapper').append(
			'<div id="remove">' +
				replacementForm +
				from +
				to +
				wrapperday +
				doc +
				suppdoc +
			'</div>'
		);

		/////////////////////////////////////////////////////////////////////////////////////////
		// more option
		$('#form').bootstrapValidator('addField', $('.nrl').find('[name="leave_id"]'));
		@if( $userneedbackup == 1 )
		// $('#form').bootstrapValidator('addField', $('.backup').find('[name="staff_id"]'));
		@endif
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_start"]'));
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_end"]'));
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
		$('#form').bootstrapValidator('addField', $('.supportdoc').find('[name="document"]'));
		$('#form').bootstrapValidator('addField', $('.suppdoc').find('[name="documentsupport"]'));

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable select2
		$('#nrla').select2({
			placeholder: 'Please select',
			width: '100%',
		});

		/////////////////////////////////////////////////////////////////////////////////////////
		//enable select 2 for backup
		$('#backupperson').select2({
			placeholder: 'Please Choose',
			width: '100%',
			ajax: {
				url: '{{ route('backupperson') }}',
				// data: { '_token': '{!! csrf_token() !!}' },
				type: 'POST',
				dataType: 'json',
				data: function (params) {
					var query = {
						id: {{ \Auth::user()->belongstostaff->id }},
						_token: '{!! csrf_token() !!}',
						date_from: $('#from').val(),
						date_to: $('#to').val(),
					}
					return query;
				}
			},
			allowClear: true,
			closeOnSelect: true,
		});

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable datetime for the 1st one
		setupDateChange('#from', 'from', 2, {{ $setHalfDayMC }});
		setupDateChange('#to', 'to', 2, {{ $setHalfDayMC }});

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable radio
		$(document).on('change', '#appendleavehalf :radio', function () {
			if (this.checked) {

				let obj = getTimeLeave($('#from').val());

				// checking so there is no double
				if( $('.removetest').length == 0 ) {
					$('#wrappertest').append(toggle_time_checked(obj));
				}
			}
		});

		$(document).on('change', '#removeleavehalf :radio', function () {
		// $('#removeleavehalf :radio').change(function() {
			if (this.checked) {
				$('.removetest').remove();
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
			}
		});
	}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// S-UPL
	if ($selection.val() == '12') {

		$('#remove').remove();
		$('#wrapper').append(

			'<div id="remove">' +
				<!-- annual leave -->

				from +
				to +
				wrapperday +
				doc +
				suppdoc +
			'</div>'
			);
		/////////////////////////////////////////////////////////////////////////////////////////
		// add more option
		//add bootstrapvalidator
		@if( $userneedbackup == 1 )
		$('#form').bootstrapValidator('addField', $('.backup').find('[name="staff_id"]'));
		@endif
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_start"]'));
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_end"]'));
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
		$('#form').bootstrapValidator('addField', $('.supportdoc').find('[name="document"]'));
		$('#form').bootstrapValidator('addField', $('.suppdoc').find('[name="documentsupport"]'));

		/////////////////////////////////////////////////////////////////////////////////////////
		//enable select 2 for backup
		$('#backupperson').select2({
			placeholder: 'Please Choose',
			width: '100%',
			allowClear: true,
			closeOnSelect: true,
			ajax: {
				url: '{{ route('backupperson') }}',
				// data: { '_token': '{!! csrf_token() !!}' },
				type: 'POST',
				dataType: 'json',
				data: function (params) {
					var query = {
						id: {{ \Auth::user()->belongstostaff->id }},
						_token: '{!! csrf_token() !!}',
						date_from: $('#from').val(),
						date_to: $('#to').val(),
					}
					return query;
				}
			},
		});

		/////////////////////////////////////////////////////////////////////////////////////////
		// start date
		setupDateChange('#from', 'from', 2, {{ $setHalfDayMC }});
		setupDateChange('#to', 'to', 2, {{ $setHalfDayMC }});

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable radio
		$(document).on('change', '#appendleavehalf :radio', function () {
			if (this.checked) {
				let obj = getTimeLeave($('#from').val());
				// checking so there is no double
				if( $('.removetest').length == 0 ) {
					$('#wrappertest').append(toggle_time_checked(obj));
				}
			}
		});
		$(document).on('change', '#removeleavehalf :radio', function () {
		//$('#removeleavehalf :radio').change(function() {
			if (this.checked) {
				$('.removetest').remove();
			}
		});
	}
});

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// validator
$(document).ready(function() {
	$('#form').bootstrapValidator({
		feedbackIcons: {
			valid: '',
			invalid: '',
			validating: ''
		},
		fields: {
			leave_type_id: {
				validators: {
					notEmpty: {
						message: 'Please choose'
					},
				}
			},
			reason: {
				validators: {
					notEmpty: {
						message: 'Please insert your reason'
					},
					callback: {
						message: 'The reason must be less than 200 characters long',
						callback: function(value, validator, $field) {
							var div  = $('<div/>').html(value).get(0),
							text = div.textContent || div.innerText;
							return text.length <= 200;
						},
					},
				}
			},
			akuan: {
				validators: {
					notEmpty: {
						message: 'Please click this as an acknowledgement'
					}
				}
			},
			date_time_start: {
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
			date_time_end: {
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
			time_start: {
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
			time_end: {
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
			leave_id: {
				validators: {
					notEmpty: {
						message: 'Please select 1 option',
					},
				}
			},
			staff_id: {
				validators: {
					notEmpty: {
						message: 'Please choose'
					}
				}
			},
			document: {
				validators: {
					file: {
						extension: 'jpeg,jpg,png,bmp,pdf,doc,docx',											// no space
						type: 'image/jpeg,image/png,image/bmp,application/pdf,application/msword',			// no space
						maxSize: 5242880,	// 5120 * 1024,
						message: 'The selected file is not valid. Please use jpeg, jpg, png, bmp, pdf or doc and the file is below than 5MB. '
					},
				}
			},
			//container: '.suppdoc',
			documentsupport: {
				validators: {
					notEmpty: {
						message: 'Please click this as an aknowledgement.'
					},
				}
			},
		}
	})
	.find('[name="reason"]')
	// .ckeditor()
	// .editor
		.on('change', function() {
			// Revalidate the bio field
		$('#form').bootstrapValidator('revalidateField', 'reason');
		// console.log($('#reason').val());
	})
	;
});

/////////////////////////////////////////////////////////////////////////////////////////
@endsection

