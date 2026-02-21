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
		box-shadow:
		0 0 8px yellow,
		0 0 16px orange,
		0 0 24px red;
		pointer-events: none;
	}

/* trailing particles */
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
	0% {
		opacity: 1;
		transform: scale(1);
	}
	100% {
		opacity: 0;
		transform: translateY(-15px) scale(0.2);
	}
}

/* BOOM effect */
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
/* ===== FLASH EFFECT ===== */
#flash {
	position: fixed;
	inset: 0;
	background: white;
	opacity: 0;
	pointer-events: none;
	z-index: 9999;
}

/* ===== SCREEN SHAKE ===== */
@keyframes screenShake {
	0% { transform: translate(0px, 0px); }
	20% { transform: translate(-10px, 5px); }
	40% { transform: translate(10px, -5px); }
	60% { transform: translate(-8px, 4px); }
	80% { transform: translate(8px, -4px); }
	100% { transform: translate(0px, 0px); }
}

.shake {
	animation: screenShake 400ms ease-in-out;
}

/* ===== FLASH ANIMATION ===== */
@keyframes flashBang {
	0% { opacity: 0; }
	20% { opacity: 1; }
	100% { opacity: 0; }
}

.flash-active {
	animation: flashBang 400ms ease-out;
}
</style>
<div class="col-sm-12 row">
@include('humanresources.hrdept.navhr')
	<div class="row justify-content-center">

		<!-- Progress Bar -->
		<div id="progress-bar" class="progress" role="progressbar"
		aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
		style="display: none; position: relative; overflow: visible;">

		<div id="flash"></div>

		<div id="progress"
		class="rounded-5 progress-bar progress-bar-striped progress-bar-animated"
		style="width: 0%">
		0%
	</div>

	<!-- Fuse Spark -->
	<div id="spark"></div>
</div>

		<div class="col-sm-12 m-3">
			<h4>Overall Summary</h4>
			<div class="table-responsive">
				<table class="table table-hover table-sm align-middle table-border" style="font-size:12px">
					<thead>
						<tr>
							<th class="text-center">Date</th>
							<th class="text-center">Day Status</th>
							<th class="text-center">Percentage</th>
							<th class="text-center">Available Staff</th>
							<th class="text-center" colspan="2">Outstation</th>
							<th class="text-center" colspan="2">On Leave</th>
							<th class="text-center" colspan="2">Absents</th>
							<th class="text-center" colspan="2">Half Absents</th>
							<th class="text-center">Total Staff</th>
						</tr>
					</thead>
					<tbody id="summary">
					</tbody>
				</table>
			</div>
		</div>
		<div class="col-sm-12 m-3">
			<canvas id="myChart" width="200" height="75"></canvas>
		</div>
	</div>
</div>
@endsection

@section('js')
window.data = {
	route: {
		staffdaily: "{{ route('staffdaily') }}",
	},
};

@endsection
