@extends('layouts.app')

@section('content')
<?php
use \App\Models\HumanResources\HRLeaveMC;

use \Carbon\Carbon;
?>

<div class="col-sm-12 row">
	@include('humanresources.hrdept.navhr')
	<h4>Edit Medical Certificate Leave Entitlement Year {{ $mcleave->year }} for {{ $mcleave->belongstostaff->name }}</h4>
	<form method="POST" action="{{ route('mcleave.update', $mcleave) }}" accept-charset="UTF-8" id="form" autocomplete="off" class="" enctype="multipart/form-data">
		@csrf
		@method('PATCH')

		<div class="form-group row {{ $errors->has('mc_leave') ? 'has-error' : '' }} mb-3 g-3">
			<label for="alt" class="col-form-label col-sm-3">Medical Certificate Leave : </label>
			<div class=" col-sm-2">
				<input type="text" name="mc_leave" value="{{ old('mc_leave', $mcleave->mc_leave) }}" id="alt" class="form-control form-control-sm col-sm-12 @error('mc_leave') is-invalid @enderror" placeholder="Medical Certificate Leave Initialize">
			</div>
		</div>

		<div class="form-group row {{ $errors->has('mc_leave_adjustment') ? 'has-error' : '' }} mb-3 g-3">
			<label for="ala" class="col-form-label col-sm-3">Medical Certificate Leave Adjustment : </label>
			<div class=" col-sm-2">
				<input type="number" name="mc_leave_adjustment" value="{{ old('mc_leave_adjustment', $mcleave->mc_leave_adjustment) }}" id="ala" class="form-control form-control-sm col-sm-12 @error('mc_leave_adjustment') is-invalid @enderror" step="0.5" placeholder="Medical Certificate Leave Adjustment">
			</div>
		</div>

		<div class="form-group row {{ $errors->has('mc_leave_utilize') ? 'has-error' : '' }} mb-3 g-3">
			<label for="alu" class="col-form-label col-sm-3">Medical Certificate Leave Utilize : </label>
			<div class=" col-sm-2">
				<input type="text" name="mc_leave_utilize" value="{{ old('mc_leave_utilize', $mcleave->mc_leave_utilize) }}" id="alu" class="form-control form-control-sm col-sm-12 @error('mc_leave_utilize') is-invalid @enderror" placeholder="Medical Certificate Leave Utilize">
			</div>
		</div>

		<div class="form-group row {{ $errors->has('mc_leave_balance') ? 'has-error' : '' }} mb-3 g-3">
			<label for="alb" class="col-form-label col-sm-3">Medical Certificate Leave Balance : </label>
			<div class=" col-sm-2">
				<input type="text" name="mc_leave_balance" value="{{ old('mc_leave_balance', $mcleave->mc_leave_balance) }}" id="alb" class="form-control form-control-sm col-sm-12 @error('mc_leave_balance') is-invalid @enderror" placeholder="Medical Certificate Leave Balance">
			</div>
		</div>

		<div class="form-group row {{ $errors->has('remarks') ? 'has-error' : '' }} mb-3 g-3">
			<label for="rem" class="col-form-label col-sm-3">Remarks : </label>
			<div class=" col-sm-4">
				<textarea name="remarks" id="rem" class="form-control form-control-sm col-sm-12 @error('remarks') is-invalid @enderror" placeholder="Remarks">{{ old('remarks', $mcleave->remarks) }}</textarea>
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
	var balance = (($(this).val() * 100)/100) + {{ $mcleave->mc_leave_balance }};
	$('#alb').val(balance);
});

/////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////
// validator
$(document).ready(function() {
	$('#form').bootstrapValidator({
		fields: {
			mc_leave: {
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
			mc_leave_adjustment: {
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
			mc_leave_utilize: {
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
			mc_leave_balance: {
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
