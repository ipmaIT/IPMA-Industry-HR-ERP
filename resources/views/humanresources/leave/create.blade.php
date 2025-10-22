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
		<form method="POST" action="{{ route('leave.store') }}" accept-charset="UTF-8" id="form" autocomplete="off" data-toggle="validator" enctype="multipart/form-data">
			@csrf
		<h5 class="text-center">Leave Application</h5>

		<div class="form-group row m-2 {{ $errors->has('leave_type_id') ? 'has-error' : '' }}">
			<label for="leave_id" class="col-sm-4 col-form-label">Leave Type : </label>
			<div class="col-sm-8">
				<select name="leave_type_id" id="leave_id" class="form-control form-control-sm"></select>
			</div>
		</div>

		<div class="form-group row m-2 {{ $errors->has('reason') ? 'has-error' : '' }}">
			<label for="reason" class="col-sm-4 col-form-label">Reason : </label>
			<div class="col-sm-8">
				<textarea name="reason" id="reason" class="form-control form-control-sm" placeholder="Reason">{{ old('reason') }}</textarea>
			</div>
		</div>

		<div id="wrapper">
		</div>

		<div class="form-group row m-2 {{ $errors->has('akuan') ? 'has-error' : '' }}">
			<div class="col-sm-8 offset-sm-4 form-check">
				<input type="checkbox" name="akuan" value="1" id="akuan1" class="form-check-input">
				<label for="akuan1" class="form-check-label p-1 bg-warning text-danger rounded"><p>I hereby confirmed that all details and information filled in are <strong>CORRECT</strong> and <strong>CHECKED</strong> before sending.</p></label>
			</div>
		</div>

		<div class="form-group row mb-3">
			<div class="col-sm-8 offset-sm-4">
				<button type="submit" class="btn btn-sm btn-primary">Submit Application</button>
			</div>
		</div>
		</form>
	</div>
</div>

@endsection
@section('js')

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

<?php
$user = \Auth::user()->belongstostaff;
$userneedbackup = $user->belongstoleaveapprovalflow?->backup_approval;
$setHalfDayMC = \App\Models\Setting::find(2)->active;
?>

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

