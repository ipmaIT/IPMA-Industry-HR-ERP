@extends('layouts.app')

@section('content')
<?php
use \App\Models\HumanResources\HRLeave;
use \App\Models\Staff;
use \Illuminate\Database\Eloquent\Builder;

$day_type = App\Models\HumanResources\OptDayType::pluck('daytype', 'id')->sortKeys()->toArray();


$tcms = App\Models\HumanResources\OptTcms::pluck('leave_short', 'id')->sortKeys()->toArray();

// $staff = $attendance->belongstostaff;
$staff = Staff::find($attendance->staff_id);
$login = $staff->hasmanylogin()->where('active', '1')->get()->first();

if ($attendance->time_work_hour != NULL || $attendance->time_work_hour != '') {
	$time_work_hour = $attendance->time_work_hour;
} else {
	$time_work_hour = '00:00';
}

$dayName = \Carbon\Carbon::parse($attendance->attend_date)->format('l');


if ($staff->belongstomanydepartment()->wherePivot('main', 1)->first()->id == 19 || $staff->belongstomanydepartment()->wherePivot('main', 1)->first()->id == 30) {		// maintenance staff
	if ($dayName == 'Friday') {
		$working_hour = $staff->belongstomanydepartment()->wherePivot('main', 1)->first()->belongstowhgroup()->where('effective_date_start', '<=', $attendance->attend_date)->where('effective_date_end', '>=', $attendance->attend_date)->where('category', 7)->first();
	} else {
		$working_hour = $staff->belongstomanydepartment()->wherePivot('main', 1)->first()->belongstowhgroup()->where('effective_date_start', '<=', $attendance->attend_date)->where('effective_date_end', '>=', $attendance->attend_date)->where('category', 8)->first();
	}
} else {																																								// non-maintenance staff
	if ($dayName == 'Friday') {
		$working_hour = $staff->belongstomanydepartment()->wherePivot('main', 1)->first()->belongstowhgroup()->where('effective_date_start', '<=', $attendance->attend_date)->where('effective_date_end', '>=', $attendance->attend_date)->where('category', 3)->first();
	} else {
		$working_hour = $staff->belongstomanydepartment()->wherePivot('main', 1)->first()->belongstowhgroup()->where('effective_date_start', '<=', $attendance->attend_date)->where('effective_date_end', '>=', $attendance->attend_date)->where('category', '!=', 3)->first();
	}
}
// dd($working_hour, $staff->belongstomanydepartment()->wherePivot('main', 1)->first()->belongstowhgroup());

$time_start_am = \Carbon\Carbon::parse($working_hour->time_start_am)->format('H:i');
$time_end_am = \Carbon\Carbon::parse($working_hour->time_end_am)->format('H:i');
$time_start_pm = \Carbon\Carbon::parse($working_hour->time_start_pm)->format('H:i');
$time_end_pm = \Carbon\Carbon::parse($working_hour->time_end_pm)->format('H:i');

// $leave = App\Models\HumanResources\HRLeave::join('option_leave_types', 'hr_leaves.leave_type_id', '=', 'option_leave_types.id')->where('date_time_start', '<=', $attendance->attend_date)->where('date_time_end', '>=', $attendance->attend_date)->where('staff_id', '=', $staff->id)->pluck('option_leave_types.leave_type_code', 'hr_leaves.id')->sortKeys()->toArray();
$leaves = HRLeave::where('staff_id', $attendance->belongstostaff->id)
->whereDate('date_time_start', '<=', $attendance->attend_date)
->whereDate('date_time_end', '>=', $attendance->attend_date)
->where(function (Builder $query){
	$query->whereIn('leave_status_id', [5,6])
	->orWhereNull('leave_status_id');
})
->orderBy('date_time_start', 'DESC')
->get();
			// ->ddRawSql();
			// ->pluck('HR9-'.str_pad('leave_no',5,'0',STR_PAD_LEFT).'/'.'leave_year', 'id');
// dd($leave);
if ($leaves->count()) {
	foreach ($leaves as $lv) {
		$leave = [$lv->id => 'HR9-'.str_pad($lv->leave_no,5,'0',STR_PAD_LEFT).'/'.$lv->leave_year];
	}
} else {
	$leave = [];
}

?>

