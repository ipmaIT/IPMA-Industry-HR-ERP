@extends('layouts.app')

@section('content')
<style>
	#spark {
		position: absolute;
		top: 50%;
		left: 0%;
		width: 10px;
		height: 10px;
		background: radial-gradient(circle, #fff 0%, yellow 40%, orange 70%, red 100%);
		border-radius: 50%;
		transform: translate(-50%, -50%);
		box-shadow: 0 0 8px yellow, 0 0 16px orange, 0 0 24px red;
		pointer-events: none;
	}

	.spark-trail {
		position: absolute;
		width: 6px;
		height: 6px;
		background: orange;
		border-radius: 50%;
		pointer-events: none;
		animation: fadeOut 600ms linear forwards;
	}

	@keyframes fadeOut {
		0% { opacity: 1; transform: scale(1); }
		100% { opacity: 0; transform: translateY(-15px) scale(0.2); }
	}

	.boom {
		position: absolute;
		width: 20px;
		height: 20px;
		border-radius: 50%;
		background: radial-gradient(circle, white, yellow, orange, red);
		animation: explode 700ms ease-out forwards;
		pointer-events: none;
	}

	@keyframes explode {
		0% { transform: scale(0.5); opacity: 1; }
		70% { transform: scale(3); opacity: 1; }
		100% { transform: scale(4); opacity: 0; }
	}

	#flash {
		position: fixed;
		inset: 0;
		background: white;
		opacity: 0;
		pointer-events: none;
		z-index: 9999;
	}

	@keyframes screenShake {
		0% { transform: translate(0px, 0px); }
		20% { transform: translate(-10px, 5px); }
		40% { transform: translate(10px, -5px); }
		60% { transform: translate(-8px, 4px); }
		80% { transform: translate(8px, -4px); }
		100% { transform: translate(0px, 0px); }
	}

	.shake { animation: screenShake 400ms ease-in-out; }

	@keyframes flashBang {
		0% { opacity: 0; }
		20% { opacity: 1; }
		100% { opacity: 0; }
	}

	.flash-active { animation: flashBang 400ms ease-out; }
</style>
<div class="container justify-content-center align-items-start">
@include('humanresources.hrdept.navhr')
	<h4 class="align-items-start">Generate Payslip Excel Report</h4>
	<div class="row justify-content-center">
		<div class="col-sm-6">
		  <form method="POST" action="{{ route('excelreport.store') }}" accept-charset="UTF-8" id="form" autocomplete="off" class="form-horizontal" enctype="multipart/form-data">
		    @csrf
			<div class="form-group row mb-3 {{ $errors->has('from') ? 'has-error' : '' }}">
				<label for="from1" class="col-sm-4 col-form-label">From : </label>
				<div class="col-sm-8" style="position:relative;">
					<input type="text" name="from" value="{{ old('from') }}" id="from1" class="form-control form-control-sm col-auto @error('from') is-invalid @enderror" placeholder="From">
				</div>
			</div>
			<div class="form-group row mb-3 {{ $errors->has('to') ? 'has-error' : '' }}">
				<label for="to1" class="col-sm-4 col-form-label">To : </label>
				<div class="col-sm-8" style="position:relative;">
					<input type="text" name="to" value="{{ old('to') }}" id="to1" class="form-control form-control-sm col-auto @error('to') is-invalid @enderror" placeholder="To">
				</div>
			</div>
			<div class="col-sm-12 offset-4 mb-6">
				<button type="submit" class="btn btn-sm btn-outline-secondary">Generate Excel</button>
			</div>
			</form>
		</div>
	</div>
<?php
use Illuminate\Http\Request;
?>
@if( isset(request()->id) || session()->exists('lastBatchId') )
	<p>&nbsp</p>
	<div id="processcsv" class="row col-sm-12">
		<div id="flash"></div>
		<div class="progress col-sm-12" style="height: 30px; position: relative; overflow: visible;" role="progressbar" aria-label="CSV Processing" aria-valuenow="{{ $batch->progress() }}" aria-valuemin="0" aria-valuemax="100">
			<div class="progress-bar progress-bar-striped progress-bar-animated csvprogress rounded-5" style="width: {{ $batch->progress() }}%">
				{{ $batch->progress() }}% Processing
			</div>
			
			<div id="spark" style="left: {{ $batch->progress() }}%"></div>
		</div>
	</div>
	<div id="uploadStatus" class="col-sm-auto ">
		<span id="processedJobs">{{ $batch->processedJobs() }}</span> completed out of {{ $batch->totalJobs }} process
	</div>
