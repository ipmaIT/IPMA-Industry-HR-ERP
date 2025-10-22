@extends('layouts.app')

@section('content')
<?php
use \App\Models\HumanResources\OptWorkingHour;

use \Carbon\Carbon;
?>

<div class="col-sm-12 row">
	@include('humanresources.hrdept.navhr')
	<h4>Edit Maternity Leave Entitlement Year {{ $maternityleave->year }} for {{ $maternityleave->belongstostaff->name }}</h4>
	<form method="POST" action="{{ route('maternityleave.update', $maternityleave) }}" accept-charset="UTF-8" id="form" autocomplete="off" class="" enctype="multipart/form-data">
		@csrf
		@method('PATCH')

		<div class="form-group row {{ $errors->has('maternity_leave') ? 'has-error' : '' }} mb-3 g-3">
			<label for="alt" class="col-form-label col-sm-3">Maternity Leave : </label>
			<div class=" col-sm-2">
				<input type="text" name="maternity_leave" value="{{ old('maternity_leave', $maternityleave->maternity_leave) }}" id="alt" class="form-control form-control-sm col-sm-12 @error('maternity_leave') is-invalid @enderror" placeholder="Maternity Leave Initialize">
			</div>
		</div>

		<div class="form-group row {{ $errors->has('maternity_leave_adjustment') ? 'has-error' : '' }} mb-3 g-3">
			<label for="ala" class="col-form-label col-sm-3">Maternity Leave Adjustment : </label>
			<div class=" col-sm-2">
				<input type="text" name="maternity_leave_adjustment" value="{{ old('maternity_leave_adjustment', $maternityleave->maternity_leave_adjustment) }}" id="ala" class="form-control form-control-sm col-sm-12 @error('maternity_leave_adjustment') is-invalid @enderror" placeholder="Maternity Leave Adjustment">
			</div>
		</div>

		<div class="form-group row {{ $errors->has('maternity_leave_utilize') ? 'has-error' : '' }} mb-3 g-3">
			<label for="alu" class="col-form-label col-sm-3">Maternity Leave Utilize : </label>
			<div class=" col-sm-2">
				<input type="text" name="maternity_leave_utilize" value="{{ old('maternity_leave_utilize', $maternityleave->maternity_leave_utilize) }}" id="alu" class="form-control form-control-sm col-sm-12 @error('maternity_leave_utilize') is-invalid @enderror" placeholder="Maternity Leave Utilize">
			</div>
		</div>

		<div class="form-group row {{ $errors->has('maternity_leave_balance') ? 'has-error' : '' }} mb-3 g-3">
			<label for="alb" class="col-form-label col-sm-3">Maternity Leave Balance : </label>
			<div class=" col-sm-2">
				<input type="text" name="maternity_leave_balance" value="{{ old('maternity_leave_balance', $maternityleave->maternity_leave_balance) }}" id="alb" class="form-control form-control-sm col-sm-12 @error('maternity_leave_balance') is-invalid @enderror" placeholder="Maternity Leave Balance">
			</div>
		</div>

		<div class="form-group row {{ $errors->has('remarks') ? 'has-error' : '' }} mb-3 g-3">
			<label for="rem" class="col-form-label col-sm-3">Remarks : </label>
			<div class=" col-sm-4">
				<textarea name="remarks" id="rem" class="form-control form-control-sm col-sm-12 @error('remarks') is-invalid @enderror" placeholder="Remarks">{{ old('remarks', $maternityleave->remarks) }}</textarea>
			</div>
		</div>

		<div class="form-group row mb-3 g-3">
			<div class="col-sm-10 offset-sm-3">
				<button type="submit" class="btn btn-sm btn-outline-secondary">Submit</button>
			</div>
		</div>

	</form>

</div>
@endsection

@section('js')
/////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////
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
