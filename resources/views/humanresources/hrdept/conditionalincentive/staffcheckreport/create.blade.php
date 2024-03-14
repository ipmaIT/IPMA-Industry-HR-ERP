@extends('layouts.app')

@section('content')
<div class="container row align-items-start justify-content-center">
	@include('humanresources.hrdept.navhr')
	<h2>Report of Staff Checking Incentive</h2>

	<div class="hstack align-items-start justify-content-between">
		<div class="col-sm-12">
			{{ Form::open(['route' => ['cicategorystaffcheckreport.store'], 'method' => 'POST', 'id' => 'form', 'files' => true]) }}

			<div class="form-group hstack @error('date_from') has-error is-invalid @enderror">
				{{ Form::label( 'week1', 'From Week : ', ['class' => 'col-sm-2 col-form-label'] ) }}
				<div class="col-sm-4 align-items-center">
					<select name="date_from" id="week1" class="form-select form-select-sm @error('date_from') is-invalid @enderror"></select>
					@error('date_from') <div id="week1a" class="invalid-feedback">{{ $message }}</div> @enderror
				</div>
			</div>

			<div class="form-group hstack @error('date_to') has-error is-invalid @enderror">
				{{ Form::label( 'week2', 'To Week : ', ['class' => 'col-sm-2 col-form-label'] ) }}
				<div class="col-sm-4 align-items-center">
					<select name="date_to" id="week2" class="form-select form-select-sm @error('date_to') is-invalid @enderror"></select>
					@error('date_to') <div id="week2a" class="invalid-feedback">{{ $message }}</div> @enderror
				</div>
			</div>

			<div class="offset-sm-2 col-sm-auto mt-3 ">
				<button type="submit" class="btn btn-sm btn-outline-secondary">Submit</button>
			</div>

			{{ Form::close() }}
		</div>
	</div>

<?php
use Illuminate\Http\Request;
?>
@if( request()->id || session()->exists('lastBatchIdPay') )
	<p>&nbsp</p>
	<div id="processcsv" class="row col-sm-12">
		<div class="progress col-sm-12" role="progressbar" aria-label="CSV Processing" aria-valuenow="{{ $batch->progress() }}" aria-valuemin="0" aria-valuemax="100">
			<div class="col-sm-auto progress-bar csvprogress" style="width: 0%">0% CSV Processing</div>
		</div>
	</div>
	<div id="uploadStatus" class="col-sm-auto ">
		<span id="processedJobs">{{ $batch->processedJobs() }}</span> completed out of {{ $batch->totalJobs }} process
	</div>
@endif
</div>
@endsection

@section('js')
/////////////////////////////////////////////////////////////////////////////////////////
$('#week1,#week2').select2({
	placeholder: 'Please choose',
	allowClear: true,
	closeOnSelect: true,
	width: '100%',
	ajax: {
		url: '{{ route('week_dates') }}',
		type: 'POST',
		dataType: 'json',
		data: function (params) {
			var query = {
				_token: '{!! csrf_token() !!}',
				search: params.term,
			}
			return query;
		}
	},
});

/////////////////////////////////////////////////////////////////////////////////////////
@if( request()->id || session()->exists('lastBatchIdPay') )
	<?php
	$batchId = $request->id ?? session()->get('lastBatchIdPay');
	?>
	setInterval(percent, 500);
	function percent() {
		$.ajax({
			url: '{{ route('progress', ['id' => $batchId]) }}',
			type: "GET",
			data: { _token: '{{ csrf_token() }}'},
			dataType: 'json',
			success: function (response) {
				window.percentbar = response.progress;
				$('.progress').attr('aria-valuenow', percentbar).css('width', percentbar + '%');
				$(".csvprogress").width(percentbar + '%');
				$(".csvprogress").html(percentbar +'%');
				$('#processedJobs').html(response.processedJobs);
				console.log(percentbar);
				if (percentbar == 100) {
					clearInterval(percent);
					window.location.replace('{{ route('cicategorystaffcheckreport.create') }}');
					<?php
					session()->forget('lastBatchIdPay');
					?>
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log(textStatus, errorThrown);
			}
		})
	}
@endif
/////////////////////////////////////////////////////////////////////////////////////////
// bootstrap validator
$('#form').bootstrapValidator({
	feedbackIcons: {
		valid: '',
		invalid: '',
		validating: ''
	},
	fields: {
		'week_id': {
			validators: {
				notEmpty: {
					message: 'Please choose '
				},
			}
		},
	}
});
@endsection