<div class="container row align-items-start justify-content-center">
	@include('humanresources.hrdept.navhr')
	<div class="d-flex justify-content-center align-items-start">
		<div class="col-md-7">

			<form method="POST" action="{{ route('attendance.update', $attendance->id) }}" accept-charset="UTF-8" id="form" autocomplete="off" class="" enctype="multipart/form-data">
				@csrf
				@method('PATCH')
				<input type="hidden" name="staff_id" value="<?php echo $staff->id; ?>">

				<h5>Attendance Edit</h5>

				<div class="row mt-3"></div>

				<div class="row mt-2">
					<div class="col-md-3">
						<label for="id" class="col-form-label">ID : </label>
					</div>
					<div class="col-md-9">
						<input type="text" name="id" value="{{ $login->username }}" id="id" class="form-control form-control-sm" readonly>
					</div>
				</div>

				<div class="row mt-2">
					<div class="col-md-3">
						<label for="name" class="col-form-label">Name : </label>
					</div>
					<div class="col-md-9">
						<input type="text" name="name" value="{{ $staff->name }}" id="name" class="form-control form-control-sm" readonly>
					</div>
				</div>

				<div class="row mt-2">
					<div class="col-md-3">
						<label for="date" class="col-form-label">Date : </label>
					</div>
					<div class="col-md-9">
						<input type="text" name="attend_date" value="{{ old('attend_date', $attendance->attend_date) }}" id="date" class="form-control form-control-sm @error('attend_date') is-invalid @enderror" readonly>
					</div>
				</div>

				<div class="row mt-2">
					<div class="col-md-3">
						<label for="daytype_id" class="col-form-label">Day Type : </label>
					</div>
					<div class="col-md-9 {{ $errors->has('daytype_id') ? 'has-error' : '' }}">
						<select name="daytype_id" id="daytype_id" class="form-select form-select-sm @error('daytype_id') is-invalid @enderror">
							<option value="">Please choose</option>
							@foreach($day_type as $k => $v)
							<option value="{{ $k }}" {{ ($k == $attendance->daytype_id)?'selected':NULL }}>{{ $v }}</option>
							@endforeach
						</select>
					</div>
				</div>

				<div class="row mt-2">
					<div class="col-md-3">
						<label for="attendance_type_id" class="col-form-label">Cause : </label>
					</div>
					<div class="col-md-9 {{ $errors->has('attendance_type_id') ? 'has-error' : '' }}">
						<select name="attendance_type_id" id="attendance_type_id" class="form-select form-select-sm @error('attendance_type_id') is-invalid @enderror">
							<option value="">Please choose</option>
							@foreach($tcms as $k => $v)
							<option value="{{ $k }}" {{ ($k == $attendance->attendance_type_id)?'selected':NULL }}>{{ $v }}</option>
							@endforeach
						</select>
					</div>
				</div>

				<div class="row mt-2">
					<div class="col-md-3">
						<label for="leave_id" class="col-form-label">Leave : </label>
					</div>
					<div class="col-md-9 {{ $errors->has('leave_id') ? 'has-error' : '' }}">
						<select name="leave_id" id="leave_id" class="form-select form-select-sm @error('leave_id') is-invalid @enderror">
							<option value="">Please choose</option>
							@foreach($leave as $k => $v)
							<option value="{{ $k }}" {{ ($k == $attendance->leave_id)?'selected':NULL }}>{{ $v }}</option>
							@endforeach
						</select>
					</div>
				</div>

				<div class="row mt-2">
					<div class="col-md-3">
						<label for="in" class="col-form-label">In : </label>
					</div>
					<div class="col-md-9 {{ $errors->has('in') ? 'has-error' : '' }}">
						<input type="text" name="in" value="{{ old('in', $attendance->in) }}" id="in" class="form-control form-control-sm @error('in') is-invalid @enderror" placeholder="In">
					</div>
				</div>

				<div class="row mt-2">
					<div class="col-md-3">
						<label for="break" class="col-form-label">Break : </label>
					</div>
					<div class="col-md-9 {{ $errors->has('break') ? 'has-error' : '' }}">
						<input type="text" name="break" value="{{ old('break', $attendance->break) }}" id="break" class="form-control form-control-sm @error('break') is-invalid @enderror" placeholder="Break">
					</div>
				</div>

				<div class="row mt-2">
					<div class="col-md-3">
						<label for="from1" class="col-form-label">Resume : </label>
					</div>
					<div class="col-md-9 {{ $errors->has('resume') ? 'has-error' : '' }}">
						<input type="text" name="resume" value="{{ old('resume', $attendance->resume) }}" id="resume" class="form-control form-control-sm @error('resume') is-invalid @enderror" placeholder="Resume">
					</div>
				</div>

				<div class="row mt-2">
					<div class="col-md-3">
						<label for="out" class="col-form-label">Out : </label>
					</div>
					<div class="col-md-9 {{ $errors->has('out') ? 'has-error' : '' }}">
						<input type="text" name="out" value="{{ old('out', $attendance->out) }}" id="out" class="form-control form-control-sm @error('out') is-invalid @enderror" placeholder="Out">
					</div>
				</div>

				<div class="row mt-2">
					<div class="col-md-3">
						<label for="time_work_hour" class="col-form-label">Duration : </label>
					</div>
					<div class="col-md-9 {{ $errors->has('time_work_hour') ? 'has-error' : '' }}">
						<input type="text" name="time_work_hour" value="{{ old('time_work_hour', $attendance->time_work_hour) }}" id="time_work_hour" class="form-control form-control-sm @error('time_work_hour') is-invalid @enderror" placeholder="Duration">
					</div>
				</div>

				<div class="row mt-2">
					<div class="col-md-3">
						<label for="remarks" class="col-form-label">Remarks : </label>
					</div>
					<div class="col-md-9 {{ $errors->has('remark') ? 'has-error' : '' }}">
						<input type="text" name="remarks" value="{{ old('remarks', $attendance->remarks) }}" id="remarks" class="form-control form-control-sm @error('remarks') is-invalid @enderror" placeholder="Remarks">
					</div>
				</div>

				<div class="row mt-2">
					<div class="col-md-3">
						<label for="hr_remarks" class="col-form-label">HR Remarks : </label>
					</div>
					<div class="col-md-9 {{ $errors->has('hr_remark') ? 'has-error' : '' }}">
						<input type="text" name="hr_remarks" value="{{ old('hr_remarks', $attendance->hr_remarks) }}" id="hr_remarks" class="form-control form-control-sm @error('hr_remarks') is-invalid @enderror" placeholder="HR Remarks">
					</div>
				</div>

				<div class="row mt-2">
					<div class="col-md-3">
						&nbsp;
					</div>
					<div class="col-md-9 form-check {{ $errors->has('exception') ? 'has-error' : '' }}">
						<input type="checkbox" name="exception" value="1" id="exception" class="form-check-input @error('exception') is-invalid @enderror" {{ ($attendance->exception)?'checked':NULL }}>
						<label for="exception" class="form-check-label">Exception</label>
					</div>
				</div>

				<div class="row mt-4">
					<div class="text-center">
						<button type="submit" class="btn btn-sm btn-outline-secondary">Update</button>
					</div>
				</div>

			</form>

			<div class="row mt-4 text-center">
				<a href="{{ url()->previous() }}">
					<button class="btn btn-sm btn-outline-secondary">Back</button>
				</a>
			</div>

		</div>
	</div>
