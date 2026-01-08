@extends('layouts.app')

@section('content')
<style>
	.scrollable-div {
		/* Set the width height as needed */
/*		width: 100%;*/
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
use \App\Models\HumanResources\OptWorkingHour;
use \App\Models\Staff;
use \App\Models\Customer;

use \Carbon\Carbon;

$staffs = Staff::join('logins', 'staffs.id', '=', 'logins.staff_id')
			->where('staffs.active', 1)
			->where('logins.active', 1)
			->where(function ($query) {
				$query->where('staffs.div_id', '!=', 2)
				->orWhereNull('staffs.div_id');
			})
			->select('staffs.id as staffID', 'staffs.*', 'logins.*')
			->orderBy('logins.username', 'asc')
			->get();

$c = Customer::orderBy('customer')->pluck('customer', 'id')->toArray();
?>

<div class="col-sm-12 row">
	@include('humanresources.hrdept.navhr')
	<h4>Add Staff For Outstation</h4>
	<form method="POST" action="{{ route('outstation.store') }}" accept-charset="UTF-8" id="form" class="" autocomplete="off" enctype="multipart/form-data">
		@csrf
	<div class="form-group row mb-3 @error('staff_id') has-error @enderror">
		<div class="col-md-2">
		<label for="staff" class="col-sm-2 col-form-label">Outstation Staff : </label>
		</div>
		<div class="col-md-10">
			<div class="scrollable-div">
				@foreach ($staffs as $staff)
				<p>
					<input type="checkbox" id="staff_id{{ $staff->staffID }}" name="staff_id[]" id="staff" value="{{ $staff->staffID }}">
					<label for="staff_id{{ $staff->staffID }}">{{ $staff->username }} - {{ $staff->name }}</label>
				</p>
				@endforeach
			</div>
		</div>
	</div>

	<div class="form-group row mb-3 {{ $errors->has('customer_id') ? 'has-error' : '' }}">
		<label for="loc" class="col-sm-2 col-form-label">Location : </label>
		<div class="col-md-10">
			<select name="customer_id" id="loc" class="form-select form-select-sm col-auto @error('customer_id') is-invalid @enderror">
				<option value="">Please choose</option>
				@foreach($c as $k => $v)
					<option value="{{ $k }}" {{ (old('customer_id') == $k)?'selected':NULL }}>{{ $v }}</option>
				@endforeach
			</select>
		</div>
	</div>

	<div class="form-group row mb-3 @error('date_from') has-error @enderror">
		<label for="from" class="col-sm-2 col-form-label">From : </label>
		<div class="col-md-10" style="position: relative">
			<input type="text" name="date_from" value="{{ old('date_from') }}" id="from" class="form-control form-control-sm col-auto @error('date_from') has-error @enderror">
		</div>
	</div>

	<div class="form-group row mb-3 @error('date_to') has-error @enderror">
		<label for="to" class="col-sm-2 col-form-label">To : </label>
		<div class="col-md-10" style="position: relative">
			<input type="text" name="date_to" value="{{ old('date_to') }}" id="to" class="form-control form-control-sm col-auto @error('date_to') has-error @enderror">
		</div>
	</div>

	<div class="form-group row mb-3 @error('remarks') has-error @enderror">
		<label for="name" class="col-sm-2 col-form-label">Name : </label>
		<div class="col-md-10">
			<textarea name="remarks" id="rem" class="form-control form-control-sm col-auto" placeholder="Remarks">{{ old('remarks') }}</textarea>
		</div>
	</div>

	<div class="form-group row mb-3 g-3 p-2">
		<div class="col-sm-10 offset-sm-2">
			<button type="submit" class="btn btn-sm btn-outline-secondary">Add Data</button>
		</div>
	</div>
	</form>

</div>
@endsection

@section('js')
/////////////////////////////////////////////////////////////////////////////////////////
$('#loc').select2({
	placeholder: 'Please choose',
	allowClear: true,
	closeOnSelect: true,
	width: '100%',
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
	var minDate = $('#from').val();
	$('#to').datetimepicker('minDate', minDate);
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
	var maxDate = $('#to').val();
	$('#from').datetimepicker('maxDate', maxDate);
	$('#form').bootstrapValidator('revalidateField', 'date_to');
});


/////////////////////////////////////////////////////////////////////////////////////////
// bootstrap validator

$('#form').bootstrapValidator({
	fields: {
		'staff_id[]': {
			validators: {
				notEmpty: {
					message: 'Please choose '
				},
			}
		},
		'date_from': {
			validators: {
				notEmpty: {
					message: 'Please insert date start. '
				},
				date: {
					format: 'YYYY-MM-DD',
					message: 'Please insert date start. '
				},
			}
		},
		'date_to': {
			validators: {
				notEmpty: {
					message: 'Please insert date end. '
				},
				date: {
					format: 'YYYY-MM-DD',
					message: 'Please insert date end. '
				},
			}
		},
	}
});
@endsection
