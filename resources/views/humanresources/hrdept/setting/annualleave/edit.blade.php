@extends('layouts.app')

@section('content')
<?php
use \App\Models\HumanResources\OptWorkingHour;

use \Carbon\Carbon;
?>

<div class="col-sm-12 row">
	@include('humanresources.hrdept.navhr')
	<h4>Edit Annual Leave Entitlement Year {{ $annualleave->year }} for {{ $annualleave->belongstostaff->name }}</h4>
	<form method="POST" action="{{ route('annualleave.update', $annualleave) }}" accept-charset="UTF-8" id="form" autocomplete="off" class="" enctype="multipart/form-data">
		@csrf
		@method('PATCH')

		<div class="form-group row {{ $errors->has('annual_leave') ? 'has-error' : '' }} mb-3 g-3">
			<label for="alt" class="col-form-label col-sm-3">Annual Leave : </label>
			<div class=" col-sm-2">
				<input type="text" name="annual_leave" value="{{ old('annual_leave', $annualleave->annual_leave) }}" id="alt" class="form-control form-control-sm col-sm-12 @error('annual_leave') is-invalid @enderror" placeholder="Annual Leave Initialize">
			</div>
		</div>

		<div class="form-group row {{ $errors->has('annual_leave_adjustment') ? 'has-error' : '' }} mb-3 g-3">
			<label for="ala" class="col-form-label col-sm-3">Annual Leave Adjustment : </label>
			<div class=" col-sm-2">
				<input type="number" name="annual_leave_adjustment" value="{{ old('annual_leave_adjustment', $annualleave->annual_leave_adjustment) }}" id="ala" class="form-control form-control-sm col-sm-12 @error('annual_leave_adjustment') is-invalid @enderror" placeholder="Annual Leave Adjustment" step="0.5">
			</div>
		</div>

		<div class="form-group row {{ $errors->has('annual_leave_utilize') ? 'has-error' : '' }} mb-3 g-3">
			<label for="alu" class="col-form-label col-sm-3">Annual Leave Utilize : </label>
			<div class=" col-sm-2">
				<input type="text" name="annual_leave" value="{{ old('annual_leave', $annualleave->annual_leave) }}" id="alu" class="form-control form-control-sm col-sm-12 @error('annual_leave') is-invalid @enderror" placeholder="Annual Leave Initialize">
			</div>
		</div>

		<div class="form-group row {{ $errors->has('annual_leave_balance') ? 'has-error' : '' }} mb-3 g-3">
			<label for="alb" class="col-form-label col-sm-3">Annual Leave Balance : </label>
			<div class=" col-sm-2">
				<input type="text" name="annual_leave" value="{{ old('annual_leave', $annualleave->annual_leave) }}" id="alb" class="form-control form-control-sm col-sm-12 @error('annual_leave') is-invalid @enderror" placeholder="Annual Leave Initialize">
			</div>
		</div>

		<div class="form-group row {{ $errors->has('remarks') ? 'has-error' : '' }} mb-3 g-3">
			<label for="rem" class="col-form-label col-sm-3">Remarks : </label>
			<div class=" col-sm-4">
				<textarea name="remarks" id="rem" class="form-control form-control-sm col-sm-12 @error('remarks') is-invalid @enderror" placeholder="Remarks">{{ old('remarks', $annualleave->remarks) }}</textarea>
			</div>
		</div>



		<div class="form-group row  mb-3 g-3">
			<div class="col-sm-10 offset-sm-3">
				<button type="submit" class="btn btn-sm btn-outline-secondary">Submit</button>
			</div>
		</div>

	</form>

</div>
@endsection

@section('js')
/////////////////////////////////////////////////////////////////////////////////////////
// counting annual leave
$(document).on('keyup mouseup', '#ala', function () {
	let adjustment = parseFloat($(this).val()) || 0;
	let currentBalance = parseFloat({{ $annualleave->annual_leave_balance }}) || 0;
	let newBalance = currentBalance + adjustment;
	$('#alb').val(newBalance.toFixed(1));
});

/////////////////////////////////////////////////////////////////////////////////////////
// validator
$(document).ready(function() {
	$('#form').bootstrapValidator({
		feedbackIcons: {
			valid: '',
			invalid: '',
			validating: ''
		},
		fields: {
			annual_leave: {
				validators: {
					notEmpty: {
						message: 'Please insert number with/out decimal. ',
					},
					numeric: {
						separator: '.',
						message: 'Use DOT (.) as separator. '
					}
				}
			},
			annual_leave_adjustment: {
				validators: {
					notEmpty: {
						message: 'Please insert number with/out decimal. ',
					},
					numeric: {
						separator: '.',
						message: 'Use DOT (.) as separator. '
					}
				}
			},
			annual_leave_utilize: {
				validators: {
					notEmpty: {
						message: 'Please insert number with/out decimal. ',
					},
					numeric: {
						separator: '.',
						message: 'Use DOT (.) as separator. '
					}
				}
			},
			annual_leave_balance: {
				validators: {
					notEmpty: {
						message: 'Please insert number with/out decimal. ',
					},
					numeric: {
						separator: '.',
						message: 'Use DOT (.) as separator. '
					}
				}
			},
			remarks: {
				validators: {
					// notEmpty: {
					// 	message: 'Please type some remarks',
					// },
				}
			},
		}
	})
	// .find('[name="reason"]')
	// .ckeditor()
	// .editor
	// 	.on('change', function() {
	// 		// Revalidate the bio field
	// 	$('#form').bootstrapValidator('revalidateField', 'reason');
	// 	// console.log($('#reason').val());
	// })
	;
});
@endsection