</div>
@endsection


@section('js')
/////////////////////////////////////////////////////////////////////////////////////////
// DATE PICKER IN
$('#in').datetimepicker({
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
	format: 'HH:mm',
	useCurrent: false,
})
.on('dp.change dp.update', function(e) {

	var breakStr = {!! json_encode($time_end_am) !!};
	var breakEnd = {!! json_encode($time_start_pm) !!};

	if ($('#in').val() > {!! json_encode($time_start_am) !!}) {
		var inTime = $('#in').val();
	} else if ($('#in').val() == '00:00') {
		var inTime = '00:00';
	} else {
		var inTime = {!! json_encode($time_start_am) !!};
	}

	if ($('#break').val() < {!! json_encode($time_end_am) !!}) {
		var breakTime = $('#break').val();
	} else if ($('#break').val() == '00:00') {
		var breakTime = '00:00';
	} else {
		var breakTime = {!! json_encode($time_end_am) !!};
	}

	if ($('#resume').val() > {!! json_encode($time_start_pm) !!}) {
		var resumeTime = $('#resume').val();
	} else if ($('#resume').val() == '00:00') {
		var resumeTime = '00:00';
	} else {
		var resumeTime = {!! json_encode($time_start_pm) !!};
	}

	if ($('#out').val() < {!! json_encode($time_end_pm) !!}) {
		var outTime = $('#out').val();
	} else if ($('#out').val() == '00:00') {
		var outTime = '00:00';
	} else {
		var outTime = {!! json_encode($time_end_pm) !!};
	}

	// Validate input format (HH:mm)
	var timeRegex = /^([01]\d|2[0-3]):([0-5]\d)$/;

	if (timeRegex.test(inTime) && timeRegex.test(breakTime) && timeRegex.test(resumeTime) && timeRegex.test(outTime)) {

		if (inTime != '00:00' && breakTime != '00:00' && outTime == '00:00') {
			var startTimeStr = inTime;
			var endTimeStr = breakTime;

			// TEA BREAK
			if (startTimeStr > '10:15') {
				var teaTime = '00:00';
			} else {
				var teaTime = '00:15';
			}

			// LUNCH BREAK
			breakTimeDuration = '00:00';

		} else if (inTime != '00:00' && outTime != '00:00') {
			var startTimeStr = inTime;
			var endTimeStr = outTime;

			// TEA BREAK
			if (startTimeStr > '10:15') {
				var teaTime = '00:00';
			} else {
				var teaTime = '00:15';
			}

			// LUNCH BREAK
			var lunchStr = moment(`${breakStr}`, 'HH:mm');
			var lunchEnd = moment(`${breakEnd}`, 'HH:mm');

			var duration_break = moment.duration(lunchEnd.diff(lunchStr));

			var hours_break = duration_break.hours();
			var minutes_break = duration_break.minutes();

			var breakTimeDuration = `${hours_break.toString().padStart(2, '0')}:${minutes_break.toString().padStart(2, '0')}`;

		} else if (inTime == '00:00' && resumeTime != '00:00' && outTime != '00:00') {
			var startTimeStr = resumeTime;
			var endTimeStr = outTime;

			// TEA BREAK
			if (startTimeStr > '10:15') {
				var teaTime = '00:00';
			} else {
				var teaTime = '00:15';
			}

			// LUNCH BREAK
			breakTimeDuration = '00:00';
		}

		var startTime = moment(`${startTimeStr}`, 'HH:mm');
		var endTime = moment(`${endTimeStr}`, 'HH:mm');

		var duration = moment.duration(endTime.diff(startTime));

		var hours = duration.hours();
		var minutes = duration.minutes();

		var formattedDuration = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;

		var filter1 = moment(`${formattedDuration}`, 'HH:mm').subtract(`${teaTime}`, 'HH:mm');
		var Duration1 = filter1.format('HH:mm')

		var filter2 = moment(`${Duration1}`, 'HH:mm').subtract(`${breakTimeDuration}`, 'HH:mm');
		var Duration2 = filter2.format('HH:mm')

		var inputElement = document.getElementById('time_work_hour');
		inputElement.value = Duration2;
	} else {
		var inputElement = document.getElementById('time_work_hour');
		inputElement.value = 'Invalid Time Format';
	}
});


