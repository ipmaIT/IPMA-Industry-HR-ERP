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
use App\Models\Staff;
use App\Models\Customer;

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
?>

<div class="container">
	@include('humanresources.hrdept.navhr')
	<h4>Add Replacement Leave</h4>

	<form method="POST" action="{{ route('rleave.store') }}" accept-charset="UTF-8" id="form" autocomplete="off" class="" enctype="multipart/form-data">
		@csrf

	<div class="row mt-3">
		<div class="col-md-2">
			<label for="name" class="col-form-label col-auto">Name : </label>
		</div>
		<div class="col-md-10 @error('staff_id.*') has-error @enderror">
			<div class="form-check form-check-inline">
				<label for="checkAll" class="form-check-label mx-5"><input type="checkbox" id="checkAll" class="form-check-input"> Check All</label>
				<label for="checkG1" class="form-check-label mx-5"><input type="checkbox" id="checkG1" class="form-check-input"> Check Group 1</label>
				<label for="checkG2" class="form-check-label mx-5"><input type="checkbox" id="checkG2" class="form-check-input"> Check Group 2</label>
			</div>
			<div class="scrollable-div">
				@foreach ($staffs as $staff)
				<div class="form-check m-2 ">
					<label class="form-check-label @error('staff_id.*') has-error @enderror" for="staff_id_{{ $staff->id }}">
						<input type="checkbox" class="form-check-input staff group{{ $staff->restday_group_id }} @error('staff_id.*') is-invalid @enderror" name="staff_id[]" id="staff_id_{{ $staff->id }}" value="{{ $staff->staffID }}">
						{{ $staff->username }} - Group {{ $staff->restday_group_id }} - {{ $staff->name }}
					</label>
				</div>
				@endforeach
			</div>
		</div>
	</div>

	<div class="row mt-3">
		<div class="col-md-2">
			<label for="date_start" class="col-form-label col-auto">Date Start : </label>
		</div>
		<div class="col-md-10 {{ $errors->has('date_start') ? 'has-error' : '' }}">
			<input type="text" name="date_start" value="{{ old('date_start') }}" id="date_start" class="form-control form-control-sm col-sm-12 @error('date_start') is-invalid @enderror" placeholder="Date Start">
		</div>
	</div>

	<div class="row mt-3">
		<div class="col-md-2">
			<label for="date_end" class="col-form-label col-auto">Date End : </label>
		</div>
		<div class="col-md-10 {{ $errors->has('date_end') ? 'has-error' : '' }}">
			<input type="text" name="date_end" value="{{ old('date_end') }}" id="date_end" class="form-control form-control-sm col-sm-12 @error('date_end') is-invalid @enderror" placeholder="Date End">
		</div>
	</div>

	<div class="row mt-3">
		<div class="col-md-2">
			<label for="customer_id" class="col-form-label col-auto">Customer : </label>
		</div>
		<div class="col-md-10">
			<select name="customer_id" id="customer_id" class="form-select form-select-sm col-sm-12 customer_id @error('customer_id') is-invalid @enderror">
				<option value="">Please choose</option>
				@foreach(Customer::pluck('customer', 'id')->toArray() as $k1 => $v1)
					<option value="{{ $k1 }}" {{ (old('customer_id') == $k1)?'selected':NULL }}>{{ $v1 }}</option>
				@endforeach
			</select>
		</div>
	</div>

	<div class="row mt-3">
		<div class="col-md-2">
			<label for="reason" class="col-form-label col-auto">Reason : </label>
		</div>
		<div class="col-md-10 {{ $errors->has('reason') ? 'has-error' : '' }}">
			<textarea name="reason" id="reason" class="form-control form-control-sm col-sm-12 @error('reason') is-invalid @enderror" placeholder="Reason">{{ old('reason') }}</textarea>
		</div>
	</div>

	<div class="row mt-3">
		<div class="col-md-12 text-center">
			<button type="submit" class="btn btn-sm btn-outline-secondary">Submit</button>
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
$('#customer_id').select2({
	placeholder: 'Please Choose',
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

		fields: {
			'staff_id[]': {
				validators: {
					notEmpty: {
						message: 'Please select a staff.'
					}
				}
			},

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
@endsection

@section('nonjquery')

@endsection
