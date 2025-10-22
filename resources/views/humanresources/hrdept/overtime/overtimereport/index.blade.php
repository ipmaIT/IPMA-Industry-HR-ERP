@extends('layouts.app')

@section('content')
<style>
  .table,
  .table tr,
  .table td {
    border: 1px solid black;
    font-size: 14px;
  }

  .top-row td {
    background-color: #cccccc;
  }
</style>
<?php

use Carbon\Carbon;

use App\Models\HumanResources\OptBranch;
use App\Models\HumanResources\HROvertime;

$location = OptBranch::pluck('location', 'id')->toArray();

$no = 1;
$total_col = 0;
$total_hour = '0';

if ($date_start != NULL && $date_end != NULL) {
  $startDate = Carbon::parse($date_start);
  $endDate = Carbon::parse($date_end);
}

$currentYear = Carbon::now()->year;
$lastYear = Carbon::now()->subYear()->year;
?>

<div class="container">
  @include('humanresources.hrdept.navhr')
  <h4>Overtime Report</h4>

  <form method="POST" action="{{ route('overtimereport.index') }}" accept-charset="UTF-8" id="form" autocomplete="off" class="" enctype="multipart/form-data">
    @csrf

  <div class="row g-3 mb-3">
    <div class="col-auto">
      <input type="text" name="date_start" value="{{ old('date_start') }}" id="date_start" class="form-control form-control-sm col-sm-12 @error('date_start') is-invalid @enderror" placeholder="Date Start">
    </div>
    <div class="col-auto">
      <input type="text" name="date_end" value="{{ old('date_end') }}" id="date_end" class="form-control form-control-sm col-sm-12 @error('date_end') is-invalid @enderror" placeholder="Date End">
    </div>
    <div class="col-auto">
      <select name="branch" id="branch" class="form-select form-select-sm branch @error('branch') is-invalid @enderror">
        <option value="">Please choose</option>
        @foreach($location as $k1 => $v1)
          <option value="{{ $k1 }}" {{ (old('branch') == $k1)?'selected':NULL }}>{{ $v1 }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-auto">
      <select class="form-select form-select-sm title @error('title') is-invalid @enderror" id="title" name="title">
        <option selected="selected" value=""></option>
        <option value="1st half">1st half</option>
        <option value="2nd half">2nd half</option>
      </select>
    </div>
    <div class="col-auto">
      <select class="form-control month" id="month" name="month">
        <option selected="selected" value=""></option>
        <option value="January">January</option>
        <option value="February">February</option>
        <option value="March">March</option>
        <option value="April">April</option>
        <option value="May">May</option>
        <option value="June">June</option>
        <option value="July">July</option>
        <option value="August">August</option>
        <option value="September">September</option>
        <option value="October">October</option>
        <option value="November">November</option>
        <option value="December">December</option>
      </select>
    </div>
    <div class="col-auto">
      <select class="form-select form-select-sm year @error('year') is-invalid @enderror" id="year" name="year">
        <option selected="selected" value=""></option>
        <option value="{{ $currentYear }}">{{ $currentYear }}</option>
        <option value="{{ $lastYear }}">{{ $lastYear }}</option>
      </select>
    </div>
    <div class="col-auto">
      <button type="submit" class="btn btn-sm btn-outline-secondary">Submit</button>
    </div>
  </div>

  </form>

  @if ($overtimes != NULL)
  <div class="row g-3 mb-3 text-center">
    <div class="text-center">
      Overtime Claim Form {{Carbon::parse($date_start)->format('j')}} - {{Carbon::parse($date_end)->format('j')}} {{Carbon::parse($date_end)->format('F')}} {{Carbon::parse($date_end)->format('Y') }} ({{ $title }} of {{ $month }} {{ $year }})
    </div>
  </div>

  <div class="row g-3 mb-3">
    <table class="table table-hover table-sm align-middle">
      <tr class="top-row">
        <td class="text-center" style="width: 30px;">
          NO
        </td>
        <td class="text-center" style="width: 55px;">
          ID
        </td>
        <td class="text-center" style="max-width: 150px;">
          NAME
        </td>
        <td class="text-center">
          DEPARTMENT
        </td>
        @for ($date = $startDate; $date->lte($endDate); $date->addDay())
        <td class="text-center" style="max-width: 48px;">
          <?php
          $total_col++;
          $rows[] = $date->format('Y-m-d');
          echo $formattedDate = $date->format('d/m');
          ?>
        </td>
        @endfor
        <td class="text-center" style="max-width: 60px;">
          TOTAL<br />HOURS
        </td>
        <td class="text-center" style="max-width: 70px;">
          SIGNATURE
        </td>
      </tr>

      @foreach ($overtimes as $overtime)
      <?php $total_hour_per_person = '0'; ?>
      <tr>
        <td class="text-truncate text-center" style="width: 30px;">
          {{ $no++ }}
        </td>
        <td class="text-truncate text-center" style="width: 55px;" title="{{ $overtime->username }}">
          {{ $overtime->username }}
        </td>
        <td class="text-truncate" style="max-width: 150px;" title="{{ $overtime->name }}">
          {{ $overtime->name }}
        </td>
        <td class="text-truncate" style="max-width: 1px;" title="{{ $overtime->department }}">
          {{ $overtime->department }}
        </td>
        @foreach ($rows as $row)
          <?php
          $ot = HROvertime::join('hr_overtime_ranges', 'hr_overtime_ranges.id', '=', 'hr_overtimes.overtime_range_id')
            ->where('hr_overtimes.ot_date', '=', $row)
            ->where('hr_overtimes.staff_id', '=', $overtime->staff_id)
            ->where('hr_overtimes.active', 1)
            ->select('hr_overtimes.assign_staff_id', 'hr_overtime_ranges.total_time')
            ->first();

          $background = "";

          if ($ot) {
            $department_id = $ot->belongstoassignstaff->belongstomanydepartment()->first()->department_id;

            if ($department_id == '14' || $department_id == '15') {
              $background = "background-color: #d9d9d9";
            }
          }
          ?>
        <td class="text-truncate text-center" style="max-width: 48px;<?php echo $background ?>">
          <?php
          if ($ot) {
            echo $timeString_per_person = (Carbon::parse($ot->total_time))->format('H:i');

            // Explode the time string into an array of hours, minutes, and seconds
            $timeArray_per_person = explode(':', $timeString_per_person);

            // Calculate the total minutes
            $totalMinutes_per_person = ($timeArray_per_person[0] * 60) + $timeArray_per_person[1];
            $total_hour_per_person = $total_hour_per_person + $totalMinutes_per_person;
          }
          ?>
        </td>
        @endforeach
        <td class="text-center" style="max-width: 60px;">
          <?php
          $total_hour = $total_hour + $total_hour_per_person;

          echo (sprintf('%02d', intdiv($total_hour_per_person, 60)) . ':' . sprintf('%02d', ($total_hour_per_person % 60)));
          ?>
        </td>
        <td style="max-width: 70px;"></td>
      </tr>
      @endforeach

      <tr>
        <td align="right" colspan="{{ $total_col+4 }}">
          TOTAL HOURS
        </td>
        <td class="text-center">
          <?php
          echo (sprintf('%02d', intdiv($total_hour, 60)) . ':' . sprintf('%02d', ($total_hour % 60)));
          ?>
        </td>
        <td></td>
      </tr>
    </table>

    <div class="row">
      <div style="width: 25px; height: 25px; background-color: #d9d9d9;"></div>&nbsp;REMARK
    </div>

    <form method="GET" action="{{ route('overtimereport.print') }}" accept-charset="UTF-8" id="form" autocomplete="off" class="" enctype="multipart/form-data">
      @csrf
    <div class="row">
      <div class="text-center">
        <input type="hidden" name="date_start" id="date_start" value="{{ $date_start }}">
        <input type="hidden" name="date_end" id="date_end" value="{{ $date_end }}">
        <input type="hidden" name="branch" id="branch" value="{{ $branch }}">
        <input type="hidden" name="title" id="title" value="{{ $title }}">
        <input type="hidden" name="month" id="month" value="{{ $month }}">
        <input type="hidden" name="year" id="year" value="{{ $year }}">

        <input type="submit" class="btn btn-sm btn-outline-secondary" value="PRINT" target="_blank">
      </div>
    </div>
    </form>
  </div>
  @endif

</div>
@endsection

@section('js')
/////////////////////////////////////////////////////////////////////////////////////////
// DATE PICKER
$('#date_start, #date_end').datetimepicker({
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
$('#branch').select2({
  placeholder: 'Location',
  width: '100px',
  allowClear: false,
  closeOnSelect: true,
});

$('#title').select2({
  placeholder: 'Title',
  width: '100px',
  allowClear: false,
  closeOnSelect: true,
});

$('#month').select2({
  placeholder: 'Month',
  width: '130px',
  allowClear: false,
  closeOnSelect: true,
});

$('#year').select2({
  placeholder: 'Year',
  width: '100px',
  allowClear: false,
  closeOnSelect: true,
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