// DATE PICKER BREAK
$('#break').datetimepicker({
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
	format: 'HH:mm',
	useCurrent: false,
})
.on('dp.change dp.update', function(e) {

	var breakStr = {!! json_encode($time_end_am) !!};
	var breakEnd = {!! json_encode($time_start_pm) !!};

	if ($('#in').val() > {!! json_encode($time_start_am) !!}) {
		var inTime = $('#in').val();
	} else if ($('#in').val() == '00:00') {
		var inTime = '00:00';
	} else {
		var inTime = {!! json_encode($time_start_am) !!};
	}

	if ($('#break').val() < {!! json_encode($time_end_am) !!}) {
		var breakTime = $('#break').val();
	} else if ($('#break').val() == '00:00') {
		var breakTime = '00:00';
	} else {
		var breakTime = {!! json_encode($time_end_am) !!};
	}

	if ($('#resume').val() > {!! json_encode($time_start_pm) !!}) {
		var resumeTime = $('#resume').val();
	} else if ($('#resume').val() == '00:00') {
		var resumeTime = '00:00';
	} else {
		var resumeTime = {!! json_encode($time_start_pm) !!};
	}

	if ($('#out').val() < {!! json_encode($time_end_pm) !!}) {
		var outTime = $('#out').val();
	} else if ($('#out').val() == '00:00') {
		var outTime = '00:00';
	} else {
		var outTime = {!! json_encode($time_end_pm) !!};
	}

	// Validate input format (HH:mm)
	var timeRegex = /^([01]\d|2[0-3]):([0-5]\d)$/;

	if (timeRegex.test(inTime) && timeRegex.test(breakTime) && timeRegex.test(resumeTime) && timeRegex.test(outTime)) {

		if (inTime != '00:00' && breakTime != '00:00' && outTime == '00:00') {
			var startTimeStr = inTime;
			var endTimeStr = breakTime;

			// TEA BREAK
			if (startTimeStr > '10:15') {
				var teaTime = '00:00';
			} else {
				var teaTime = '00:15';
			}

			// LUNCH BREAK
			breakTimeDuration = '00:00';

		} else if (inTime != '00:00' && outTime != '00:00') {
			var startTimeStr = inTime;
			var endTimeStr = outTime;

			// TEA BREAK
			if (startTimeStr > '10:15') {
				var teaTime = '00:00';
			} else {
				var teaTime = '00:15';
			}

			// LUNCH BREAK
			var lunchStr = moment(`${breakStr}`, 'HH:mm');
			var lunchEnd = moment(`${breakEnd}`, 'HH:mm');

			var duration_break = moment.duration(lunchEnd.diff(lunchStr));

			var hours_break = duration_break.hours();
			var minutes_break = duration_break.minutes();

			var breakTimeDuration = `${hours_break.toString().padStart(2, '0')}:${minutes_break.toString().padStart(2, '0')}`;

		} else if (inTime == '00:00' && resumeTime != '00:00' && outTime != '00:00') {
			var startTimeStr = resumeTime;
			var endTimeStr = outTime;

			// TEA BREAK
			if (startTimeStr > '10:15') {
				var teaTime = '00:00';
			} else {
				var teaTime = '00:15';
			}

			// LUNCH BREAK
			breakTimeDuration = '00:00';
		}

		var startTime = moment(`${startTimeStr}`, 'HH:mm');
		var endTime = moment(`${endTimeStr}`, 'HH:mm');

		var duration = moment.duration(endTime.diff(startTime));

		var hours = duration.hours();
		var minutes = duration.minutes();

		var formattedDuration = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;

		var filter1 = moment(`${formattedDuration}`, 'HH:mm').subtract(`${teaTime}`, 'HH:mm');
		var Duration1 = filter1.format('HH:mm')

		var filter2 = moment(`${Duration1}`, 'HH:mm').subtract(`${breakTimeDuration}`, 'HH:mm');
		var Duration2 = filter2.format('HH:mm')

		var inputElement = document.getElementById('time_work_hour');
		inputElement.value = Duration2;
	} else {
		var inputElement = document.getElementById('time_work_hour');
		inputElement.value = 'Invalid Time Format';
	}
});


