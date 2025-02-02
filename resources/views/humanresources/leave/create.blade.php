@extends('layouts.app')
@section('content')
<div class="container row align-items-start justify-content-center">
	<div class="table-responsive col-sm-12 m-5">
		<table class="table table-hover table-sm">
			<tbody>
				<tr class="">
					<td rowspan="3" class="text-danger w-25">Attention :</td>
					<td>
						Leave application must be at least <span class="font-weight-bold">THREE (3)</span> days in advance for <strong>"Annual Leave"</strong> and <strong>"Unpaid Leave"</strong>. Otherwise it will be considered as <strong>"Emergency Annual Leave"</strong> or <strong>"Emergency Unpaid Leave"</strong>
					</td>
				</tr>
				<tr>
					<td>
            <strong>"Time-Off"</strong> will consider as a <strong>"Leave"</strong>, if leave period exceed <strong>more than 2 hours</strong>.
          </td>
				</tr>
				<tr>
					<td>
						Application for <strong>"Sick Leave/Medical Certificate (MC)"</strong> or <strong>"Unpaid Medical Certificate (MC-UPL)"</strong> will only be <strong>considered VALID and ELIGIBLE</strong> if a sick/medical certificate is <strong>issued by a REGISTERED government hospital/clinic or panel clinic only.
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<!-- herecomes the hardest part, leave application -->

	<div class="col-sm-12 row">
		{{ Form::open(['route' => ['leave.store'], 'id' => 'form', 'autocomplete' => 'off', 'files' => true,  'data-toggle' => 'validator']) }}
		<h5 class="text-center">Leave Application</h5>

		<div class="form-group row m-2 {{ $errors->has('leave_id') ? 'has-error' : '' }}">
			{{ Form::label( 'leave_type_id', 'Leave Type : ', ['class' => 'col-sm-4 col-form-label'] ) }}
			<div class="col-sm-8">
				<select name="leave_type_id" id="leave_id" class="form-control form-control-sm"></select>
			</div>
		</div>

		<div class="form-group row m-2 {{ $errors->has('reason') ? 'has-error' : '' }}">
			{{ Form::label( 'reason', 'Reason : ', ['class' => 'col-sm-4 col-form-label'] ) }}
			<div class="col-sm-8">
				{{ Form::textarea('reason', @$value, ['class' => 'form-control form-control-sm ', 'id' => 'reason', 'placeholder' => 'Reason', 'autocomplete' => 'off']) }}
			</div>
		</div>

		<div class="m-2" id="wrapper">
		</div>

		<div class="form-group row m-2 {{ $errors->has('akuan') ? 'has-error' : '' }}">
			<div class="col-sm-8 offset-sm-4 form-check">
				{{ Form::checkbox('akuan', 1, @$value, ['class' => 'form-check-input ', 'id' => 'akuan1']) }}
				<label for="akuan1" class="form-check-label p-1 bg-warning text-danger rounded">
					<p class="my-auto">I hereby confirmed that all details and information filled in are <strong>CORRECT</strong> and <strong>CHECKED</strong> before sending.</p>
				</label>
			</div>
		</div>

		<div class="form-group row mb-3">
			<div class="col-sm-8 offset-sm-4">
				{!! Form::button('Submit Application', ['class' => 'btn btn-sm btn-primary', 'type' => 'submit']) !!}
			</div>
		</div>
		{{ Form::close() }}
	</div>
</div>

@endsection
@section('js')
/////////////////////////////////////////////////////////////////////////////////////////
$('#leave_id').select2({
	placeholder: 'Please choose',
	allowClear: true,
	closeOnSelect: true,
	width: '100%',
	ajax: {
		url: '{{ route('leaveType.leaveType') }}',
		// data: { '_token': '{!! csrf_token() !!}' },
		type: 'POST',
		dataType: 'json',
		data: function () {
			var data = {
				id: {{ \Auth::user()->belongstostaff->id }},
				_token: '{!! csrf_token() !!}',
			}
			return data;
		}
	},
});

/////////////////////////////////////////////////////////////////////////////////////////
// start setting up the leave accordingly.
<?php
$user = \Auth::user()->belongstostaff;
$userneedbackup = $user->belongstoleaveapprovalflow?->backup_approval;
$setHalfDayMC = \App\Models\Setting::find(2)->active;
?>

/////////////////////////////////////////////////////////////////////////////////////////
//  global variable : ajax to get the unavailable date
@include('humanresources.leave.method.get_unavailabledate')

/////////////////////////////////////////////////////////////////////////////////////////
// checking for overlapp leave on half day (if it is turn on)
@include('humanresources.leave.method.overlappleave')

