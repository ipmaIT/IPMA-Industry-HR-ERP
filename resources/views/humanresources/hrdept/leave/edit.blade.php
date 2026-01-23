@extends('layouts.app')

@section('content')
<style>
	@media print {
		body {
			visibility: hidden;
		}

		#printPageButton, #back {
			display: none;
		}

		.table-container {
			visibility: visible;
			position: absolute;
			left: 0;
			top: 0;
		}
	}

	.table-container {
		display: table;
		width: 100%;
		border-collapse: collapse;
	}

	.table {
		display: table;
		width: 100%;
		border-collapse: collapse;
		margin-top: 0;
		padding-top: 0;
		margin-bottom: 0;
		padding-bottom: 0;
	}

	.table-row {
		display: table-row;
	}

	.table-cell {
		display: table-cell;
		border: 1px solid #b3b3b3;
		padding: 4px;
		box-sizing: border-box;
	}

	.table-cell-top {
		display: table-cell;
		border: 1px solid #b3b3b3;
		border-top: none;
		padding: 4px;
		box-sizing: border-box;
	}

	.table-cell-top-bottom {
		display: table-cell;
		border: 1px solid #b3b3b3;
		border-top: none;
		border-bottom: none;
		padding: 0px;
		box-sizing: border-box;
	}

	.table-cell-hidden {
		display: table-cell;
		border: none;
	}

	.header {
		font-size: 22px;
		text-align: center;
	}

	.theme {
		background-color: #e6e6e6;
	}

	.table-cell-top1 {
		display: table-cell;
		border: 1px solid #b3b3b3;
		border-top: none;
		padding: 0px;
		box-sizing: border-box;
	}
</style>

<?php
use \App\Models\Staff;
use \Carbon\Carbon;
use \Carbon\CarbonPeriod;

$user = $hrleave->belongstostaff;
$userneedbackup = $user->belongstoleaveapprovalflow->backup_approval;
$setHalfDayMC = \App\Models\Setting::find(2)->active;
// dd($setHalfDayMC);
// checking for overlapped leave only for half day leave
// dd(\App\Helpers\UnavailableDateTime::unblockhalfdayleave($hrleave->belongstostaff->id, '2023-09-08'));
// dd($hrleave);

$staff = $user;
// dd([$staff, $user]);
$login = $staff->hasmanylogin()->where('active', 1)->get()->first();

$count = 0;
$supervisor_no = 0;
$hod_no = 0;
$director_no = 0;
$hr_no = 0;

$backup = $hrleave->hasmanyleaveapprovalbackup?->first();
$supervisor = $hrleave->hasmanyleaveapprovalsupervisor->first();
$hod = $hrleave->hasmanyleaveapprovalhod->first();
$director = $hrleave->hasmanyleaveapprovaldir->first();
$hr = $hrleave->hasmanyleaveapprovalhr->first();

if ($supervisor) {
	$count++;
	$supervisor_no = $count;
}

if ($hod) {
	$count++;
	$hod_no = $count;
}

if ($director) {
	$count++;
	$director_no = $count;
}

if ($hr) {
	$count++;
	$hr_no = $count;
}

if ($count != 0) {
	$width = 100 / $count;
} else {
	$width = 100;
}

if ((\Carbon\Carbon::parse($hrleave->date_time_start)->format('H:i')) == '00:00') {
	$date_start = \Carbon\Carbon::parse($hrleave->date_time_start)->format('d F Y');
} else {
	$date_start = \Carbon\Carbon::parse($hrleave->date_time_start)->format('d F Y h:i a');
}

if ((\Carbon\Carbon::parse($hrleave->date_time_end)->format('H:i')) == '00:00') {
	$date_end = \Carbon\Carbon::parse($hrleave->date_time_end)->format('d F Y');
} else {
	$date_end = \Carbon\Carbon::parse($hrleave->date_time_end)->format('d F Y h:i a');
}

if ($hrleave->period_day !== 0.0 &&$hrleave->period_time == NULL) {
	$total_leave =$hrleave->period_day . ' Days';
} else {
	$total_leave =$hrleave->period_time;
}

if ($backup) {
	$backup_name = $backup->belongstostaff->name;

	if ($backup->created_at == $backup->updated_at) {
		$approved_date = '-';
	} else {
		$approved_date = \Carbon\Carbon::parse($backup->updated_at)->format('d F Y h:i a');
	}
} else {
	$backup_name = '-';
	$approved_date = '-';
}
?>
<div class="container row align-items-start justify-content-center">
	<div class="col-sm-12">
		@include('humanresources.hrdept.navhr')
		<h4>Leave Edit</h4>
		<div class="table-container">
			<div class="table">
				<div class="table-row header">
					<div class="table-cell" style="width: 40%; background-color: #99ff99;">IPMA INDUSTRY SDN.BHD.</div>
					<div class="table-cell" style="width: 60%; background-color: #e6e6e6;">LEAVE APPLICATION FORM</div>
				</div>
			</div>

			<div class="table">
				<div class="table-row">
					<div class="table-cell-top" style="width: 25%;">STAFF ID : {{ @$login->username }}</div>
					<div class="table-cell-top" style="width: 75%;">NAME : {{ @$staff->name }}</div>
				</div>
			</div>

			<div class="table">
				<div class="table-row">
					<div class="table-cell-top" style="width: 25%;">LEAVE NO : HR9-{{ @str_pad($hrleave->leave_no,5,'0',STR_PAD_LEFT) }}/{{ $hrleave->leave_year }}</div>
					<div class="table-cell-top" style="width: 60%;">DATE : {{ @$date_start }} - {{ @$date_end }} </div>
					<div class="table-cell-top" style="width: 25%;">TOTAL : {{ @$total_leave }} </div>
				</div>
			</div>

			<div class="table">
				<div class="table-row">
					<div class="table-cell-top text-wrap" style="width: 45%;">LEAVE TYPE : {{ $hrleave->belongstooptleavetype->leave_type_code }} ({{ $hrleave->belongstooptleavetype->leave_type }})</div>
					<div class="table-cell-top text-wrap" style="width: 55%;">REASON : {{ $hrleave->reason }} </div>
				</div>
			</div>

			<div class="table">
				<div class="table-row">
					<div class="table-cell-top text-wrap" style="width: 60%;">BACKUP : {{ @$backup_name }}</div>
					<div class="table-cell-top" style="width: 40%;">DATE APPROVED : {{ @$approved_date }} </div>
				</div>
			</div>

		<?php
		use \App\Models\HumanResources\HRAttendance;
		use Illuminate\Database\Eloquent\Builder;

		$hrremarksattendance = HRAttendance::where(function (Builder $query) use ($hrleave){
												$query->whereDate('attend_date', '>=', $hrleave->date_time_start)
												->whereDate('attend_date', '<=', $hrleave->date_time_end);
											})
								->where('staff_id', $hrleave->staff_id)
								->where(function (Builder $query) {
									$query->whereNotNull('remarks')->orWhereNotNull('hr_remarks');
								})
								// ->ddrawsql();
								->get();
		?>
		@if($hrremarksattendance)
		<div class="table">
			@foreach($hrremarksattendance as $key => $valueble)
				<div class="table-row">
					<div class="table-cell-top" style="width: 100%;">REMARKS FROM ATTENDANCE : {{ $valueble->remarks }}<br/>HR REMARKS FROM ATTENDANCE : {{ $valueble->hr_remarks }}</div>
				</div>
			@endforeach
		</div>
		@endif





			<div class="table">
				<div class="table-row">
					<div class="table-cell-top text-center" style="width: 100%; background-color: #ffcc99; font-size: 18px;">SIGNATURE / APPROVALS</div>
				</div>
			</div>

			<div class="table">
				<div class="table-row">
					@for ($a = 1; $a <= $count; $a++)
						@if ($supervisor_no==$a)
							<div class="table-cell-top text-center" style="width: {{ $width }}%; background-color: #f2f2f2; font-size: 18px;">SUPERVISOR</div>
						@elseif ($hod_no == $a)
							<div class="table-cell-top text-center" style="width: {{ $width }}%; background-color: #f2f2f2; font-size: 18px;">HOD</div>
						@elseif ($director_no == $a)
							<div class="table-cell-top text-center" style="width: {{ $width }}%; background-color: #f2f2f2; font-size: 18px;">DIRECTOR</div>
						@elseif ($hr_no == $a)
							<div class="table-cell-top text-center" style="width: {{ $width }}%; background-color: #f2f2f2; font-size: 18px;">HR</div>
						@endif
					@endfor
				</div>
			</div>

			<div class="table">
				<div class="table-row" style="height: 50px;">
					@for ($a = 1; $a <= $count; $a++)
						@if ($supervisor_no==$a)
							<div class="table-cell-top-bottom text-center text-decoration-underline text-wrap" style="width: {{ $width }}%; vertical-align: bottom;">{{ @$supervisor->belongstostaff->name }}</div>
						@elseif ($hod_no == $a)
							<div class="table-cell-top-bottom text-center text-decoration-underline text-wrap" style="width: {{ $width }}%; vertical-align: bottom;">{{ @$hod->belongstostaff->name }}</div>
						@elseif ($director_no == $a)
							<div class="table-cell-top-bottom text-center text-decoration-underline text-wrap" style="width: {{ $width }}%; vertical-align: bottom;">{{ @$director->belongstostaff->name }}</div>
						@elseif ($hr_no == $a)
							<div class="table-cell-top-bottom text-center text-decoration-underline text-wrap" style="width: {{ $width }}%; vertical-align: bottom;">{{ @$hr->belongstostaff->name }}</div>
						@endif
					@endfor
				</div>
				<div class="table-row">
					@for ($a = 1; $a <= $count; $a++)
						@if ($supervisor_no==$a)
							<div class="table-cell-top1 text-center">{{ @$supervisor->updated_at }}</div>
						@elseif ($hod_no == $a)
							<div class="table-cell-top1 text-center">{{ @$hod->updated_at }}</div>
						@elseif ($director_no == $a)
							<div class="table-cell-top1 text-center">{{ @$director->updated_at }}</div>
						@elseif ($hr_no == $a)
							<div class="table-cell-top1 text-center">{{ @$hr->updated_at }}</div>
						@endif
					@endfor
				</div>
			</div>
		</div>
	</div>

	<p>&nbsp;</p>

	<div class="col-sm-12 row justify-content-center align-items-start">
		<form method="POST" action="{{ route('hrleave.update', $hrleave->id) }}" accept-charset="UTF-8" id="form" autocomplete="off" class="" enctype="multipart/form-data">
		@csrf
		@method('PATCH')
		<h5>Edit Leave Application</h5>

		<div class="form-group row m-1 @error('leave_id') has-error @enderror">
			<label for="leave_id" class="col-sm-4 col-form-label">Leave Type : </label>
			<div class="col-sm-8">
				<select name="leave_type_id" id="leave_id" class="form-select form-select-sm @error('leave_type_id') is-invalid @enderror">
					<option value="">Please choose</option>
					@foreach(\App\Models\HumanResources\OptLeaveType::pluck('leave_type', 'id') as $k => $v)
					<option value="{{ $k }}" {{ ($hrleave->leave_type_id == $k)?'selected':NULL }}>{{ $v }}</option>
					@endforeach
				</select>
			</div>
		</div>

		<div class="form-group row m-1 @error('reason') has-error @enderror">
			<label for="reason" class="col-sm-4 col-form-label">Reason : </label>
			<div class="col-sm-8">
				<textarea name="reason" id="reason" class="w-100 form-control form-control-sm @error('reason') is-invalid @enderror">{{ old('reason', $hrleave->reason) }}</textarea>
			</div>
		</div>

		<div id="wrapper" class="m-1">
		</div>

		<div class="form-group row m-1 @error('amend_note') has-error @enderror">
		<label for="amend_note" class="col-sm-4 col-form-label">Amend Note : </label>
			<div class="col-sm-8">
				<textarea name="amend_note" id="amend_note" class="w-100 form-control form-control-sm @error('amend_note') is-invalid @enderror">{{ old('amend_note', $hrleave->amend_note) }}</textarea>
			</div>
		</div>

		<div class="form-group m-1 row">
			<div class="col-sm-8 offset-sm-4">
				<button type="submit" class="btn btn-sm btn-outline-secondary">Submit Application</button>
			</div>
		</div>
	</form>
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
				id: {{ $hrleave->belongstostaff->id }},
				_token: '{!! csrf_token() !!}',
			}
			return data;
		}
	},
});