function setupDateChange(selector, type, no, allowHalfDayMC = false) {
	initDatepicker(selector, no)
	.on('dp.change dp.update', function(e) {
		let dateVal = $(selector).val();

		$('#form').bootstrapValidator('revalidateField',
		type === 'from' ? 'date_time_start' : 'date_time_end'
		);

		if (type === 'from') {
			$('#to').datetimepicker('minDate', dateVal);
		} else {
			$('#from').datetimepicker('maxDate', dateVal);
		}

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
	<div class="form-group row m-2 {{ $errors->has('nrla') ? 'has-error' : '' }}">
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
		<label for="from" class="col-sm-4 col-form-label">From : </label>
		<div class="col-sm-8 datetime" style="position: relative">
			<input type="text" name="date_time_start" value="{{ old('date_time_start') }}" id="from" class="form-control form-control-sm" placeholder="From">
		</div>
	</div>
`;

let to = `
	<div class="form-group row m-2 {{ $errors->has('date_time_end') ? 'has-error' : '' }}">
		<label for="to" class="col-sm-4 col-form-label">To : </label>
		<div class="col-sm-8 datetime" style="position: relative">
			<input type="text" name="date_time_end" value="{{ old('date_time_end') }}" id="to" class="form-control form-control-sm" placeholder="To">
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
		<label for="backupperson" class="col-sm-4 col-form-label">Replacement : </label>
		<div class="col-sm-8 backup">
			<select name="staff_id" id="backupperson" class="form-control form-select form-select-sm " placeholder="Please choose" autocomplete="off"></select>
		</div>
	</div>
	@endif
`;

let timeOffHtml =
'<div class="form-group row m-2 {{ $errors->has('date_time_end') ? 'has-error' : '' }}">' +
	'<label for="to" class="col-sm-4 col-form-label">Time : </label>' +
	'<div class="col-sm-8">' +
		'<div class="form-row time">' +
			'<div class="col-sm-8 m-2" style="position: relative">' +
				'<input type="text" name="time_start" value="{{ old('time_start') }}" id="start" class="form-control form-control-sm" placeholder="Time Start">' +
			'</div>' +
			'<div class="col-sm-8 m-2" style="position: relative">' +
				'<input type="text" name="time_end" value="{{ old('time_end') }}" id="end" class="form-control form-control-sm" placeholder="Time End">' +
			'</div>' +
		'</div>' +
	'</div>' +
'</div>';

let doc = `
	<div class="form-group row m-2 {{ $errors->has('document') ? 'has-error' : '' }}">
		<label for="doc" class="col-sm-4 col-form-label">Upload Supporting Document : </label>
		<div class="col-sm-8 supportdoc">
			<input type="file" name="document" id="doc" class="form-control form-control-sm form-control-file" placeholder="Supporting Document">
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

function appendWrapper(html) {
	$('#remove').remove();
	$('#wrapper').append('<div id="remove">' + html + '</div>');
}

function addValidatorFields(selectors) {
	selectors.forEach(sel => {
		const $el = $(sel);
		if ($el.length) $('#form').bootstrapValidator('addField', $el);
	});
}

function removeValidatorFields(selectors) {
	selectors.forEach(sel => {
		const $el = $(sel);
		if ($el.length) $('#form').bootstrapValidator('removeField', $el);
	});
}

function initBackupPerson(selector = '#backupperson', df = '#from', dt = '#to') {
	$(selector).select2({
		placeholder: 'Please Choose',
		width: '100%',
		allowClear: true,
		closeOnSelect: true,
		ajax: {
			url: '{{ route('backupperson') }}',
			type: 'POST',
			dataType: 'json',
			data: function () {
				return {
					id: {{ \Auth::user()->belongstostaff->id }},
					_token: '{!! csrf_token() !!}',
					date_from: $(df).val(),
					date_to: $(dt).val()
				};
			}
		}
	});
}

function initNRLA() {
	$('#nrla').select2({ placeholder: 'Please select', width: '100%' });
}

function initTimePickersForTimeOff() {
	// start/end time pickers used by type 9 (time off)
	$('#start').datetimepicker({
		icons: datetimeIcons,
		format: 'h:mm A'
	}).on('dp.change dp.update', function () {
		$('#form').bootstrapValidator('revalidateField', 'time_start');
	});
	$('#end').datetimepicker({
		icons: datetimeIcons,
		format: 'h:mm A'
	}).on('dp.change dp.update', function () {
		$('#form').bootstrapValidator('revalidateField', 'time_end');
	});
}

// Attach delegated half-day handlers once (safe to call multiple times)
function initHalfDayListeners() {
	// Append (when user selects to append half day)
	$(document).off('change', '#appendleavehalf :radio').on('change', '#appendleavehalf :radio', async function () {
		if (!this.checked) return;

		const date = $('#from').val();
		if (!date) return;

		try {
			const obj = await getTimeLeave(date);
			if (!obj) {
				showWarning('Unable to load half-day times');
				return;
			}
			if ($('.removetest').length === 0) {
				$('#wrappertest').append(toggle_time_checked(obj));
				$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
			}
		} catch (err) {
			console.error('getTimeLeave error', err);
			showWarning('Network error while loading half-day times.');
		}
	});

	// Remove (when user chooses remove-half)
	$(document).off('change', '#removeleavehalf :radio').on('change', '#removeleavehalf :radio', function () {
		if (!this.checked) return;
		$('.removetest').remove();
		$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
		$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
	});
}

// Utility to handle the "EL-AL / EL-UPL" date-change logic (from)
function handleELFromChange() {
	$('#form').bootstrapValidator('revalidateField', 'date_time_start');
	const minDaten = $('#from').val();
	$('#to').datetimepicker('minDate', minDaten);

	if ($('#from').val() === $('#to').val()) {
		if ($('.removehalfleave').length === 0) {
			let [d, itime_start, itime_end] = getHalfdayInfo($('#from').val());
			if (d === true) {
				$('#wrapperday').append(leave_cat);
				$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

				let obj = getTimeLeave($('#from').val());

				// compute checked/disabled states then append
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
	if ($('#from').val() !== $('#to').val()) {
		$('.removehalfleave').remove();
		$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
		$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
	}

	@if( $userneedbackup == 1 )
	if ($('#from').val() >= moment().format('YYYY-MM-DD')) {
		if ($('#backupwrapper').children().length === 0) {
			$('#backupwrapper').append(userneedbackup);
			$('#form').bootstrapValidator('addField', $('.backup').find('[name="staff_id"]'));
			initBackupPerson();
		} else {
			// keep existing
		}
	} else {
		$('#form').bootstrapValidator('removeField', $('.backup').find('[name="staff_id"]'));
		$('#backupwrapper').children().remove();
	}
	@endif
}

// Utility to handle the "EL-AL / EL-UPL" date-change logic (to)
function handleELToChange() {
	$('#form').bootstrapValidator('revalidateField', 'date_time_end');
	const maxDate = $('#to').val();
	$('#from').datetimepicker('maxDate', maxDate);

	if ($('#from').val() === $('#to').val()) {
		if ($('.removehalfleave').length === 0) {
			let [d, itime_start, itime_end] = getHalfdayInfo($('#to').val());
			if (d === true) {
				$('#wrapperday').append(leave_cat);
				$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

				let obj = getTimeLeave($('#from').val());
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
	if ($('#from').val() !== $('#to').val()) {
		$('.removehalfleave').remove();
		$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
		$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
	}

	@if( $userneedbackup == 1 )
	if ($('#from').val() >= moment().format('YYYY-MM-DD')) {
		if ($('#backupwrapper').children().length === 0) {
			$('#backupwrapper').append(userneedbackup);
			$('#form').bootstrapValidator('addField', $('.backup').find('[name="staff_id"]'));
			initBackupPerson();
		}
	} else {
		$('#form').bootstrapValidator('removeField', $('.backup').find('[name="staff_id"]'));
		$('#backupwrapper').children().remove();
	}
	@endif
}

// -----------------------
// Main optimized if-blocks
// -----------------------
$('#leave_id').on('change', function () {
	let $selection = $(this).find(':selected');
	const val = $selection.val();

	// ---- TYPE 1 & 3 (Full day / MC w/ support) ----
	if (val == '1' || val == '3') {
		if (val == '3') {
			appendWrapper(from + to + wrapperday + userneedbackup + doc + suppdoc);
		} else {
			appendWrapper(from + to + wrapperday + userneedbackup);
		}

		// validator fields
		@if( $userneedbackup == 1 )
		addValidatorFields(['.backup [name="staff_id"]']);
		@endif
		addValidatorFields([
		'.datetime [name="date_time_start"]',
		'.datetime [name="date_time_end"]'
		]);
		if (val == '3') {
			addValidatorFields(['.supportdoc [name="document"]', '.suppdoc [name="documentsupport"]']);
		}

		// init selects & date change
		initBackupPerson();
		setupDateChange('#from', 'from', 1);
		setupDateChange('#to', 'to', 1);

		// half-day handlers (delegated)
		initHalfDayListeners();
	}

	// ---- TYPE 2 (MC single-day or half-day MC) ----
	if (val == '2') {
		appendWrapper(
		from +
		to +
		@if($setHalfDayMC == 1) wrapperday + @endif
		doc + suppdoc
		);

		addValidatorFields([
		'.datetime [name="date_time_start"]',
		'.datetime [name="date_time_end"]',
		'.time [name="time_start"]',
		'.time [name="time_end"]',
		'.supportdoc [name="document"]',
		'.suppdoc [name="documentsupport"]'
		]);

		initBackupPerson();
		setupDateChange('#from', 'from', 2, {{ $setHalfDayMC }});
		setupDateChange('#to', 'to', 2, {{ $setHalfDayMC }});

		@if($setHalfDayMC == 1)
		initHalfDayListeners();
		@endif
	}

	// ---- TYPE 4 (Replacement) ----
	if (val == '4') {
		appendWrapper(replacementForm + from + to + wrapperday + userneedbackup);

		addValidatorFields([
		'.nrl [name="leave_id"]',
		'.datetime [name="date_time_start"]',
		'.datetime [name="date_time_end"]',
		'.time [name="time_start"]',
		'.time [name="time_end"]'
		]);

		@if( $userneedbackup == 1 )
		addValidatorFields(['.backup [name="staff_id"]']);
		@endif

		initNRLA();
		initBackupPerson();
		setupDateChange('#from', 'from', 1);
		setupDateChange('#to', 'to', 1);

		initHalfDayListeners();
	}

	// ---- TYPE 7 ----
	if (val == '7') {
		appendWrapper(from + to);

		addValidatorFields([
		'.nrl [name="leave_id"]',
		'.datetime [name="date_time_start"]',
		'.datetime [name="date_time_end"]',
		'.supportdoc [name="document"]',
		'.suppdoc [name="documentsupport"]'
		]);

		initBackupPerson();

		// custom date constraints (59 days)
		initDatepicker('#from', 1).on('dp.change dp.update', function () {
			$('#form').bootstrapValidator('revalidateField', 'date_time_start');
			var minDate = $('#from').val();
			$('#to').datetimepicker('minDate', moment(minDate, 'YYYY-MM-DD').add(59, 'days').format('YYYY-MM-DD'));
			$('#to').val(moment(minDate, 'YYYY-MM-DD').add(59, 'days').format('YYYY-MM-DD'));
		});
		initDatepicker('#to', 1).on('dp.change dp.update', function () {
			$('#form').bootstrapValidator('revalidateField', 'date_time_end');
		});
	}

	// ---- TYPE 5 & 6 (EL-AL and EL-UPL) ----
	if (val == '5' || val == '6') {
		appendWrapper(
		from +
		to +
		wrapperday +
		@if( $userneedbackup == 1 ) '<div id="backupwrapper"></div>' + @endif
		doc + suppdoc
		);

		addValidatorFields([
		'.nrl [name="leave_id"]',
		'.datetime [name="date_time_start"]',
		'.datetime [name="date_time_end"]',
		'.time [name="time_start"]',
		'.time [name="time_end"]',
		'.supportdoc [name="document"]',
		'.suppdoc [name="documentsupport"]'
		]);

		@if( $userneedbackup == 1 )
		addValidatorFields(['.backup [name="staff_id"]']);
		@endif

		// datepicker with EL-specific handlers
		initDatepicker('#from', 2).on('dp.change dp.update', handleELFromChange);
		initDatepicker('#to', 2).on('dp.change dp.update', handleELToChange);

		initHalfDayListeners();
	}

	// ---- TYPE 9 (Time off) ----
	if (val == '9') {
		// note: timeOffForm should be a string variable that contains the Time fields HTML or use inline as below
		appendWrapper(from + timeOffHtml + userneedbackup + doc + suppdoc);

		// now init select2 for the newly inserted element:
		initBackupPerson('#backupperson', '#from', '#from');

		@if( $userneedbackup == 1 )
		addValidatorFields(['.backup [name="staff_id"]']);
		@endif

		addValidatorFields([
		'.datetime [name="date_time_start"]',
		'.time [name="time_start"]',
		'.time [name="time_end"]',
		'.supportdoc [name="document"]',
		'.suppdoc [name="documentsupport"]'
		]);

		// initBackupPerson();
		initDatepicker('#from', 2).on('dp.change', function () {
			$('#form').bootstrapValidator('revalidateField', 'date_time_start');

			@if( $userneedbackup == 1 )
			if ($('#from').val() >= moment().format('YYYY-MM-DD')) {
				if ($('#backupwrapper').children().length == 0) {
					$('#backupwrapper').append(userneedbackup);
					addValidatorFields(['.backup [name="staff_id"]']);
					initBackupPerson('#backupperson', '#from', '#from');
				}
			} else {
				$('#form').bootstrapValidator('removeField', $('.backup').find('[name="staff_id"]'));
				$('#backupwrapper').children().remove();
			}
			@endif
		});

		initTimePickersForTimeOff();
	}

	// ---- TYPE 11 (MC-UPL) ----
	if (val == '11') {
		appendWrapper(from + to + @if($setHalfDayMC == 1) wrapperday + @endif doc + suppdoc);

		@if( $userneedbackup == 1 )
		addValidatorFields(['.backup [name="staff_id"]']);
		@endif
		addValidatorFields([
		'.datetime [name="date_time_start"]',
		'.datetime [name="date_time_end"]',
		'.time [name="time_start"]',
		'.time [name="time_end"]',
		'.supportdoc [name="document"]',
		'.suppdoc [name="documentsupport"]'
		]);

		setupDateChange('#from', 'from', 2, {{ $setHalfDayMC }});
		setupDateChange('#to', 'to', 2, {{ $setHalfDayMC }});

		@if($setHalfDayMC == 1)
		initHalfDayListeners();
		@endif
	}

	// ---- TYPE 10 (Replacement leave) ----
	if (val == '10') {
		appendWrapper(replacementForm + from + to + wrapperday + doc + suppdoc);

		addValidatorFields([
		'.nrl [name="leave_id"]',
		'.datetime [name="date_time_start"]',
		'.datetime [name="date_time_end"]',
		'.time [name="time_start"]',
		'.time [name="time_end"]',
		'.supportdoc [name="document"]',
		'.suppdoc [name="documentsupport"]'
		]);

		initNRLA();
		initBackupPerson();
		setupDateChange('#from', 'from', 2, {{ $setHalfDayMC }});
		setupDateChange('#to', 'to', 2, {{ $setHalfDayMC }});

		initHalfDayListeners();
	}

	// ---- TYPE 12 ----
	if (val == '12') {
		appendWrapper(from + to + wrapperday + doc + suppdoc);

		@if( $userneedbackup == 1 )
		addValidatorFields(['.backup [name="staff_id"]']);
		@endif
		addValidatorFields([
		'.datetime [name="date_time_start"]',
		'.datetime [name="date_time_end"]',
		'.time [name="time_start"]',
		'.time [name="time_end"]',
		'.supportdoc [name="document"]',
		'.suppdoc [name="documentsupport"]'
		]);

		initBackupPerson();
		setupDateChange('#from', 'from', 2, {{ $setHalfDayMC }});
		setupDateChange('#to', 'to', 2, {{ $setHalfDayMC }});

		initHalfDayListeners();
	}
}); // end leave_id change



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