function obj1(datenow) {
	var data1 = $.ajax({
		url: "{{ route('leavedate.timeleave') }}",
		type: "POST",
		data: {date: datenow, _token: '{!! csrf_token() !!}', id: {{ \Auth::user()->belongstostaff->id }} },
		dataType: 'json',
		global: false,
		async:false,
		success: function (response) {
			// you will get response from your php page (what you echo or print)
			return response;
		},
		error: function(jqXHR, textStatus, errorThrown) {
			console.log(textStatus, errorThrown);
		}
	}).responseText;

	// convert data1 into json
	return obj = $.parseJSON( data1 );
}

/////////////////////////////////////////////////////////////////////////////////////////
// start here when user start to select the leave type option
$('#leave_id').on('change', function() {
	$selection = $(this).find(':selected');
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// annual leave & UPL
	if ($selection.val() == '1' || $selection.val() == '3') {
		@include('humanresources.leave.partial.annualleaveupl')
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if ($selection.val() == '2') {
		@include('humanresources.leave.partial.mc')
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// replacement leave
<?php
$oi = \Auth::user()->belongstostaff->hasmanyleavereplacement()->where('leave_balance', '<>', 0)->get();
?>
	if ($selection.val() == '4') {
		@include('humanresources.leave.partial.replacementleave')
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// maternity leave
	if ($selection.val() == '7') {
		@include('humanresources.leave.partial.maternityleave')
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// el-al and el-upl
	if ($selection.val() == '5' || $selection.val() == '6') {
		@include('humanresources.leave.partial.elalelupl')
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// time off
if ($selection.val() == '9') {
		@include('humanresources.leave.partial.tf')
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// mc-upl
	if ($selection.val() == '11') {
		@include('humanresources.leave.partial.elmc')
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// el replacement leave
<?php
$oi = \Auth::user()->belongstostaff->hasmanyleavereplacement()->where('leave_balance', '<>', 0)->get();
?>
	if ($selection.val() == '10') {
		@include('humanresources.leave.partial.elreplacementleave')
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// S-UPL
	if ($selection.val() == '12') {
		@include('humanresources.leave.partial.supl')
	});
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// validator
$(document).ready(function() {
	$('#form').bootstrapValidator({
		feedbackIcons: {
			valid: '',
			invalid: '',
			validating: ''
		},
		fields: {
			leave_type_id: {
				validators: {
					notEmpty: {
						message: 'Please choose'
					},
				}
			},
			reason: {
				validators: {
					notEmpty: {
						message: 'Please insert your reason'
					},
					callback: {
						message: 'The reason must be less than 200 characters long',
						callback: function(value, validator, $field) {
							var div  = $('<div/>').html(value).get(0),
							text = div.textContent || div.innerText;
							return text.length <= 200;
						},
					},
				}
			},
			akuan: {
				validators: {
					notEmpty: {
						message: 'Please click this as an acknowledgement'
					}
				}
			},
			date_time_start: {
				validators: {
					notEmpty : {
						message: 'Please insert date start'
					},
					date: {
						format: 'YYYY-MM-DD',
						message: 'The value is not a valid date. '
					},
				}
			},
			date_time_end: {
				validators: {
					notEmpty : {
						message: 'Please insert date end'
					},
					date: {
						format: 'YYYY-MM-DD',
						message: 'The value is not a valid date. '
					},
				}
			},
			time_start: {
				validators: {
					notEmpty: {
						message: 'Please insert time',
					},
					regexp: {
						regexp: /^([1-6]|[8-9]|1[0-2]):([0-5][0-9])\s([A|P]M|[a|p]m)$/i,
						message: 'The value is not a valid time',
					}
				}
			},
			time_end: {
				validators: {
					notEmpty: {
						message: 'Please insert time',
					},
					regexp: {
						regexp: /^([1-6]|[8-9]|1[0-2]):([0-5][0-9])\s([A|P]M|[a|p]m)$/i,
						message: 'The value is not a valid time',
					}
				}
			},
			leave_id: {
				validators: {
					notEmpty: {
						message: 'Please select 1 option',
					},
				}
			},
			staff_id: {
				validators: {
					notEmpty: {
						message: 'Please choose'
					}
				}
			},
			document: {
				validators: {
					file: {
						extension: 'jpeg,jpg,png,bmp,pdf,doc,docx',											// no space
						type: 'image/jpeg,image/png,image/bmp,application/pdf,application/msword',			// no space
						maxSize: 5242880,	// 5120 * 1024,
						message: 'The selected file is not valid. Please use jpeg, jpg, png, bmp, pdf or doc and the file is below than 5MB. '
					},
				}
			},
			//container: '.suppdoc',
			documentsupport: {
				validators: {
					notEmpty: {
						message: 'Please click this as an aknowledgement.'
					},
				}
			},
		}
	})
	.find('[name="reason"]')
	// .ckeditor()
	// .editor
		.on('change', function() {
			// Revalidate the bio field
		$('#form').bootstrapValidator('revalidateField', 'reason');
		// console.log($('#reason').val());
	})
	;
});

/////////////////////////////////////////////////////////////////////////////////////////
@endsection
