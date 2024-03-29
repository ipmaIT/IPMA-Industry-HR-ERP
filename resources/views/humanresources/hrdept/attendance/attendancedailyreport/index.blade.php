@extends('layouts.app')

@section('content')
<style>
	.table,
	.table tr,
	.table td {
		border: 1px solid black;
		font-size: 12px;
	}

	.top-row td {
		background-color: #cccccc;
	}
</style>

<?php

use \Carbon\Carbon;

use App\Models\HumanResources\HRLeave;
use App\Models\HumanResources\OptTcms;
use App\Models\HumanResources\HROutstation;
use App\Models\HumanResources\HRAttendance;
?>

<div class="container">
	@include('humanresources.hrdept.navhr')
	<h4>Attendance Daily Report</h4>

	{{ Form::open(['route' => ['attendancedailyreport.index'], 'id' => 'form', 'class' => 'form-horizontal', 'autocomplete' => 'off', 'files' => true]) }}

	<div class="row g-3 mb-3">
		<div class="col-auto">
			{{ Form::text('date', @$selected_date, ['class' => 'form-control form-control-sm col-auto', 'id' => 'date', 'autocomplete' => 'off']) }}
		</div>
		<div class="col-auto">
			{!! Form::submit('SEARCH', ['class' => 'form-control form-control-sm btn btn-sm btn-outline-secondary']) !!}
		</div>
	</div>

	{!! Form::close() !!}


	@if (!empty($dailyreport_absent)|| !empty($dailyreport_late)|| !empty($dailyreport_outstation))
	<div class="row g-3 mb-3">
		<table class="table table-hover table-sm align-middle">

			<!-- ABSENT -->
			@if (!empty($dailyreport_absent))
			<?php $no = 1; ?>
			<tr class="top-row">
				<td colspan="11">
					<b>ABSENT</b>
				</td>
			</tr>
			<tr class="top-row">
				<td class="text-center" style="width: 30px;">
					NO
				</td>
				<td class="text-center" style="width: 75px;">
					DATE
				</td>
				<td class="text-center" style="width: 90px;">
					STATUS
				</td>
				<td class="text-center" style="max-width: 60px;">
					LOCATION
				</td>
				<td class="text-center" style="max-width: 70px;">
					DEPARTMENT
				</td>
				<td class="text-center" style="width: 55px;">
					GROUP
				</td>
				<td class="text-center" style="width: 55px;">
					ID
				</td>
				<td class="text-center" style="max-width: 120px;">
					NAME
				</td>
				<td colspan="2" class="text-center" style="max-width: 100%;">
					REASON / REMARK
				</td>
				<td class="text-center" style="width: 90px;">
					LEAVE ID
				</td>
			</tr>

			@foreach ($dailyreport_absent as $absent)
			<?php
			// dd($absent);
			if ($absent->leave_id != NULL) {
				$leave = HRLeave::join('option_leave_types', 'hr_leaves.leave_type_id', '=', 'option_leave_types.id')
				->where('hr_leaves.id', '=', $absent->leave_id)
				->select('hr_leaves.id as leave_id', 'hr_leaves.leave_no', 'hr_leaves.leave_year', 'option_leave_types.leave_type_code', 'hr_leaves.reason')
				->first();

				// dd($leave);

				$status = $leave->leave_type_code;
				// $remark = $leave->reason;
				$remark = $absent->remarks;
				$leave_number = 'HR9-' . str_pad($leave->leave_no, 5, "0", STR_PAD_LEFT) . '/' . $leave->leave_year;
			} else {

				if ($absent->attendance_type_id != NULL) {
					$status_code = OptTcms::where('id', '=', $absent->attendance_type_id)->first();
					$status = $status_code->leave;
				} else {
					$status = NULL;
				}

				$remark = $absent->remarks;
				$leave_number = NULL;
			}
			?>

			<tr>
				<td class="text-center">
					{{ $no++ }}
				</td>
				<td class="text-center">
					{{ $absent->attend_date }}
				</td>
				<td class="text-center" title="{{ $status }}">
					{{ $status }}
				</td>
				<td class="text-truncate text-center" style="max-width: 60px;" title="{{ $absent->code }}">
					{{ $absent->code }}
				</td>
				<td class="text-truncate" style="max-width: 70px;" title="{{ $absent->department }}">
					{{ $absent->department }}
				</td>
				<td class="text-center">
					{{ $absent->group }}
				</td>
				<td class="text-center">
					{{ $absent->username }}
				</td>
				<td class="text-truncate" style="max-width: 120px;" title="{{ $absent->name }}">
					{{ $absent->name }}
				</td>
				<td colspan="2" class="text-truncate" style="max-width: 100%;" title="{{ $remark }}">
					{{ $remark }}
				</td>
				<td class="text-center">
					@if ($leave_number != NULL)
					<a href="{{ route('leave.show', $leave->leave_id) }}" target="_blank">
						{{ $leave_number }}
					</a>
					@endif
				</td>
			</tr>
			@endforeach
			@endif


			<!-- LATE -->
			@if (!empty($dailyreport_late))
			<?php $no = 1; ?>
			<tr class="top-row">
				<td colspan="11">
					<b>LATE</b>
				</td>
			</tr>
			<tr class="top-row">
				<td class="text-center" style="width: 30px;">
					NO
				</td>
				<td class="text-center" style="width: 75px;">
					DATE
				</td>
				<td class="text-center" style="width: 90px;">
					STATUS
				</td>
				<td class="text-center" style="max-width: 60px;">
					LOCATION
				</td>
				<td class="text-center" style="max-width: 70px;">
					DEPARTMENT
				</td>
				<td class="text-center" style="width: 55px;">
					GROUP
				</td>
				<td class="text-center" style="width: 55px;">
					ID
				</td>
				<td class="text-center" style="max-width: 120px;">
					NAME
				</td>
				<td class="text-center" style="max-width: 100%;">
					REASON / REMARK
				</td>
				<td class="text-center" style="width: 90px;">
					IN
				</td>
				<td class="text-center" style="width: 90px;">
					LEAVE ID
				</td>
			</tr>

			@foreach ($dailyreport_late as $late)
			<?php
			$staff_late = HRAttendance::join('staffs', 'staffs.id', '=', 'hr_attendances.staff_id')
			->join('logins', 'hr_attendances.staff_id', '=', 'logins.staff_id')
			->join('pivot_staff_pivotdepts', 'staffs.id', '=', 'pivot_staff_pivotdepts.staff_id')
			->join('pivot_dept_cate_branches', 'pivot_staff_pivotdepts.pivot_dept_id', '=',  'pivot_dept_cate_branches.id')
			->join('option_branches', 'pivot_dept_cate_branches.branch_id', '=', 'option_branches.id')
			->leftjoin('option_restday_groups', 'staffs.restday_group_id', '=', 'option_restday_groups.id')
			->where('hr_attendances.attend_date', '=', $selected_date)
			->where('staffs.id', $late)
			->where('pivot_staff_pivotdepts.main', 1)
			->select('hr_attendances.attend_date', 'option_branches.code', 'pivot_dept_cate_branches.department', 'option_restday_groups.group', 'logins.username', 'staffs.name', 'hr_attendances.leave_id', 'hr_attendances.remarks', 'hr_attendances.in', 'pivot_dept_cate_branches.wh_group_id')
			->first();

			$in = Carbon::parse($staff_late->in)->format('h:i a');

			if ($staff_late->leave_id != NULL) {
				$leave = HRLeave::join('option_leave_types', 'hr_leaves.leave_type_id', '=', 'option_leave_types.id')
				->where('hr_leaves.id', '=', $staff_late->leave_id)
				->select('hr_leaves.id as leave_id', 'hr_leaves.leave_no', 'hr_leaves.leave_year', 'option_leave_types.leave_type_code', 'hr_leaves.reason')
				->first();

				$status = $leave->leave_type_code;
				$remark = $leave->reason;
				$leave_number = 'HR9-' . str_pad($leave->leave_no, 5, "0", STR_PAD_LEFT) . '/' . $leave->leave_year;
			} else {

				if ($staff_late->attendance_type_id != NULL) {
					$status_code = OptTcms::where('id', '=', $staff_late->attendance_type_id)->first();
					$status = $status_code->leave;
				} else {
					$status = NULL;
				}

				$remark = $staff_late->remarks;
				$leave_number = NULL;
			}
			?>

			<tr>
				<td class="text-center">
					{{ $no++ }}
				</td>
				<td class="text-center">
					{{ $staff_late->attend_date }}
				</td>
				<td class="text-center" title="LATE">
					LATE
				</td>
				<td class="text-truncate text-center" style="max-width: 60px;" title="{{ $staff_late->code }}">
					{{ $staff_late->code }}
				</td>
				<td class="text-truncate" style="max-width: 70px;" title="{{ $staff_late->department }}">
					{{ $staff_late->department }}
				</td>
				<td class="text-center">
					{{ $staff_late->group }}
				</td>
				<td class="text-center">
					{{ $staff_late->username }}
				</td>
				<td class="text-truncate" style="max-width: 120px;" title="{{ $staff_late->name }}">
					{{ $staff_late->name }}
				</td>
				<td class="text-truncate" style="max-width: 100%;" title="{{ $remark }}">
					{{ $remark }}
				</td>
				<td class="text-center">
					<span class="text-danger">{{ $in }}</span>
				</td>
				<td class="text-center">
					@if ($leave_number != NULL)
					<a href="{{ route('leave.show', $leave->leave_id) }}" target="_blank">
						{{ $leave_number }}
					</a>
					@endif
				</td>
			</tr>
			@endforeach
			@endif


			<!-- OUTSTATION -->
			@if (!empty($dailyreport_outstation))
			<?php $no = 1; ?>
			<tr class="top-row">
				<td colspan="11">
					<b>OUTSTATION</b>
				</td>
			</tr>
			<tr class="top-row">
				<td class="text-center" style="width: 30px;">
					NO
				</td>
				<td class="text-center" style="width: 75px;">
					DATE
				</td>
				<td class="text-center" style="width: 90px;">
					STATUS
				</td>
				<td class="text-center" style="max-width: 60px;">
					LOCATION
				</td>
				<td class="text-center" style="max-width: 70px;">
					DEPARTMENT
				</td>
				<td class="text-center" style="width: 55px;">
					GROUP
				</td>
				<td class="text-center" style="width: 55px;">
					ID
				</td>
				<td class="text-center" style="max-width: 120px;">
					NAME
				</td>
				<td colspan="2" class="text-center" style="max-width: 100%;">
					REASON / REMARK
				</td>
				<td class="text-center" style="width: 90px;">
					LEAVE ID
				</td>
			</tr>

			@foreach ($dailyreport_outstation as $outstation)
			<?php

			if ($outstation->outstation_id != NULL) {
				$out = HROutstation::leftjoin('customers', 'hr_outstations.customer_id', '=', 'customers.id')
				->where('hr_outstations.id', '=', $outstation->outstation_id)
				->where('hr_outstations.active', '=', 1)
				->select('customers.customer', 'hr_outstations.remarks', 'hr_outstations.customer_id')
				->first();

				$status = 'OUTSTATION';

				if ($out->customer_id != NULL) {
					$remark = $out->customer;
				} else {
					$remark = $out->remarks;
				}

				$contact = NULL;
			} else {

				if ($outstation->attendance_type_id != NULL) {
					$status_code = OptTcms::where('id', '=', $outstation->attendance_type_id)->first();
					$status = $status_code->leave;
				} else {
					$status = NULL;
				}

				$remark = $outstation->remarks;
				$contact = NULL;
			}
			?>

			<tr>
				<td class="text-center">
					{{ $no++ }}
				</td>
				<td class="text-center">
					{{ $outstation->attend_date }}
				</td>
				<td class="text-center" title="{{ $status }}">
					{{ $status }}
				</td>
				<td class="text-truncate text-center" style="max-width: 60px;" title="{{ $outstation->code }}">
					{{ $outstation->code }}
				</td>
				<td class="text-truncate" style="max-width: 70px;" title="{{ $outstation->department }}">
					{{ $outstation->department }}
				</td>
				<td class="text-center">
					{{ $outstation->group }}
				</td>
				<td class="text-center">
					{{ $outstation->username }}
				</td>
				<td class="text-truncate" style="max-width: 120px;" title="{{ $outstation->name }}">
					{{ $outstation->name }}
				</td>
				<td colspan="2" class="text-truncate" style="max-width: 100%;" title="{{ $remark }}">
					{{ $remark }}
				</td>
				<td class="text-center">
					{{ $contact }}
				</td>
			</tr>
			@endforeach
			@endif

		</table>
	</div>
	@endif

	{{ Form::open(['route' => ['attendancedailyreport.print'], 'method' => 'GET',  'id' => 'form', 'class' => 'form-horizontal', 'autocomplete' => 'off', 'files' => true]) }}
	<div class="row">
		<div class="text-center">
			<input type="hidden" name="date" id="date" value="{{ $selected_date }}">

			<input type="submit" class="btn btn-sm btn-outline-secondary" value="PRINT" target="_blank">
		</div>
	</div>
	{{ Form::close() }}
</div>
@endsection

@section('js')
/////////////////////////////////////////////////////////////////////////////////////////
// DATE PICKER
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
	format: 'YYYY-MM-DD',
	useCurrent: true,
});


/////////////////////////////////////////////////////////////////////////////////////////
// VALIDATOR
$(document).ready(function() {
	$('#form').bootstrapValidator({
		feedbackIcons: {
			valid: '',
			invalid: '',
			validating: ''
		},

		fields: {
			date_start: {
				validators: {
					notEmpty: {
						message: 'Please select a start date.'
					}
				}
			},

			date_end: {
				validators: {
					notEmpty: {
						message: 'Please select a end date.'
					}
				}
			},

			branch: {
				validators: {
					notEmpty: {
						message: 'Please select a branch.'
					}
				}
			},

		}
	})

});
@endsection