/////////////////////////////////////////////////////////////////////////////////////////
//  global variable : ajax to get the unavailable date
function getUnavailableDates(type) {
	var result;
	$.ajax({
		url: "{{ route('leavedate.unavailabledate') }}",
		type: "POST",
		data: {
			id: {{ $hrleave->staff_id }},
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
			id: {{ $hrleave->staff_id }},
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

/////////////////////////////////////////////////////////////////////////////////////////
function getTimeLeave(date) {
	let result = null;
	$.ajax({
		url: "{{ route('leavedate.timeleave') }}",
		type: "POST",
		data: {
			date: date,
			_token: '{!! csrf_token() !!}',
			id: {{ $hrleave->staff_id }}
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

/////////////////////////////////////////////////////////////////////////////////////////
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
					id: {{ $hrleave->staff_id }},
					_token: '{!! csrf_token() !!}',
					date_from: $(df).val(),
					date_to: $(dt).val()
				};
			}
		}
	});
}

/////////////////////////////////////////////////////////////////////////////////////////
function getHalfdayInfo(selector) {
	let d = false, itime_start = 0, itime_end = 0;
	$.each(getUnblockhalfdayleave(), function() {
		if (this.date_half_leave == selector.val()) {
			d = true;
			itime_start = this.time_start;
			itime_end = this.time_end;
			return false; // break
		}
	});
	return [d, itime_start, itime_end];
};

// let [d, itime_start, itime_end] = getHalfdayInfo(date);

/////////////////////////////////////////////////////////////////////////////////////////
let from = `
	<div class="form-group row m-2 @error('date_time_start') has-error @enderror">
		<label for="from" class="col-sm-4 col-form-label">From : </label>
		<div class="col-sm-8 datetime" style="position: relative">
			<input type="text" name="date_time_start" value="{{ old('date_time_start') }}" id="from" class="form-control form-control-sm @error('date_time_start') is-invalid @enderror" placeholder="From">
		</div>
	</div>
`;

/////////////////////////////////////////////////////////////////////////////////////////
let to = `
	<div class="form-group row m-2 @error('date_time_end') has-error @enderror">
		<label for="to" class="col-sm-4 col-form-label">To : </label>
		<div class="col-sm-8 datetime" style="position: relative">
			<input type="text" name="date_time_end" value="{{ old('date_time_end') }}" id="to" class="form-control form-control-sm @error('date_time_end') is-invalid @enderror" placeholder="To">
		</div>
	</div>
`;

/////////////////////////////////////////////////////////////////////////////////////////
let timeOffHtml = `
<div class="form-group row m-2 @error('time_start') has-error @enderror">
	<label for="to" class="col-sm-4 col-form-label">Time : </label>
	<div class="col-sm-8">
		<div class="form-row time">
			<div class="col-sm-8 m-2" style="position: relative">
				<input type="text" name="time_start" value="{{ old('time_start') }}" id="start" class="form-control form-control-sm @error('time_start') is-invalid @enderror" placeholder="Time Start">
			</div>
			<div class="col-sm-8 m-2" style="position: relative">
				<input type="text" name="time_end" value="{{ old('time_end') }}" id="end" class="form-control form-control-sm @error('time_end') is-invalid @enderror" placeholder="Time End">
			</div>
		</div>
	</div>
</div>`;

/////////////////////////////////////////////////////////////////////////////////////////
let wrapperday = `
	<div class="form-group row m-2 @error('leave_cat') has-error @enderror" id="wrapperday">
		<div class="form-group col-sm-8 offset-sm-4 form-check @error('half_type_id') has-error @enderror removehalfleave"  id="wrappertest">
		</div>
	</div>
`;

/////////////////////////////////////////////////////////////////////////////////////////
let leave_cat = `
	<label for="leave_cat" class="col-sm-4 col-form-label removehalfleave">Leave Category : </label>
	<div class="col-sm-8 m-0 removehalfleave" id="halfleave">
		<div class="form-check form-check-inline removehalfleave" id="removeleavehalf">
			<input type="radio" name="leave_cat" value="1" id="radio1" class="form-check-input removehalfleave @error('leave_cat') is-invalid @enderror" {{ ($hrleave->leave_cat == 1 || is_null($hrleave->leave_cat))?'checked':NULL }}>
			<label for="radio1" class="form-check-label removehalfleave m-2 my-auto">Full Day Off</label>
		</div>
		<div class="form-check form-check-inline removehalfleave" id="appendleavehalf">
			<input type="radio" name="leave_cat" value="2" id="radio2" class="form-check-input removehalfleave @error('leave_cat') is-invalid @enderror">
			<label for="radio2" class="form-check-label removehalfleave m-2 my-auto">Half Day Off</label>
		</div>
	</div>
	<div class="form-group col-sm-8 offset-sm-4 @error('half_type_id') has-error @enderror removehalfleave"  id="wrappertest">
	</div>
`;

/////////////////////////////////////////////////////////////////////////////////////////
function toggle_time_checkedam(obj) {
	return `
	<div class="form-check form-check-inline removetest">
		<input type="radio" name="half_type_id" value="1/${obj.time_start_am}/${obj.time_end_am}" id="am" class="form-check-input @error('half_type_id') is-invalid @enderror" ${toggle_time_start_am} ${checkedam}>
		<label for="am" class="form-check-label m-2 my-auto">
			${moment(obj.time_start_am, 'HH:mm:ss').format('h:mm a')} to ${moment(obj.time_end_am, 'HH:mm:ss').format('h:mm a')}
		</label>
	</div>
	<div class="form-check form-check-inline removetest">
		<input type="radio" name="half_type_id" value="2/${obj.time_start_pm}/${obj.time_end_pm}" id="pm" class="form-check-input @error('half_type_id') is-invalid @enderror" ${toggle_time_start_pm} ${checkedpm}>
		<label for="pm" class="form-check-label m-2 my-auto">
			${moment(obj.time_start_pm, 'HH:mm:ss').format('h:mm a')} to ${moment(obj.time_end_pm, 'HH:mm:ss').format('h:mm a')}
		</label>
	</div>
	`;
};

/////////////////////////////////////////////////////////////////////////////////////////
function toggle_time_hrleave(obj) {
	return `
	<div class="form-check form-check-inline removetest">
		<input type="radio" name="half_type_id" value="1/${obj.time_start_am}/${obj.time_end_am}" id="am" class="form-check-input @error('half_type_id') is-invalid @enderror" {{ ($hrleave->half_type_id == 1)?'checked=checked':NULL }}>
		<label for="am" class="form-check-label m-2 my-auto">
			${moment(obj.time_start_am, 'HH:mm:ss').format('h:mm a')} to ${moment(obj.time_end_am, 'HH:mm:ss').format('h:mm a')}
		</label>
	</div>
	<div class="form-check form-check-inline removetest">
		<input type="radio" name="half_type_id" value="2/${obj.time_start_pm}/${obj.time_end_pm}" id="pm" class="form-check-input @error('half_type_id') is-invalid @enderror" {{ ($hrleave->half_type_id == 2)?'checked=checked':NULL }}>
		<label for="pm" class="form-check-label m-2 my-auto">
			${moment(obj.time_start_pm, 'HH:mm:ss').format('h:mm a')} to ${moment(obj.time_end_pm, 'HH:mm:ss').format('h:mm a')}
		</label>
	</div>
	`;
};

/////////////////////////////////////////////////////////////////////////////////////////
let replacementForm = `
	<?php
	$oi = \App\Models\Staff::find($hrleave->staff_id)->hasmanyleavereplacement()->get();
	$oi1 = \App\Models\HumanResources\HRLeave::find($hrleave->id)->belongstomanyleavereplacement()->get()->map(function($lrid){
		return $lrid->id;
	})->first();
	?>
	<div class="form-group row m-2 @error('nrla') has-error @enderror">
		<label for="nrla" class="col-sm-4 col-form-label">Please Choose Your Replacement Leave : </label>
		<div class="col-sm-8 nrl">
			<p>Total Replacement Leave = {{ $oi->sum('leave_balance') }} days</p>
			<select name="id" id="nrla" class="form-select form-select-sm @error('id') is-invalid @enderror">
				<option value="">Please select</option>
			@foreach( $oi as $po )
				<option value="{{ $po->id }}" data-nrlbalance="{{ $po->leave_balance }}" {{ ($po->id == $oi1)?'selected':NULL }}>On ${moment( '{{ $po->date_start }}', 'YYYY-MM-DD' ).format('ddd Do MMM YYYY')}, your leave balance = {{ $po->leave_balance }} day</option>
			@endforeach
			</select>
		</div>
	</div>
`;

/////////////////////////////////////////////////////////////////////////////////////////
let userneedbackup = `
	<div class="form-group row m-2 @error('staff_id') has-error @enderror">
		<label for="backupperson" class="col-sm-4 col-form-label">Replacement : </label>
		<div class="col-sm-8 backup">
			<select name="staff_id" id="backupperson" class="form-select form-select-sm @error('staff_id') is-invalid @enderror" placeholder="Please choose" autocomplete="off">
				@foreach(App\Models\Staff::where('active', 1)->get()->pluck('name', 'id') as $k => $v)
					<option value="{{ $k }}" {{ ($hrleave->hasmanyleaveapprovalbackup?->first()?->staff_id == $k)?'selected':NULL }}>{{ $v }}</option>
				@endforeach
			</select>
		</div>
	</div>
`;

/////////////////////////////////////////////////////////////////////////////////////////
let doc = `
	<div class="form-group row m-2 @error('document') has-error @enderror">
		<label for="doc" class="col-sm-4 col-form-label">Upload Supporting Document : </label>
		<div class="col-sm-8 supportdoc">
			<input type="file" name="document" id="doc" class="form-control form-control-sm form-control-file @error('document') is-invalid @enderror" placeholder="Supporting Document">
		</div>
	</div>
`;

/////////////////////////////////////////////////////////////////////////////////////////
let suppdoc = `
	<div class="form-group row m-2 @error('documentsupport') has-error @enderror">
		<div class="offset-sm-4 col-sm-8 form-check">
			<input type="checkbox" name="documentsupport" value="1" id="suppdoc" class="form-check-input @error('documentsupport') is-invalid @enderror">
			<label for="suppdoc" class="form-check-label p-1 bg-warning text-danger rounded">Please ensure you will submit <strong>Supporting Documents</strong> within <strong>3 Days</strong> after date leave.</label>
		</div>
	</div>
`;

/////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////
function initDatepicker(selector) {
	let options = {
		icons: datetimeIcons,
		format:'YYYY-MM-DD',
		useCurrent: false,
	};
	return $(selector).datetimepicker(options);
}
/////////////////////////////////////////////////////////////////////////////////////////

/////////////////////////////////////////////////////////////////////////////////////////
// start setting up the leave accordingly.
/////////////////////////////////////////////////////////////////////////////////////////
$(document).ready(function(){
	if ($('#leave_id').val() == '9') {													// if TF
		$('#wrapper').append(`
			<div id="remove">
				<div class="form-group row m-2 @error('date_time_start') has-error @enderror">
					<label for="from" class="col-sm-4 col-form-label">From : </label>
					<div class="col-sm-8 datetime" style="position: relative">
						<input type="text" name="date_time_start" value="{{ old('date_time_start', $hrleave->date_time_start) }}" id="from" class="form-control form-control-sm @error('date_time_start') is-invalid @enderror" placeholder="From">
					</div>
				</div>

				<div class="form-group row m-2 @error('time_start') has-error @enderror">
					<label for="to" class="col-sm-4 col-form-label">Time : </label>
					<div class="col-sm-8">
						<div class="form-row time">
							<div class="col-sm-8 m-2" style="position: relative">
								<input type="text" name="time_start" value="{{ old('time_start') }}" id="start" class="form-control form-control-sm @error('time_start') is-invalid @enderror" placeholder="Time Start">
							</div>
							<div class="col-sm-8 m-2" style="position: relative">
								<input type="text" name="time_end" value="{{ old('time_end') }}" id="end" class="form-control form-control-sm @error('time_end') is-invalid @enderror" placeholder="Time End">
							</div>
						</div>
					</div>
				</div>
				@if( $userneedbackup == 1 )
				@if($hrleave->leave_type_id != 2 || $hrleave->leave_type_id != 11)

				@endif
				@if( $backup )
				<div class="form-group row m-2 @error('staff_id') has-error @enderror">
					<label for="backupperson" class="col-sm-4 col-form-label">Replacement : </label>
					<div class="col-sm-8 backup">
						<select name="staff_id" id="backupperson" class="form-select form-select-sm @error('staff_id') is-invalid @enderror" placeholder="Please choose" autocomplete="off">
							@foreach(App\Models\Staff::where('active', 1)->get()->pluck('name', 'id') as $k => $v)
							<option value="{{ $k }}" {{ ($hrleave->hasmanyleaveapprovalbackup?->first()->staff_id == $k)?'selected':NULL }}>{{ $v }}</option>
							@endforeach
						</select>
					</div>
				</div>
					@endif
				@endif

				<div class="form-group row m-2 @error('document') has-error @enderror">
					<label for="doc" class="col-sm-4 col-form-label">Upload Supporting Document : </label>
					<div class="col-sm-8 supportdoc">
						<input type="file" name="document" id="doc" class="form-control form-control-sm form-control-file @error('document') is-invalid @enderror" placeholder="Supporting Document">
					</div>
				</div>

				<div class="form-group row m-2 @error('documentsupport') has-error @enderror">
					<div class="offset-sm-4 col-sm-8 form-check">
						<input type="checkbox" name="documentsupport" value="1" id="suppdoc" class="form-check-input @error('documentsupport') is-invalid @enderror">
						<label for="suppdoc" class="form-check-label p-1 bg-warning text-danger rounded">Please ensure you will submit <strong>Supporting Documents</strong> within <strong>3 Days</strong> after date leave.</label>
					</div>
				</div>
			</div>`
		);
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_start"]'));
	} else {																			// other than TF

		var datenow = '{{ Carbon::parse($hrleave->date_time_start)->format('Y-m-d') }}';

		// convert data1 into json
		var obj = getTimeLeave(datenow);

		$('#wrapper').append(`
			<div id="remove">

				@if($hrleave->leave_type_id == 4 || $hrleave->leave_type_id == 10)
				<?php
				$oi = \App\Models\Staff::find($hrleave->staff_id)->hasmanyleavereplacement()->get();
				$oi1 = \App\Models\HumanResources\HRLeave::find($hrleave->id)->belongstomanyleavereplacement()->get()->map(function($lrid){
					return $lrid->id;
				})->first();
				?>
				<div class="form-group row m-2 @error('nrla') has-error @enderror">
					<label for="nrla" class="col-sm-4 col-form-label">Please Choose Your Replacement Leave : </label>
					<div class="col-sm-8 nrl">
						<p>Total Replacement Leave = {{ $oi->sum('leave_balance') }} days</p>
						<select name="id" id="nrla" class="form-select form-select-sm @error('id') is-invalid @enderror">
							<option value="">Please select</option>
						@foreach( $oi as $po )
							<option value="{{ $po->id }}" data-nrlbalance="{{ $po->leave_balance }}" {{ ($po->id == $oi1)?'selected':NULL }}>On ${moment( '{{ $po->date_start }}', 'YYYY-MM-DD' ).format('ddd Do MMM YYYY')}, your leave balance = {{ $po->leave_balance }} day</option>
						@endforeach
						</select>
					</div>
				</div>
				@endif

				<div class="form-group row m-2 @error('date_time_start') has-error @enderror">
					<label for="from" class="col-sm-4 col-form-label">From : </label>
					<div class="col-sm-8 datetime" style="position: relative">
						<input type="text" name="date_time_start" value="{{ old('date_time_start', $hrleave->date_time_start) }}" id="from" class="form-control form-control-sm @error('date_time_start') is-invalid @enderror" placeholder="From">
					</div>
				</div>

				<div class="form-group row m-2 @error('date_time_end') has-error @enderror">
					<label for="to" class="col-sm-4 col-form-label">To : </label>
					<div class="col-sm-8 datetime" style="position: relative">
						<input type="text" name="date_time_end" value="{{ old('date_time_end', $hrleave->date_time_end) }}" id="to" class="form-control form-control-sm @error('date_time_end') is-invalid @enderror" placeholder="To">
					</div>
				</div>

				<div class="form-group row m-2 @error('leave_cat') has-error @enderror" id="wrapperday">
					@if($hrleave->period_day <= 1)
						<label for="leave_cat" class="col-sm-4 col-form-label removehalfleave">Leave Category : </label>
						<div class="col-sm-8 m-0 removehalfleave" id="halfleave">
							<div class="form-check form-check-inline removehalfleave" id="removeleavehalf">
								<input type="radio" name="leave_cat" value="1" id="radio1" class="form-check-input removehalfleave @error('') is-invalid @enderror" {{ ($hrleave->leave_cat == 1 || is_null($hrleave->leave_cat))?'checked':NULL }}>
								<label for="radio1" class="form-check-label removehalfleave m-2 my-auto">Full Day Off</label>
							</div>
							<div class="form-check form-check-inline removehalfleave" id="appendleavehalf">
								<input type="radio" name="leave_cat" value="2" id="radio2" class="form-check-input removehalfleave @error('') is-invalid @enderror" {{ ($hrleave->leave_cat == 2)?'checked':NULL }}>
								<label for="radio2" class="form-check-label removehalfleave m-2 my-auto">Half Day Off</label>
							</div>
						</div>
						<div class="form-group col-sm-8 offset-sm-4 @error('half_type_id') has-error @enderror removehalfleave"  id="wrappertest">
							@if($hrleave->period_day <= 0.5)
								<div class="form-check form-check-inline removetest">
									<input type="radio" name="half_type_id" value="1/${obj.time_start_am}/${obj.time_end_am}" id="am" class="form-check-input @error('half_type_id') is-invalid @enderror" {{ ($hrleave->half_type_id == 1)?'checked=checked':NULL }}>
									<label for="am" class="form-check-label m-2 my-auto">
										${moment(obj.time_start_am, 'HH:mm:ss').format('h:mm a')} to ${moment(obj.time_end_am, 'HH:mm:ss').format('h:mm a')}
									</label>
								</div>
								<div class="form-check form-check-inline removetest">
									<input type="radio" name="half_type_id" value="2/${obj.time_start_pm}/${obj.time_end_pm}" id="pm" class="form-check-input @error('half_type_id') is-invalid @enderror" {{ ($hrleave->half_type_id == 2)?'checked=checked':NULL }}>
									<label for="pm" class="form-check-label m-2 my-auto">
										${moment(obj.time_start_pm, 'HH:mm:ss').format('h:mm a')} to ${moment(obj.time_end_pm, 'HH:mm:ss').format('h:mm a')}
									</label>
								</div>
							@endif
						</div>
					@endif

				</div>
				@if( $userneedbackup == 1 )
					@if( $backup )
						${userneedbackup}
					@endif
				@endif
				<div class="form-group row m-2 @error('document') has-error @enderror">
					<label for="doc" class="col-sm-4 col-form-label">Upload Supporting Document : </label>
					<div class="col-sm-8 supportdoc">
						<input type="file" name="document" id="doc" class="form-control form-control-sm form-control-file @error('document') is-invalid @enderror" placeholder="Supporting Document">
					</div>
				</div>
				<div class="form-group row m-2 @error('documentsupport') has-error @enderror">
					<div class="offset-sm-4 col-sm-8 form-check">
						<input type="checkbox" name="documentsupport" value="1" id="suppdoc" class="form-check-input @error('documentsupport') is-invalid @enderror">
						<label for="suppdoc" class="form-check-label p-1 bg-warning text-danger rounded">Please ensure you will submit <strong>Supporting Documents</strong> within <strong>3 Days</strong> after date leave.</label>
					</div>
				</div>
			</div>
		`);

		$(document).on('change', '#appendleavehalf :radio', function () {
			if (this.checked) {
				if( $('.removetest').length == 0 ) {
					$('#wrappertest').append(`${toggle_time_hrleave(obj)}`);
					if( moment('{{ Carbon::parse($hrleave->date_time_start)->format('H:i:s') }}').isSame(moment(obj.time_start_am, 'HH:mm:ss')) ) {
						// console.log('ppagi');
						$('#am').prop('checked', true);
					} else {
						// console.log('ptg');
						$('#pm').prop('checked', true);
					}
				}
			}
		});

		if( moment('{{ Carbon::parse($hrleave->date_time_start)->format('H:i:s') }}').isSame(moment(obj.time_start_am, 'HH:mm:ss')) ) {
			// console.log('ppagi');
			$('#am').prop('checked', true);
			$('#pm').prop('checked', false);
		} else {
			// console.log('ptg');
			$('#am').prop('checked', false);
			$('#pm').prop('checked', true);
		}

		$(document).on('change', '#removeleavehalf :radio', function () {
			if (this.checked) {
				$('.removetest').remove();
			}
		});
	}
	// start date
	initDatepicker('#from').on('dp.change dp.update', function(e) {
		// $('#form').bootstrapValidator('revalidateField', 'date_time_start');
		$('#to').datetimepicker('minDate', $('#from').val());

			if($('#from').val() === $('#to').val()) {
				if( $('.removehalfleave').length === 0) {

					////////////////////////////////////////////////////////////////////////////////////////
					// checking half day leave
					let [d, itime_start, itime_end] = getHalfdayInfo($('#from'));
					// console.log(d);
					if(d === true) {
						$('#wrapperday').append(`${leave_cat}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

						var daynow = moment($('#from').val(), 'YYYY-MM-DD').format('dddd');
						var datenow = $('#from').val();

						// convert data1 into json
						// var obj = getTimeLeave(datenow);
						var obj = getTimeLeave('#from');

						var checkedam = '';
						var checkedpm = '';
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

						$('#wrappertest').append(`${toggle_time_checkedam(obj)}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

					} else {
						$('#wrapperday').append(`${leave_cat}`);
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
	});
	// end date from

	// end date
	initDatepicker('#to').on('dp.change dp.update', function(e) {
		// $('#to').bootstrapValidator('revalidateField', 'date_time_start');
		$('#from').datetimepicker('maxDate', $('#to').val());

		if($('#from').val() === $('#to').val()) {
			if( $('.removehalfleave').length === 0) {

				////////////////////////////////////////////////////////////////////////////////////////
				// checking half day leave
				let [d, itime_start, itime_end] = getHalfdayInfo($('#to'));
				// console.log(d);
				if(d === true) {
					$('#wrapperday').append(`${leave_cat}`);
					$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
					$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

					var daynow = moment($('#from').val(), 'YYYY-MM-DD').format('dddd');
					var datenow = $('#to').val();

					// convert data1 into json
					// var obj = getTimeLeave(datenow);
					var obj = getTimeLeave('#to');

					var checkedam = '';
					var checkedpm = '';
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

					$('#wrappertest').append(`${toggle_time_checkedam(obj)}`);
					$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
					$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

				} else {
					$('#wrapperday').append(`${leave_cat}`);
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
	});

	// time start
	$('#start').datetimepicker({
		icons: datetimeIcons,
		format: 'h:mm A',
	});

	// time end
	$('#end').datetimepicker({
		icons: datetimeIcons,
		format: 'h:mm A',
	});

	//enable select 2 for backup
	initBackupPerson();

});

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////
// start here when user start to select the leave type option
$('#leave_id').on('change', function() {
	let $selection = $(this).find(':selected');
	// console.log($selection);

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// annual leave & UPL
	if ($selection.val() == '1' || $selection.val() == '3') {
		$('#remove').remove();
		if($selection.val() == '3') {
			$('#wrapper').append(`
				<div id="remove">
					${from}
					${to}
					${wrapperday}
					@if( $userneedbackup == 1 )
						@if( $backup )
							${userneedbackup}
						@endif
					@endif
					${doc}
					${suppdoc}
				</div>
			`);
		} else {
			$('#wrapper').append(`
				<div id="remove">
					${from}
					${to}
					${wrapperday}
					@if( $userneedbackup == 1 )
						@if( $backup )
							${userneedbackup}
						@endif
					@endif
				</div>
			`);
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
		initBackupPerson();

		/////////////////////////////////////////////////////////////////////////////////////////
		// start date
		initDatepicker('#from').on('dp.change dp.update', function(e) {
			$('#form').bootstrapValidator('revalidateField', 'date_time_start');
			var minDaten = $('#from').val();
			// console.log(minDaten);
			$('#to').datetimepicker('minDate', minDaten);
			if($('#from').val() === $('#to').val()) {
				if( $('.removehalfleave').length === 0) {

					////////////////////////////////////////////////////////////////////////////////////////
					// checking half day leave
					let [d, itime_start, itime_end] = getHalfdayInfo($('#from'));
					// console.log(d);
					if(d === true) {
						$('#wrapperday').append(`${leave_cat}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

						var daynow = moment($('#from').val(), 'YYYY-MM-DD').format('dddd');
						var datenow = $('#from').val();

						// convert data1 into json
						var obj = getTimeLeave(datenow);

						var checkedam = '';
						var checkedpm = '';
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
						$('#wrappertest').append(`${toggle_time_checkedam(obj)}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

					} else {
						$('#wrapperday').append(`${leave_cat}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
					}
					////////////////////////////////////////////////////////////////////////////////////////
					// end checking half day leave
				}
			}
			if($('#from').val() !== $('#to').val()) {
				$('.removehalfleave').remove();
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
			}
		});

		initDatepicker('#to').on('dp.change dp.update', function(e) {
			$('#form').bootstrapValidator('revalidateField', 'date_time_end');
			var maxDate = $('#to').val();
			$('#from').datetimepicker('maxDate', maxDate);
			if($('#from').val() === $('#to').val()) {
				if( $('.removehalfleave').length === 0) {

					////////////////////////////////////////////////////////////////////////////////////////
					// checking half day leave
					let [d, itime_start, itime_end] = getHalfdayInfo($('#to'));
					// console.log(d);
					if(d === true) {
						$('#wrapperday').append(`${leave_cat}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

						var daynow = moment($('#from').val(), 'YYYY-MM-DD').format('dddd');
						var datenow = $('#to').val();

						// convert data1 into json
						var obj = getTimeLeave(datenow);

						var checkedam = '';
						var checkedpm = '';
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
						$('#wrappertest').append(`${toggle_time_checkedam(obj)}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

					} else {
						$('#wrapperday').append(`${leave_cat}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
					}
					////////////////////////////////////////////////////////////////////////////////////////
					// end checking half day leave
				}
			}
			if($('#from').val() !== $('#to').val()) {
				$('.removehalfleave').remove();
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
			}
		});
		// end date
		/////////////////////////////////////////////////////////////////////////////////////////
		// enable radio
		$(document).on('change', '#appendleavehalf :radio', function () {
			if (this.checked) {
				var daynow = moment($('#from').val(), 'YYYY-MM-DD').format('dddd');
				var datenow = $('#from').val();

				// convert data1 into json
				var obj = getTimeLeave(datenow);

				// checking so there is no double
				if( $('.removetest').length == 0 ) {
					$('#wrappertest').append(`${toggle_time_hrleave(obj)}`);
					$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
					$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
				}
			}
		});

		$(document).on('change', '#removeleavehalf :radio', function () {
			if (this.checked) {
				$('.removetest').remove();
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
			}
		});
		if( $('#from').val() == $('#to').val() ) {
			$('#form').bootstrapValidator('addField', $('#halfleave').find('[name="leave_cat"]'));
		}
	}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($selection.val() == '2') {

		$('#remove').remove();
		$('#wrapper').append(`
			<div id="remove">
				${from}
				${to}
				@if($setHalfDayMC == 1)
					${wrapperday}
				@endif
				@if( $userneedbackup == 99 )
					@if( $backup )
						${userneedbackup}
					@endif
				@endif
				${doc}
				${suppdoc}
			</div>
		`);

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
		initBackupPerson();

		// enable datetime for the 1st one
		initDatepicker('#from').on('dp.change dp.update', function(e) {
			$('#form').bootstrapValidator('revalidateField', 'date_time_start');
			var minDaten = $('#from').val();
			$('#to').datetimepicker('minDate', minDaten);

			@if($setHalfDayMC == 1)
			if($('#from').val() === $('#to').val()) {
				if( $('.removehalfleave').length === 0) {

					////////////////////////////////////////////////////////////////////////////////////////
					// checking half day leave
					let [d, itime_start, itime_end] = getHalfdayInfo($('#from'));
					// console.log(d);
					if(d === true) {
						$('#wrapperday').append(`${leave_cat}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

						var daynow = moment($('#from').val(), 'YYYY-MM-DD').format('dddd');
						var datenow = $('#from').val();

						// convert data1 into json
						var obj = getTimeLeave(datenow);

						var checkedam = '';
						var checkedpm = '';
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
						$('#wrappertest').append(`${toggle_time_checkedam(obj)}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

					} else {
						$('#wrapperday').append(`${toggle_time_hrleave(obj)}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
					}
					////////////////////////////////////////////////////////////////////////////////////////
					// end checking half day leave
				}
			}
			if($('#from').val() !== $('#to').val()) {
				$('.removehalfleave').remove();
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
			}
			@endif
		});

		initDatepicker('#to').on('dp.change dp.update', function(e) {
			$('#form').bootstrapValidator('revalidateField', 'date_time_end');
			var maxDate = $('#to').val();
			$('#from').datetimepicker('maxDate', maxDate);

			@if($setHalfDayMC == 1)
			if($('#from').val() === $('#to').val()) {
				if( $('.removehalfleave').length === 0) {

					////////////////////////////////////////////////////////////////////////////////////////
					// checking half day leave
					let [d, itime_start, itime_end] = getHalfdayInfo($('#to'));
					// console.log(d);
					if(d === true) {
						$('#wrapperday').append(`${leave_cat}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

						var daynow = moment($('#from').val(), 'YYYY-MM-DD').format('dddd');
						var datenow =  $('#to').val();

						// convert data1 into json
						var obj = getTimeLeave(datenow);

						var checkedam = '';
						var checkedpm = '';
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
						$('#wrappertest').append(`${toggle_time_checkedam(obj)}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

					} else {
						$('#wrapperday').append(`${leave_cat}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
					}
					////////////////////////////////////////////////////////////////////////////////////////
					// end checking half day leave
				}
			}
			if($('#from').val() !== $('#to').val()) {
				$('.removehalfleave').remove();
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
			}
			@endif
		});
		// end date

		@if($setHalfDayMC == 1)
		/////////////////////////////////////////////////////////////////////////////////////////
		// enable radio
		$(document).on('change', '#appendleavehalf :radio', function () {
			if (this.checked) {
				var daynow = moment($('#from').val(), 'YYYY-MM-DD').format('dddd');
				var datenow = $('#to').val();

				// convert data1 into json
				var obj = getTimeLeave(datenow);

				// checking so there is no double
				if( $('.removetest').length == 0 ) {
					$('#wrappertest').append(`${toggle_time_hrleave(obj)}`);
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
$oi = $hrleave->belongstostaff->hasmanyleavereplacement()->where('leave_balance', '<>', 0)->get();
?>
	if ($selection.val() == '4') {
		$('#remove').remove();
		$('#wrapper').append(`
			<div id="remove">
			${replacementForm}
			${from}
			${to}
			${wrapperday}
				@if( $userneedbackup == 1 )
					@if( $backup )
						${userneedbackup}
					@endif
				@endif
			</div>
		`);

		/////////////////////////////////////////////////////////////////////////////////////////
		// more option
		$('#form').bootstrapValidator('addField', $('.nrl').find('[name="id"]'));
		@if( $userneedbackup == 1 )
		$('#form').bootstrapValidator('addField', $('.backup').find('[name="staff_id"]'));
		@endif
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_start"]'));
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_end"]'));
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));


		/////////////////////////////////////////////////////////////////////////////////////////
		// enable select2 on nrla
		$('#nrla').select2({
			placeholder: 'Please select',
			width: '100%',
		});

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable select2
		initBackupPerson();

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable datetime for the 1st one
		initDatepicker('#from').on('dp.change dp.update', function(e) {
			$('#form').bootstrapValidator('revalidateField', 'date_time_start');
			var minDaten = $('#from').val();
			// console.log(minDaten);
			$('#to').datetimepicker('minDate', minDaten);

			if($('#from').val() === $('#to').val()) {
				if( $('.removehalfleave').length === 0) {

					////////////////////////////////////////////////////////////////////////////////////////
					// checking half day leave
					let [d, itime_start, itime_end] = getHalfdayInfo($('#from'));
					// console.log(d);
					if(d === true) {
						$('#wrapperday').append(`${leave_cat}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

						var daynow = moment($('#from').val(), 'YYYY-MM-DD').format('dddd');
						var datenow = $('#from').val();

						// convert data1 into json
						var obj = getTimeLeave(datenow);

						var checkedam = '';
						var checkedpm = '';
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
						$('#wrappertest').append(`${toggle_time_checkedam(obj)}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

					} else {
						$('#wrapperday').append(`${leave_cat}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
					}
					////////////////////////////////////////////////////////////////////////////////////////
					// end checking half day leave
				}
			}
			if($('#from').val() !== $('#to').val()) {
				$('.removehalfleave').remove();
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
			}
		});

		initDatepicker('#to').on('dp.change dp.update', function(e) {
			$('#form').bootstrapValidator('revalidateField', 'date_time_end');
			var maxDate = $('#to').val();
			$('#from').datetimepicker('maxDate', maxDate);
			if($('#from').val() === $('#to').val()) {
				if( $('.removehalfleave').length === 0) {

					////////////////////////////////////////////////////////////////////////////////////////
					// checking half day leave
					let [d, itime_start, itime_end] = getHalfdayInfo($('#to'));
					// console.log(d);
					if(d === true) {
						$('#wrapperday').append(`${leave_cat}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

						var daynow = moment($('#from').val(), 'YYYY-MM-DD').format('dddd');
						var datenow = $('#to').val();

						// convert data1 into json
						var obj = getTimeLeave(datenow);

						var checkedam = '';
						var checkedpm = '';
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
						$('#wrappertest').append(`${toggle_time_checkedam(obj)}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

					} else {
						$('#wrapperday').append(`${leave_cat}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
					}
					////////////////////////////////////////////////////////////////////////////////////////
					// end checking half day leave
				}
			}
			if($('#from').val() !== $('#to').val()) {
				$('.removehalfleave').remove();
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
			}
		});

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable radio
		$(document).on('change', '#appendleavehalf :radio', function () {
			if (this.checked) {
				var daynow = moment($('#from').val(), 'YYYY-MM-DD').format('dddd');
				var datenow =  $('#from').val();

				// convert data1 into json
				var obj = getTimeLeave(datenow);

				// checking so there is no double
				if( $('.removetest').length == 0 ) {
					$('#wrappertest').append(`${toggle_time_hrleave(obj)}`);
				}
			}
		});

		$(document).on('change', '#removeleavehalf :radio', function () {
		// $('#removeleavehalf :radio').change(function() {
			if (this.checked) {
				console.log( $('#nrla option:selected').data('nrlbalance') );
				if( $('#nrla option:selected').data('nrlbalance') == 0.5 ) {

					// especially for select 2, if no select2, remove change()
					$('#nrla option:selected').prop('selected', false).change();
					// $('#nrla').val('').change();
				}
				$('.removetest').remove();
			}
		});

		/////////////////////////////////////////////////////////////////////////////////////////
		// checking for half day click but select for 1 full day
		$('#nrla').change(function() {
			let selectedOption = $('option:selected', this);
			$('#form').bootstrapValidator('revalidateField', 'leave_id');
			var nrlbal = selectedOption.data('nrlbalance');
			if (nrlbal == 0.5) {
				// make sure from and to date got value
				$('#from').val(moment().add(3, 'days').format('YYYY-MM-DD'));
				$('#to').val(moment().add(3, 'days').format('YYYY-MM-DD'));

				$('#radio2').prop('checked', true);
				// checking so there is no double

				var daynow = moment($('#from').val(), 'YYYY-MM-DD').format('dddd');
				var datenow = $('#from').val();

				// convert data1 into json
				var obj = getTimeLeave(datenow);

				// checking so there is no double
				if( $('.removetest').length == 0 ) {
					$('#wrappertest').append(`${toggle_time_hrleave(obj)}`);
				}
			} else {
				if( nrlbal != 0.5 ) {
					$('#radio1').prop('checked', true);
					$('.removetest').remove();
				}
			}
		});
	}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// maternity leave
	if ($selection.val() == '7') {

		$('#remove').remove();
		$('#wrapper').append(`
			<div id="remove">
				${from}
				${to}
				@if( $userneedbackup == 1 )
					@if( $backup )
						${userneedbackup}
					@endif
				@endif
			</div>
		`);


		/////////////////////////////////////////////////////////////////////////////////////////
		// more option
		//add bootstrapvalidator
		// more option
		$('#form').bootstrapValidator('addField', $('.nrl').find('[name="leave_id"]'));
		@if( $userneedbackup == 1 )
		$('#form').bootstrapValidator('addField', $('.backup').find('[name="staff_id"]'));
		@endif
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_start"]'));
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_end"]'));
		$('#form').bootstrapValidator('addField', $('.supportdoc').find('[name="document"]'));
		$('#form').bootstrapValidator('addField', $('.suppdoc').find('[name="documentsupport"]'));

		/////////////////////////////////////////////////////////////////////////////////////////
		//enable select 2 for backup
		initBackupPerson();

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable datetime for the 1st one
		$('#from').datetimepicker({
			icons: datetimeIcons,
			format:'YYYY-MM-DD',
			useCurrent: false,
			minDate: moment().format('YYYY-MM-DD'),
			disabledDates:getUnavailableDates(1),
		})
		.on('dp.change dp.update', function(e) {
			$('#form').bootstrapValidator('revalidateField', 'date_time_start');
			var minDate = $('#from').val();
			$('#to').datetimepicker('minDate', moment( minDate, 'YYYY-MM-DD').add(59, 'days').format('YYYY-MM-DD') );
			$('#to').val( moment( minDate, 'YYYY-MM-DD').add(59, 'days').format('YYYY-MM-DD') );
		});

		$('#to').datetimepicker({
			icons: datetimeIcons,
			format:'YYYY-MM-DD',
			useCurrent: false,
			minDate: moment().format('YYYY-MM-DD'),
			disabledDates:getUnavailableDates(1),
		})
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
		$('#wrapper').append(`
			<div id="remove">
				${from}
				${to}
				${wrapperday}
				@if( $userneedbackup == 1 )
					@if ($backup)
						${userneedbackup}
					@endif
				@endif
				${doc}
				${suppdoc}
			</div>
		`);
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
		//enable select 2 for backup
		initBackupPerson();

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable datetime for the 1st one
		initDatepicker('#from').on('dp.change dp.update', function(e) {
			$('#form').bootstrapValidator('revalidateField', 'date_time_start');
			var minDaten = $('#from').val();
			$('#to').datetimepicker('minDate', minDaten);

			if($('#from').val() === $('#to').val()) {
				if( $('.removehalfleave').length === 0) {

					////////////////////////////////////////////////////////////////////////////////////////
					// checking half day leave
					let [d, itime_start, itime_end] = getHalfdayInfo($('#from'));
					// console.log(d);
					if(d === true) {
						$('#wrapperday').append(`${leave_cat}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

						var daynow = moment($('#from').val(), 'YYYY-MM-DD').format('dddd');
						var datenow = $('#from').val();

						// convert data1 into json
						var obj = getTimeLeave(datenow);

						var checkedam = '';
						var checkedpm = '';
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
						$('#wrappertest').append(`${toggle_time_checkedam(obj)}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

					} else {
						$('#wrapperday').append(`${leave_cat}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
					}
					////////////////////////////////////////////////////////////////////////////////////////
					// end checking half day leave
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
					$('#backupwrapper').append(`${userneedbackup}`);
					$('#form').bootstrapValidator('addField', $('.backup').find('[name="staff_id"]'));
					initBackupPerson();
				}
			} else {
				$('#form').bootstrapValidator('removeField', $('.backup').find('[name="staff_id"]'));
				$('#backupwrapper').children().remove();
			}
			@endif
		});

		initDatepicker('#to').on('dp.change dp.update', function(e) {
			$('#form').bootstrapValidator('revalidateField', 'date_time_end');
			var maxDate = $('#to').val();
			$('#from').datetimepicker('maxDate', maxDate);

			if($('#from').val() === $('#to').val()) {
				if( $('.removehalfleave').length === 0) {

					////////////////////////////////////////////////////////////////////////////////////////
					// checking half day leave
					let [d, itime_start, itime_end] = getHalfdayInfo($('#to'));
					// console.log(d);
					if(d === true) {
						$('#wrapperday').append(`${leave_cat}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

						var daynow = moment($('#from').val(), 'YYYY-MM-DD').format('dddd');
						var datenow = $('#to').val();

						// convert data1 into json
						var obj = getTimeLeave(datenow);

						var checkedam = '';
						var checkedpm = '';
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
						$('#wrappertest').append(`${toggle_time_checkedam(obj)}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

					} else {
						$('#wrapperday').append(`${leave_cat}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
					}
					////////////////////////////////////////////////////////////////////////////////////////
					// end checking half day leave
				}
			}
			if($('#from').val() !== $('#to').val()) {
				$('.removehalfleave').remove();
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
			}
		});

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable radio
		$(document).on('change', '#appendleavehalf :radio', function () {
			if (this.checked) {
				var daynow = moment($('#from').val(), 'YYYY-MM-DD').format('dddd');
				var datenow = $('#to').val();

				var data1 = $.ajax({
					url: "{{ route('leavedate.timeleave') }}",
					type: "POST",
					data: {
							date: datenow,
							_token: '{!! csrf_token() !!}',
							id: {{ $hrleave->belongstostaff->id }}
					},
					dataType: 'json',
					global: false,
					async:false,
					success: function (response) {
						// you will get response from your php page (what you echo or print)
						return response;
					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.log(textStatus, errorThrown);
					}
				}).responseText;

				// convert data1 into json
				var obj = jQuery.parseJSON( data1 );

				// checking so there is no double
				if( $('.removetest').length == 0 ) {
					$('#wrappertest').append(`${toggle_time_hrleave(obj)}`);
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
		$('#wrapper').append(`
			<div id="remove">

				${from}
				${timeOffHtml}
				@if( $userneedbackup == 1 )
				@if( $backup )
					${userneedbackup}
				@endif
				@endif
				${doc}
				${suppdoc}
			</div>
		`);
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
		initBackupPerson();

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable datetime for the 1st one
		initDatepicker('#from').on('dp.change ', function(e) {
			$('#form').bootstrapValidator('revalidateField', 'date_time_start');

			@if( $userneedbackup == 1 )
			// enable backup if date from is greater or equal than today.
			//cari date now dulu
			if( $('#from').val() >= moment().format('YYYY-MM-DD') ) {
				// console.log( moment().add(1, 'days').format('YYYY-MM-DD') );
				// console.log($( '#rembackup').children().length + ' <= rembackup length' );
				if( $('#backupwrapper').children().length == 0 ) {
					$('#backupwrapper').append(`${userneedbackup}`);
					$('#form').bootstrapValidator('addField', $('.backup').find('[name="staff_id"]'));
					initBackupPerson();
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
		$('#wrapper').append(`
			<div id="remove">

				${from}
				${to}
				@if($setHalfDayMC == 1)
	<div class="form-group row m-2 @error('leave_cat') has-error @enderror" id="wrapperday">
		<div class="form-group col-sm-8 offset-sm-4 form-check @error('half_type_id') has-error @enderror removehalfleave"  id="wrappertest">
						@if($hrleave->period_day <= 0.5)
		<div class="form-check form-check-inline removetest">
			<input type="radio" name="half_type_id" value="1/${obj.time_start_am}/${obj.time_end_am}" id="am" class="form-check-input @error('half_type_id') is-invalid @enderror">
			<label for="am" class="form-check-label m-2 my-auto">${moment(obj.time_start_am, 'HH:mm:ss').format('h:mm a')} to ${moment(obj.time_end_am, 'HH:mm:ss').format('h:mm a')}</label>
		</div>
		<div class="form-check form-check-inline removetest">
			<input type="radio" name="half_type_id" value="2/${obj.time_start_pm}/${obj.time_end_pm}" id="pm" class="form-check-input @error('half_type_id') is-invalid @enderror">
			<label for="pm" class="form-check-label m-2 my-auto">${moment(obj.time_start_pm, 'HH:mm:ss').format('h:mm a')} to ${moment(obj.time_end_pm, 'HH:mm:ss').format('h:mm a')}</label>
		</div>
						@endif
					</div>
				</div>
				@endif
				@if( $userneedbackup == 1 )
					@if( $backup )
					<div id="backupwrapper">
						${userneedbackup}
					</div>
					@endif
				@endif
${doc}
${suppdoc}
			</div>
		`);

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
		initDatepicker('#from').on('dp.change dp.update', function(e) {
			$('#form').bootstrapValidator('revalidateField', 'date_time_start');
			var minDaten = $('#from').val();
			$('#to').datetimepicker('minDate', minDaten);

			@if($setHalfDayMC == 1)
			if($('#from').val() === $('#to').val()) {
				if( $('.removehalfleave').length === 0) {

					////////////////////////////////////////////////////////////////////////////////////////
					// checking half day leave
					let [d, itime_start, itime_end] = getHalfdayInfo($('#from'));
					// console.log(d);
					if(d === true) {
						$('#wrapperday').append(`${leave_cat}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

						var daynow = moment($('#from').val(), 'YYYY-MM-DD').format('dddd');
						var datenow = $('#         to').val();

						// convert data1 into json
						var obj = getTimeLeave(datenow);

						var checkedam = '';
						var checkedpm = '';
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
						$('#wrappertest').append(`${toggle_time_checkedam(obj)}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

					} else {
						$('#wrapperday').append(`${leave_cat}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
					}
					////////////////////////////////////////////////////////////////////////////////////////
					// end checking half day leave
				}
			}
			@endif
			if($('#from').val() !== $('#to').val()) {
				$('.removehalfleave').remove();
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
			}

			// for backup person based on from date
			@if( $userneedbackup == 1 )
			// enable backup if date from is greater or equal than today.
			//cari date now dulu
			if( $('#from').val() >= moment().format('YYYY-MM-DD') ) {
				// console.log( moment().add(1, 'days').format('YYYY-MM-DD') );
				// console.log($( '#rembackup').children().length + ' <= rembackup length' );
				if( $('#backupwrapper').children().length == 0 ) {
					$('#backupwrapper').append(`${userneedbackup}`);
					$('#form').bootstrapValidator('addField', $('.backup').find('[name="staff_id"]'));
					initBackupPerson();
				}
			} else {
				$('#form').bootstrapValidator('removeField', $('.backup').find('[name="staff_id"]'));
				$('#backupwrapper').children().remove();
			}
			@endif
		});

		initDatepicker('#to').on('dp.change dp.update', function(e) {
			$('#form').bootstrapValidator('revalidateField', 'date_time_end');
			var maxDate = $('#to').val();
			$('#from').datetimepicker('maxDate', maxDate);

			@if($setHalfDayMC == 1)
			if($('#from').val() === $('#to').val()) {
				if( $('.removehalfleave').length === 0) {

					////////////////////////////////////////////////////////////////////////////////////////
					// checking half day leave
					let [d, itime_start, itime_end] = getHalfdayInfo($('#to'));
					// console.log(d);
					if(d === true) {
						$('#wrapperday').append(`${leave_cat}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

						var daynow = moment($('#from').val(), 'YYYY-MM-DD').format('dddd');
						var datenow = $('#to').val();

						// convert data1 into json
						var obj = getTimeLeave(datenow);

						var checkedam = '';
						var checkedpm = '';
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
						$('#wrappertest').append(`${toggle_time_checkedam(obj)}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

					} else {
						$('#wrapperday').append(`${leave_cat}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
					}
					////////////////////////////////////////////////////////////////////////////////////////
					// end checking half day leave
				}
			}
			@endif
			if($('#from').val() !== $('#to').val()) {
				$('.removehalfleave').remove();
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
			}
		});
		// end date

		/////////////////////////////////////////////////////////////////////////////////////////
		//enable select 2 for backup
		@if( $userneedbackup == 1 )
		initBackupPerson();
		@endif
		/////////////////////////////////////////////////////////////////////////////////////////
		@if($setHalfDayMC == 1)
		// enable radio
		$(document).on('change', '#appendleavehalf :radio', function () {
			if (this.checked) {
				var daynow = moment($('#from').val(), 'YYYY-MM-DD').format('dddd');
				var datenow  =       $(to').val();

				var data1 = $.ajax({
					url: "{{ route('leavedate.timeleave') }}",
					type: "POST",
					data: {
							date: datenow,
							_token: '{!! csrf_token() !!}',
							id: {{ $hrleave->belongstostaff->id }}
					},
					dataType: 'json',
					global: false,
					async:false,
					success: function (response) {
						// you will get response from your php page (what you echo or print)
						return response;
					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.log(textStatus, errorThrown);
					}
				}).responseText;

				// convert data1 into json
				var obj = jQuery.parseJSON( data1 );

				// checking so there is no double
				if( $('.removetest').length == 0 ) {
					$('#wrappertest').append(`${toggle_time_hrleave(obj)}`);
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
	if ($selection.val() == '10') {

		$('#remove').remove();
		$('#wrapper').append(`
			<div id="remove">
			${replacementForm}
			${from}
			${to}
			${wrapperday}
				@if( $userneedbackup == 1 )
				@if( $backup )
					${userneedbackup}
				@endif
				@endif
${doc}
${suppdoc}
			</div>
		`);

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
		$('#form').bootstrapValidator('addField', $('.supportdoc').find('[name="document"]'));
		$('#form').bootstrapValidator('addField', $('.suppdoc').find('[name="documentsupport"]'));

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable select2
		$('#nrla').select2({ placeholder: 'Please select', 	width: '100%',
		});

		/////////////////////////////////////////////////////////////////////////////////////////
		//enable select 2 for backup
		initBackupPerson();

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable datetime for the 1st one
		initDatepicker('#from').on('dp.change dp.update', function(e) {
			$('#form').bootstrapValidator('revalidateField', 'date_time_start');
			var minDaten = $('#from').val();
			$('#to').datetimepicker('minDate', minDaten);

			if($('#from').val() === $('#to').val()) {
				if( $('.removehalfleave').length === 0) {

					////////////////////////////////////////////////////////////////////////////////////////
					// checking half day leave
					let [d, itime_start, itime_end] = getHalfdayInfo($('#from'));
					// console.log(d);
					if(d === true) {
						$('#wrapperday').append(`${leave_cat}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

						var daynow = moment($('#from').val(), 'YYYY-MM-DD').format('dddd');
						var datenow = $('#from').val();

						// convert data1 into json
						var obj = getTimeLeave(datenow);

						var checkedam = '';
						var checkedpm = '';
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
						$('#wrappertest').append(`${toggle_time_checkedam(obj)}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

					} else {
						$('#wrapperday').append(`${toggle_time_hrleave(obj)}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
					}
					////////////////////////////////////////////////////////////////////////////////////////
					// end checking half day leave
				}
			}
			if($('#from').val() !== $('#to').val()) {
				$('.removehalfleave').remove();
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
			}

			@if( $userneedbackup == 1 )
			// enable backup if date from is greater or equal than today.
			//cari date now dulu
			if( $('#from').val() >= moment().format('YYYY-MM-DD') ) {
				// console.log( moment().add(1, 'days').format('YYYY-MM-DD') );
				// console.log($( '#rembackup').children().length + ' <= rembackup length' );
				if( $('#backupwrapper').children().length == 0 ) {
					$('#backupwrapper').append(`${userneedbackup}`);
					$('#form').bootstrapValidator('addField', $('.backup').find('[name="staff_id"]'));
					initBackupPerson();
				}
			} else {
				$('#form').bootstrapValidator('removeField', $('.backup').find('[name="staff_id"]'));
				$('#backupwrapper').children().remove();
			}
			@endif
		});

		initDatepicker('#to').on('dp.change dp.update', function(e) {
			$('#form').bootstrapValidator('revalidateField', 'date_time_end');
			var maxDate = $('#to').val();
			$('#from').datetimepicker('maxDate', maxDate);

			if($('#from').val() === $('#to').val()) {
				if( $('.removehalfleave').length === 0) {

					////////////////////////////////////////////////////////////////////////////////////////
					// checking half day leave
					let [d, itime_start, itime_end] = getHalfdayInfo($('#to'));
					// console.log(d);
					if(d === true) {
						$('#wrapperday').append(`${leave_cat}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

						var daynow = moment($('#from').val(), 'YYYY-MM-DD').format('dddd');
						var datenow = $('#to').val();

						// convert data1 into json
						var obj = getTimeLeave(datenow);

						var checkedam = '';
						var checkedpm = '';
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
						$('#wrappertest').append(`${toggle_time_checkedam(obj)}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
					} else {
						$('#wrapperday').append(`${leave_cat}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
					}
					////////////////////////////////////////////////////////////////////////////////////////
					// end checking half day leave
				}
			}
			if($('#from').val() !== $('#to').val()) {
				$('.removehalfleave').remove();
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
			}
		});
		// end date

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable radio
		$(document).on('change', '#appendleavehalf :radio', function () {
			if (this.checked) {
				var daynow = moment($('#from').val(), 'YYYY-MM-DD').format('dddd');
				var datenow = $('#from').val();

				// convert data1 into json
				var obj = getTimeLeave(datenow);

				// checking so there is no double
				if( $('.removetest').length == 0 ) {
					$('#wrappertest').append(`${toggle_time_hrleave(obj)}`);
				}
			}
		});

		$(document).on('change', '#removeleavehalf :radio', function () {
		// $('#removeleavehalf :radio').change(function() {
			if (this.checked) {
				console.log( $('#nrla option:selected').data('nrlbalance') );
				if( $('#nrla option:selected').data('nrlbalance') == 0.5 ) {

					// especially for select 2, if no select2, remove change()
					$('#nrla option:selected').prop('selected', false).change();
					// $('#nrla').val('').change();
				}
				$('.removetest').remove();
			}
		});

		/////////////////////////////////////////////////////////////////////////////////////////
		// checking for half day click but select for 1 full day
		$('#nrla').change(function() {
			selectedOption = $('option:selected', this);
			$('#form').bootstrapValidator('revalidateField', 'leave_id');
			var nrlbal = selectedOption.data('nrlbalance');
			if (nrlbal == 0.5) {
				// make sure from and to date got value
				$('#from').val(moment().add(3, 'days').format('YYYY-MM-DD'));
				$('#to').val(moment().add(3, 'days').format('YYYY-MM-DD'));

				$('#radio2').prop('checked', true);
				// checking so there is no double

				var daynow = moment($('#from').val(), 'YYYY-MM-DD').format('dddd');
				var datenow = $('#from').val();

				// convert data1 into json
				var obj = getTimeLeave(datenow);

				// checking so there is no double
				if( $('.removetest').length == 0 ) {
					$('#wrappertest').append(`
	<div class="form-check form-check-inline removetest">
		<input type="radio" name="half_type_id" value="1/${obj.time_start_am}/${obj.time_end_am}" id="am" class="form-check-input @error('half_type_id') is-invalid @enderror" ${toggle_time_start_am} ${checkedam} {{ ($hrleave->half_type_id == 1)?'checked=checked':NULL }}>
		<label for="am" class="form-check-label m-2 my-auto">
			${moment(obj.time_start_am, 'HH:mm:ss').format('h:mm a')} to ${moment(obj.time_end_am, 'HH:mm:ss').format('h:mm a')}
		</label>
	</div>
	<div class="form-check form-check-inline removetest">
		<input type="radio" name="half_type_id" value="2/${obj.time_start_pm}/${obj.time_end_pm}" id="pm" class="form-check-input @error('half_type_id') is-invalid @enderror" ${toggle_time_start_pm} ${checkedpm} {{ ($hrleave->half_type_id == 2)?'checked=checked':NULL }}>
		<label for="pm" class="form-check-label m-2 my-auto">
			${moment(obj.time_start_pm, 'HH:mm:ss').format('h:mm a')} to ${moment(obj.time_end_pm, 'HH:mm:ss').format('h:mm a')}
		</label>
	</div>
					`);
				}
			} else {
				if( nrlbal != 0.5 ) {
					$('#radio1').prop('checked', true);
					$('.removetest').remove();
				}
			}
		});
	}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// S-UPL
	if ($selection.val() == '12') {

		$('#remove').remove();
		$('#wrapper').append(`
			<div id="remove">
				${from}
				${to}
				${wrapperday}
				@if( $userneedbackup == 1 )
				@if( $backup )
					${userneedbackup}
				@endif
				@endif
${doc}
${suppdoc}
			</div>
			`);
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
		initBackupPerson();

		/////////////////////////////////////////////////////////////////////////////////////////
		// start date
		initDatepicker('#from').on('dp.change dp.update', function(e) {
			$('#form').bootstrapValidator('revalidateField', 'date_time_start');
			var minDaten = $('#from').val();
			$('#to').datetimepicker('minDate', minDaten);

			if($('#from').val() === $('#to').val()) {
				if( $('.removehalfleave').length === 0) {

					////////////////////////////////////////////////////////////////////////////////////////
					// checking half day leave
					let [d, itime_start, itime_end] = getHalfdayInfo($('#from'));
					// console.log(d);
					if(d === true) {
						$('#wrapperday').append(`${leave_cat}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

						var daynow = moment($('#from').val(), 'YYYY-MM-DD').format('dddd');
						var datenow = $('#from').val();

						// convert data1 into json
						var obj = getTimeLeave(datenow);

						var checkedam = '';
						var checkedpm = '';
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
						$('#wrappertest').append(`${toggle_time_checkedam(obj)}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

					} else {
						$('#wrapperday').append(`${leave_cat}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
					}
					////////////////////////////////////////////////////////////////////////////////////////
					// end checking half day leave
				}
			}
			if($('#from').val() !== $('#to').val()) {
				$('.removehalfleave').remove();
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
			}

			@if( $userneedbackup == 1 )
			// enable backup if date from is greater or equal than today.
			//cari date now dulu
			if( $('#from').val() >= moment().format('YYYY-MM-DD') ) {
				// console.log( moment().add(1, 'days').format('YYYY-MM-DD') );
				// console.log($( '#rembackup').children().length + ' <= rembackup length' );
				if( $('#backupwrapper').children().length == 0 ) {
					$('#backupwrapper').append(`${userneedbackup}`);
					$('#form').bootstrapValidator('addField', $('.backup').find('[name="staff_id"]'));
					initBackupPerson();
				}
			} else {
				$('#form').bootstrapValidator('removeField', $('.backup').find('[name="staff_id"]'));
				$('#backupwrapper').children().remove();
			}
			@endif
		});

		initDatepicker('#to').on('dp.change dp.update', function(e) {
			$('#form').bootstrapValidator('revalidateField', 'date_time_end');
			var maxDate = $('#to').val();
			$('#from').datetimepicker('maxDate', maxDate);

			if($('#from').val() === $('#to').val()) {
				if( $('.removehalfleave').length === 0) {

					////////////////////////////////////////////////////////////////////////////////////////
					// checking half day leave
					let [d, itime_start, itime_end] = getHalfdayInfo($('#to'));
					// console.log(d);
					if(d === true) {
						$('#wrapperday').append(`${leave_cat}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

						var daynow = moment($('#from').val(), 'YYYY-MM-DD').format('dddd');
						var datenow = $('#to').val();

						// convert data1 into json
						var obj = getTimeLeave(datenow);

						var checkedam = '';
						var checkedpm = '';
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
						$('#wrappertest').append(`${toggle_time_checkedam(obj)}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
					} else {
						$('#wrapperday').append(`${leave_cat}`);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
					}
					////////////////////////////////////////////////////////////////////////////////////////
					// end checking half day leave
				}
			}
			if($('#from').val() !== $('#to').val()) {
				$('.removehalfleave').remove();
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
			}
		});
		// end date

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable radio
		$(document).on('change', '#appendleavehalf :radio', function () {
			if (this.checked) {
				var daynow = moment($('#from').val(), 'YYYY-MM-DD').format('dddd');
				var datenow = $('#from').val();

				var data1 = $.ajax({
					url: "{{ route('leavedate.timeleave') }}",
					type: "POST",
					data: {
							date: datenow,
							_token: '{!! csrf_token() !!}',
							id: {{ $hrleave->belongstostaff->id }}
					},
					dataType: 'json',
					global: false,
					async:false,
					success: function (response) {
						// you will get response from your php page (what you echo or print)
						return response;
					},
					error: function(jqXHR, textStatus, errorThrown) {
						console.log(textStatus, errorThrown);
					}
				}).responseText;

				// convert data1 into json
				var obj = jQuery.parseJSON( data1 );

				// checking so there is no double
				if( $('.removetest').length == 0 ) {
					$('#wrappertest').append(`${toggle_time_hrleave(obj)}`);
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
			id: {
				validators: {
					notEmpty: {
						message: 'Please select',
					},
				}
			},
			leave_cat: {
				validators: {
					notEmpty: {
						message: 'Please select leave category',
					},
				}
			},
			staff_id: {
				validators: {
					// notEmpty: {
					// 	message: 'Please choose'
					// }
				}
			},
			amend_note: {
				validators: {
					notEmpty: {
						message: 'Please insert note'
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
			// documentsupport: {
			// 	validators: {
			// 		notEmpty: {
			// 			message: 'Please click this as an aknowledgement.'
			// 		},
			// 	}
			// },
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

@section('nonjquery')
	function printPage() {
		window.print();
	}

	function back() {
		window.history.back();
	}

@endsection