// DATE PICKER RESUME
$('#resume').datetimepicker({
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
	format: 'HH:mm',
	useCurrent: false,
})
.on('dp.change dp.update', function(e) {

	var breakStr = {!! json_encode($time_end_am) !!};
	var breakEnd = {!! json_encode($time_start_pm) !!};

	if ($('#in').val() > {!! json_encode($time_start_am) !!}) {
		var inTime = $('#in').val();
	} else if ($('#in').val() == '00:00') {
		var inTime = '00:00';
	} else {
		var inTime = {!! json_encode($time_start_am) !!};
	}

	if ($('#break').val() < {!! json_encode($time_end_am) !!}) {
		var breakTime = $('#break').val();
	} else if ($('#break').val() == '00:00') {
		var breakTime = '00:00';
	} else {
		var breakTime = {!! json_encode($time_end_am) !!};
	}

	if ($('#resume').val() > {!! json_encode($time_start_pm) !!}) {
		var resumeTime = $('#resume').val();
	} else if ($('#resume').val() == '00:00') {
		var resumeTime = '00:00';
	} else {
		var resumeTime = {!! json_encode($time_start_pm) !!};
	}

	if ($('#out').val() < {!! json_encode($time_end_pm) !!}) {
		var outTime = $('#out').val();
	} else if ($('#out').val() == '00:00') {
		var outTime = '00:00';
	} else {
		var outTime = {!! json_encode($time_end_pm) !!};
	}

	// Validate input format (HH:mm)
	var timeRegex = /^([01]\d|2[0-3]):([0-5]\d)$/;

	if (timeRegex.test(inTime) && timeRegex.test(breakTime) && timeRegex.test(resumeTime) && timeRegex.test(outTime)) {

		if (inTime != '00:00' && breakTime != '00:00' && outTime == '00:00') {
			var startTimeStr = inTime;
			var endTimeStr = breakTime;

			// TEA BREAK
			if (startTimeStr > '10:15') {
				var teaTime = '00:00';
			} else {
				var teaTime = '00:15';
			}

			// LUNCH BREAK
			breakTimeDuration = '00:00';

		} else if (inTime != '00:00' && outTime != '00:00') {
			var startTimeStr = inTime;
			var endTimeStr = outTime;

			// TEA BREAK
			if (startTimeStr > '10:15') {
				var teaTime = '00:00';
			} else {
				var teaTime = '00:15';
			}

			// LUNCH BREAK
			var lunchStr = moment(`${breakStr}`, 'HH:mm');
			var lunchEnd = moment(`${breakEnd}`, 'HH:mm');

			var duration_break = moment.duration(lunchEnd.diff(lunchStr));

			var hours_break = duration_break.hours();
			var minutes_break = duration_break.minutes();

			var breakTimeDuration = `${hours_break.toString().padStart(2, '0')}:${minutes_break.toString().padStart(2, '0')}`;

		} else if (inTime == '00:00' && resumeTime != '00:00' && outTime != '00:00') {
			var startTimeStr = resumeTime;
			var endTimeStr = outTime;

			// TEA BREAK
			if (startTimeStr > '10:15') {
				var teaTime = '00:00';
			} else {
				var teaTime = '00:15';
			}

			// LUNCH BREAK
			breakTimeDuration = '00:00';
		}

		var startTime = moment(`${startTimeStr}`, 'HH:mm');
		var endTime = moment(`${endTimeStr}`, 'HH:mm');

		var duration = moment.duration(endTime.diff(startTime));

		var hours = duration.hours();
		var minutes = duration.minutes();

		var formattedDuration = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;

		var filter1 = moment(`${formattedDuration}`, 'HH:mm').subtract(`${teaTime}`, 'HH:mm');
		var Duration1 = filter1.format('HH:mm')

		var filter2 = moment(`${Duration1}`, 'HH:mm').subtract(`${breakTimeDuration}`, 'HH:mm');
		var Duration2 = filter2.format('HH:mm')

		var inputElement = document.getElementById('time_work_hour');
		inputElement.value = Duration2;
	} else {
		var inputElement = document.getElementById('time_work_hour');
		inputElement.value = 'Invalid Time Format';
	}
});


