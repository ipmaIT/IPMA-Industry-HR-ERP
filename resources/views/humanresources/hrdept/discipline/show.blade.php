@extends('layouts.app')

@section('content')
<style>
  /* div {
    border: 1px solid black;
  } */
</style>

<?php

use App\Models\Staff;
use App\Models\HumanResources\OptDisciplinaryAction;
use App\Models\HumanResources\OptViolation;
use App\Models\HumanResources\OptInfractions;

$staff = $discipline->belongstostaff->name;
$supervisor = $discipline->belongstosupervisor->name;
$disciplinary_action = $discipline->belongstooptdisciplinaryaction->disciplinary_action;
$violation = $discipline->belongstooptviolation()->select('violation', 'remarks')->first();
$infraction = $discipline->belongstooptinfractions()->select('infraction', 'remarks')->first();
?>

<div class="container">
  @include('humanresources.hrdept.navhr')
  <h4>Show Discipline</h4>

  <div class="row mt-3">
    <div class="col-md-2">
      <label for="name" class="col-form-label col-sm-2">Name : </label>
    </div>
    <div class="col-md-10">
      <input type="text" name="staff_id" value="{{ old('name', @$staff) }}" id="name" class="form-control form-control-sm col-sm-12 @error('staff_id') is-invalid @enderror" placeholder="Name" readonly>
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-md-2">
      <label for="supervisor" class="col-form-label col-sm-2">Supervisor Incharge : </label>
    </div>
    <div class="col-md-10">
      <input type="supervisor_id" name="staff_id" value="{{ old('name', @$supervisor) }}" id="supervisor" class="form-control form-control-sm col-sm-12 @error('staff_id') is-invalid @enderror" readonly>
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-md-2">
      <label for="disciplinary_action" class="col-form-label col-sm-2">Disciplinary Action : </label>
    </div>
    <div class="col-md-10">
      <input type="text" name="disciplinary_action_id" value="{{ old('name', @$disciplinary_action) }}" id="name" class="form-control form-control-sm col-sm-12 @error('staff_id') is-invalid @enderror" readonly>
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-md-2">
      <label for="violation" class="col-form-label col-sm-2">Violation : </label>
    </div>
    <div class="col-md-10">
      <input type="text" name="violation_id" value="{{ old('name', @$violation->violation.' - '.$violation->remarks) }}" id="name" class="form-control form-control-sm col-sm-12 @error('staff_id') is-invalid @enderror" readonly>
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-md-2">
      <label for="infraction" class="col-form-label col-sm-2">Infraction Level : </label>
    </div>
    <div class="col-md-10">
      <input type="text" name="infraction_id" value="{{ old('name', @$infraction->infraction.' - '.$infraction->remarks) }}" id="name" class="form-control form-control-sm col-sm-12 @error('infraction_id') is-invalid @enderror" readonly>
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-md-2">
      <label for="misconduct" class="col-form-label col-sm-2">Misconduct Date : </label>
    </div>
    <div class="col-md-10">
      <input type="text" name="misconduct_date" value="{{ old('name', @$discipline->misconduct_date) }}" id="name" class="form-control form-control-sm col-sm-12 @error('staff_id') is-invalid @enderror" readonly>
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-md-2">
      <label for="action_taken_date" class="col-form-label col-sm-2">Action Taken Date : </label>
    </div>
    <div class="col-md-10">
      <input type="text" name="action_taken_date" value="{{ old('name', @$discipline->action_taken_date) }}" id="name" class="form-control form-control-sm col-sm-12 @error('staff_id') is-invalid @enderror" readonly>
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-md-2">
      <label for="reason" class="col-form-label col-sm-2">Description of Incident : </label>
    </div>
    <div class="col-md-10">
      <textarea name="reason" class="form-control form-control-sm col-sm-12 @error('reason') is-invalid @enderror" placeholder="Placeholder" readonly>{{ old('reason', @$discipline->reason) }}</textarea>
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-md-2">
      <label for="action_to_be_taken" class="col-form-label col-sm-2">Action to be Taken : </label>
    </div>
    <div class="col-md-10">
      <textarea name="action_to_be_taken" class="form-control form-control-sm col-sm-12 @error('action_to_be_taken') is-invalid @enderror" readonly>{{ old('reason', @$discipline->action_to_be_taken) }}</textarea>
    </div>
  </div>

  @if ($discipline->softcopy)
  <input type="hidden" name="old_softcopy" id="old_softcopy" value="{{ $discipline->softcopy }}">
  <div class="row mt-3">
    <div class="col-md-2">
      <label for="softcopy" class="col-form-label col-sm-2">Softcopy : </label>
    </div>
    <div class="col-md-10">
      <a href="{{ asset('storage/disciplinary/' . $discipline->softcopy) }}" target="_blank">
        {{ $discipline->softcopy }}
      </a>
    </div>
  </div>
  @endif

  <div class="row mt-3">
    <div class="col-md-12 text-center">
      <a href="{{ url()->previous() }}">
        <button class="btn btn-sm btn-outline-secondary">BACK</button>
      </a>
    </div>
  </div>

</div>
@endsection

@section('js')

@endsection

@section('nonjquery')

@endsection