@endif
</div>
@endsection

@section('js')
$('#from1').datetimepicker({
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
	useCurrent: false,
	maxDate: moment().subtract(1, 'days').format('YYYY-MM-DD'),
})
.on('dp.change dp.update', function(e) {
	$('#form').bootstrapValidator('revalidateField', "from");
	$('#to1').datetimepicker('minDate', $('#from1').val());
});

$('#to1').datetimepicker({
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
	useCurrent: false,
	maxDate: moment().subtract(1, 'days').format('YYYY-MM-DD'),
})
.on('dp.change dp.update', function(e) {
	$('#form').bootstrapValidator('revalidateField', "to");
	$('#from1').datetimepicker('maxDate', $('#to1').val());
});

/////////////////////////////////////////////////////////////////////////////////////////
@if( isset(request()->id) || session()->exists('lastBatchId') )
	<?php
	$batchId = $request->id ?? session()->get('lastBatchId');
	?>
	window.percentbar = {{ isset($batch) ? $batch->progress() : 0 }};
	
	setInterval(() => {
		if (window.percentbar > 0 && window.percentbar < 100) {
			let trail = $('<div class="spark-trail"></div>');
			let offsetX = (Math.random() - 0.5) * 10;
			let offsetY = (Math.random() - 0.5) * 10;
			trail.css({
				left: `calc(${window.percentbar}% + ${offsetX}px)`,
				top: `calc(50% + ${offsetY}px)`,
			});
			$('.progress').append(trail);
			setTimeout(() => trail.remove(), 600);
		}
	}, 50);

	var percentInterval = setInterval(percent, 5000);
	function percent() {
		$.ajax({
			url: '{{ route('progress', ['id' => $batchId], false) }}',
			type: "GET",
			data: { _token: '{{ csrf_token() }}'},
			cache: false,
			dataType: 'json',
			success: function (response) {
				window.percentbar = response.progress;
				$('.progress').attr('aria-valuenow', percentbar);
				$(".csvprogress").css('width', percentbar + '%');
				$(".csvprogress").html(percentbar +'% Processing');
				$('#spark').css('left', percentbar + '%');
				$('#processedJobs').html(response.processedJobs);
				console.log(percentbar);
				if (percentbar >= 100) {
					window.percentbar = 100;
					clearInterval(percentInterval);
					
					let boom = $('<div class="boom"></div>');
					boom.css({ left: '100%', top: '50%', transform: 'translate(-50%, -50%)' });
					$('.progress').append(boom);
					$('#flash').addClass('flash-active');
					$('body').addClass('shake');

					setTimeout(() => {
						window.location.replace('{{ route('excelreport.create') }}');
					}, 800);
					
					<?php
					session()->forget('lastBatchId');
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
$(document).ready(function() {
	// Add loading state to button submit
	$('#form').on('submit', function() {
		if ($('#form').data('bootstrapValidator').isValid()) {
			var submitBtn = $(this).find('button[type="submit"]');
			submitBtn.prop('disabled', true);
			submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Generating... Please wait');
		}
	});

	$('#form').bootstrapValidator({
		feedbackIcons: {
			valid: 'fas fa-light fa-check',
			invalid: 'fas fa-sharp fa-light fa-xmark',
			validating: 'fas fa-duotone fa-spinner-third'
		},
		fields: {
			from: {
				validators: {
					notEmpty: {
						message: 'Please insert date '
					},
					date: {
						format: 'YYYY-MM-DD',
						message: 'Invalid date '
					},
				}
			},
			to: {
				validators: {
					notEmpty: {
						message: 'Please insert date '
					},
					date: {
						format: 'YYYY-MM-DD',
						message: 'Invalid date '
					},
				}
			},
		}
	})
});
@endsection