// DATE PICKER OUT
$('#out').datetimepicker({
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
	format: 'HH:mm',
	useCurrent: false,
})
.on('dp.change dp.update', function(e) {

	var breakStr = {!! json_encode($time_end_am) !!};
	var breakEnd = {!! json_encode($time_start_pm) !!};

	if ($('#in').val() > {!! json_encode($time_start_am) !!}) {
		var inTime = $('#in').val();
	} else if ($('#in').val() == '00:00') {
		var inTime = '00:00';
	} else {
		var inTime = {!! json_encode($time_start_am) !!};
	}

	if ($('#break').val() < {!! json_encode($time_end_am) !!}) {
		var breakTime = $('#break').val();
	} else if ($('#break').val() == '00:00') {
		var breakTime = '00:00';
	} else {
		var breakTime = {!! json_encode($time_end_am) !!};
	}

	if ($('#resume').val() > {!! json_encode($time_start_pm) !!}) {
		var resumeTime = $('#resume').val();
	} else if ($('#resume').val() == '00:00') {
		var resumeTime = '00:00';
	} else {
		var resumeTime = {!! json_encode($time_start_pm) !!};
	}

	if ($('#out').val() < {!! json_encode($time_end_pm) !!}) {
		var outTime = $('#out').val();
	} else if ($('#out').val() == '00:00') {
		var outTime = '00:00';
	} else {
		var outTime = {!! json_encode($time_end_pm) !!};
	}

	// Validate input format (HH:mm)
	var timeRegex = /^([01]\d|2[0-3]):([0-5]\d)$/;

	if (timeRegex.test(inTime) && timeRegex.test(breakTime) && timeRegex.test(resumeTime) && timeRegex.test(outTime)) {

		if (inTime != '00:00' && breakTime != '00:00' && outTime == '00:00') {
			var startTimeStr = inTime;
			var endTimeStr = breakTime;

			// TEA BREAK
			if (startTimeStr > '10:15') {
				var teaTime = '00:00';
			} else {
				var teaTime = '00:15';
			}

			// LUNCH BREAK
			breakTimeDuration = '00:00';

		} else if (inTime != '00:00' && outTime != '00:00') {
			var startTimeStr = inTime;
			var endTimeStr = outTime;

			// TEA BREAK
			if (startTimeStr > '10:15') {
				var teaTime = '00:00';
			} else {
				var teaTime = '00:15';
			}

			// LUNCH BREAK
			var lunchStr = moment(`${breakStr}`, 'HH:mm');
			var lunchEnd = moment(`${breakEnd}`, 'HH:mm');

			var duration_break = moment.duration(lunchEnd.diff(lunchStr));

			var hours_break = duration_break.hours();
			var minutes_break = duration_break.minutes();

			var breakTimeDuration = `${hours_break.toString().padStart(2, '0')}:${minutes_break.toString().padStart(2, '0')}`;

		} else if (inTime == '00:00' && resumeTime != '00:00' && outTime != '00:00') {
			var startTimeStr = resumeTime;
			var endTimeStr = outTime;

			// TEA BREAK
			if (startTimeStr > '10:15') {
				var teaTime = '00:00';
			} else {
				var teaTime = '00:15';
			}

			// LUNCH BREAK
			breakTimeDuration = '00:00';
		}

		var startTime = moment(`${startTimeStr}`, 'HH:mm');
		var endTime = moment(`${endTimeStr}`, 'HH:mm');

		var duration = moment.duration(endTime.diff(startTime));

		var hours = duration.hours();
		var minutes = duration.minutes();

		var formattedDuration = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;

		var filter1 = moment(`${formattedDuration}`, 'HH:mm').subtract(`${teaTime}`, 'HH:mm');
		var Duration1 = filter1.format('HH:mm')

		var filter2 = moment(`${Duration1}`, 'HH:mm').subtract(`${breakTimeDuration}`, 'HH:mm');
		var Duration2 = filter2.format('HH:mm')

		var inputElement = document.getElementById('time_work_hour');
		inputElement.value = Duration2;
	} else {
		var inputElement = document.getElementById('time_work_hour');
		inputElement.value = 'Invalid Time Format';
	}
});


// DATE PICKER DURATION
$('#time_work_hour').datetimepicker({
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
	format: 'HH:mm',
	useCurrent: false,
});


/////////////////////////////////////////////////////////////////////////////////////////
// SELECTION
$('#leave_id,#attendance_type_id,#daytype_id').select2({
	placeholder: 'Please Choose',
	width: '100%',
	allowClear: true,
	closeOnSelect: true,
});


/////////////////////////////////////////////////////////////////////////////////////////
// VALIDATION
$(document).ready(function() {
	$('#form').bootstrapValidator({
		feedbackIcons: {
			valid: '',
			invalid: '',
			validating: ''
		},
		fields: {

			daytype_id: {
				validators: {
					notEmpty: {
						message: 'Please select a day type.'
					},
				}
			},

			in: {
				validators: {
					notEmpty: {
						message: 'Please insert in time.'
					},
				}
			},

			break: {
				validators: {
					notEmpty: {
						message: 'Please insert break time.'
					},
				}
			},

			resume: {
				validators: {
					notEmpty: {
						message: 'Please insert resume time.'
					},
				}
			},

			out: {
				validators: {
					notEmpty: {
						message: 'Please insert out time.'
					},
				}
			},

		}
	})
});
@endsection
