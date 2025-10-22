@extends('layouts.app')

@section('content')
<style>
  /* div {
    border: 1px solid black;
  } */
</style>

<style>
  .scrollable-div {
    /* Set the width height as needed */
    width: 100%;
    height: 400px;
    background-color: blanchedalmond;
    /* Add scrollbars when content overflows */
    overflow: auto;
  }

  p {
    margin-top: 4px;
    margin-bottom: 4px;
  }
</style>

<?php
$customer = App\Models\Customer::pluck('customer', 'id')->sortKeys()->toArray();
?>

<div class="container">
  @include('humanresources.hrdept.navhr')
  <h4>Edit Replacement Leave</h4>

  <form method="POST" action="{{ route('rleave.update', $rleave) }}" accept-charset="UTF-8" id="form" autocomplete="off" class="" enctype="multipart/form-data">
    @csrf
    @method('PATCH')

  <div class="row mt-3">
    <div class="col-md-2">
      <label for="id" class="col-form-label col-auto">ID : </label>
    </div>
    <div class="col-md-10">
      <label for="id" class="col-form-label col-auto">{{ @$rleave->belongstostaff->hasmanylogin()->first()->username }}</label>
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-md-2">
      <label for="name" class="col-form-label col-auto">Name : </label>
    </div>
    <div class="col-md-10">
      <label for="id" class="col-form-label col-auto">{{ @$rleave->belongstostaff()->first()->name }}</label>
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-md-2">
      <label for="date_start" class="col-form-label col-auto">Date Start : </label>
    </div>
    <div class="col-md-10 @error('date_start') has-error @enderror">
      <input type="text" name="date_start" value="{{ old('date_start', $rleave->date_start) }}" id="id" class="form-control form-control-sm col-sm-12 @error('date_start') is-invalid @enderror" placeholder="Date Start">
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-md-2">
      <label for="date_end" class="col-form-label col-auto">Date End : </label>
    </div>
    <div class="col-md-10 @error('date_end') has-error @enderror">
      <input type="text" name="date_end" value="{{ old('date_end', $rleave->date_end) }}" id="id" class="form-control form-control-sm col-sm-12 @error('date_end') is-invalid @enderror" placeholder="Date End">
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-md-2">
      <label for="leave_total" class="col-form-label col-auto">Total Day <i class="bi bi-question-circle" data-toggle="tooltip" title="Total day for replacement leave."></i> : </label>
    </div>
    <div class="col-md-10 @error('leave_total') has-error @enderror">
      <input type="text" name="leave_total" value="{{ old('leave_total', $rleave->leave_total) }}" id="id" class="form-control form-control-sm col-sm-12 @error('leave_total') is-invalid @enderror" placeholder="Total Day">
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-md-2">
      <label for="leave_utilize" class="col-form-label col-auto">Total Utilize <i class="bi bi-question-circle" data-toggle="tooltip" title="Total day used in leave."></i> : </label>
    </div>
    <div class="col-md-10 @error('leave_utilize') has-error @enderror">
      <input type="text" name="leave_utilize" value="{{ old('leave_utilize', $rleave->leave_utilize) }}" id="id" class="form-control form-control-sm col-sm-12 @error('leave_utilize') is-invalid @enderror" placeholder="Total Utilize">
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-md-2">
      <label for="leave_balance" class="col-form-label col-auto">Total Balance : </label>
    </div>
    <div class="col-md-10 @error('leave_balance') has-error @enderror">
      <input type="text" name="leave_balance" value="{{ old('leave_balance', $rleave->leave_balance) }}" id="id" class="form-control form-control-sm col-sm-12 @error('leave_balance') is-invalid @enderror" placeholder="Total Balance">
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-md-2">
      {{Form::label('customer_id', 'Customer')}}
      <label for="customer_id" class="col-form-label col-auto">Customer : </label>
    </div>
    <div class="col-md-10 @error('customer_id') has-error @enderror">
      <select name="customer_id" id="customer_id" class="form-select form-select-sm col-sm-12 @error('customer_id') is-invalid @enderror">
        <option value="">Please choose</option>
        @foreach($customer as $k1 => $v1)
          <option value="{{ $k1 }}" {{ (old('customer_id', $rleave->customer_id) == $k1)?'selected':NULL }}>{{ $v1 }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-md-2">
      <label for="id" class="col-form-label col-auto">Reason : </label>
    </div>
    <div class="col-md-10 @error('reason') has-error @enderror">
      <textarea name="reason" id="reason" class="form-control form-control-sm col-sm-12 @error('reason') is-invalid @enderror" placeholder="Reason">{{ old('reason', $rleave->reason) }}</textarea>
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-md-12 text-center">
      <button type="submit" class="btn btn-sm btn-outline-secondary">Update</button>
    </div>
  </div>

  </form>

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
/////////////////////////////////////////////////////////////////////////////////////////
// CHECK ALL STAFF
$("#checkAll").change(function () {
$(".staff").prop('checked', this.checked);
});

// CHECK ALL GROUP 1
$("#checkG1").change(function () {
$(".group1").prop('checked', this.checked);
});

// CHECK ALL GROUP 2
$("#checkG2").change(function () {
$(".group2").prop('checked', this.checked);
});


/////////////////////////////////////////////////////////////////////////////////////////
// SELECTION
$('#customer_id').select2({
placeholder: 'Please Select',
width: '100%',
allowClear: true,
closeOnSelect: true,
});


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
message: 'Please select a date.'
}
}
},

date_end: {
validators: {
notEmpty: {
message: 'Please select a date.'
}
}
},

leave_total: {
validators: {
notEmpty: {
message: 'Please insert a value. 0 by default.'
},
numeric: {
message: 'The value is not numeric'
}
}
},

leave_utilize: {
validators: {
notEmpty: {
message: 'Please insert a value. 0 by default.'
},
numeric: {
message: 'The value is not numeric'
}
}
},

leave_balance: {
validators: {
notEmpty: {
message: 'Please insert a value. 0 by default.'
},
numeric: {
message: 'The value is not numeric'
}
}
},

reason: {
validators: {
notEmpty: {
message: 'Please insert a reason.'
}
}
},

}
})
});


/////////////////////////////////////////////////////////////////////////////////////////
// TOOLTIP
$(function () {
$('[data-toggle="tooltip"]').tooltip()
})
@endsection

@section('nonjquery')

@endsection
