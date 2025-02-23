@extends('layouts.app')

@section('content')
<div class="col-sm-12 row">
@include('humanresources.hrdept.navhr')
	<div class="row justify-content-center">

		<!-- Progress Bar -->
		<div id="progress-bar" class="progress" role="progressbar" aria-label="Loading Progress.." aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="display: none;">
			<div id="progress" class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%">0%</div>
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
/////////////////////////////////////////////////////////////////////////////////////////
$(document).ready(function () {

	// Show the progress bar
	$("#progress-bar").show();

	// Simulate progress (optional)
	let progress = 0;
	const progressInterval = setInterval(() => {
		progress += 5;
		console.log(progress);
		$('#progress-bar').attr('aria-valuenow', progress);
		$('#progress').css('width', progress + '%').html(progress +'%');
		if (progress >= 100) clearInterval(progressInterval);
	}, 300);


	$.ajax({
		url: "{{ route('staffdaily', ['_token' => csrf_token()]) }}",
		type: "POST",
		dataType: "json"
	})
	.done(function (data) {
			let summaryTable = $("#summary");

			$.each(data, function (index, value) {
					let row1 = `
							<tr>
									<td class="text-center">${value.date}</td>
									<td class="text-center">${value.working}</td>
									<td class="text-center">${value.overallpercentage}%</td>
									<td class="text-center">${value.workingpeople}</td>
									<td class="text-center" colspan="2">${value.outstation}</td>
									<td class="text-center" colspan="2">${value.leave}</td>
									<td class="text-center" colspan="2">${value.absent}</td>
									<td class="text-center" colspan="2">${value.halfabsent}</td>
									<td class="text-center">${value.workday}</td>
							</tr>`;

					let row2 = `
							<tr>
									<td class="text-center" colspan="4"></td>
									<td class="text-center" colspan="2">${formatLocations(value.locoutstation)}</td>
									<td class="text-center" colspan="2">${formatLocations(value.locationleave)}</td>
									<td class="text-center" colspan="2">${formatLocations(value.locationabsent)}</td>
									<td class="text-center" colspan="2">${formatLocations(value.locationhalfabsent)}</td>
									<td class="text-center"></td>
							</tr>`;

					summaryTable.append(row1 + row2);
			});

			renderChart(data);
	})
	.fail(function (jqXHR, textStatus, errorThrown) {
			console.error("AJAX Error:", textStatus, errorThrown);
	})
	.always(function () {
		// Hide the progress bar when the request is complete
		$("#progress-bar").hide();
		clearInterval(progressInterval); // Stop the progress simulation
	});

	function formatLocations(locations) {
			return $.isEmptyObject(locations) ? "" : Object.entries(locations).map(([k, v]) => `${k}: ${v}`).join("<br/>");
	}

	function renderChart(data) {
			new Chart(document.getElementById("myChart"), {
					type: "bar",
					data: {
							labels: data.map(row => row.date),
							datasets: [
									{
											type: "line",
											label: "Total Attendance Percentage By Day (%)",
											data: data.map(row => row.overallpercentage),
											tension: 0.3
									},
									{
											label: "Available Staff",
											data: data.map(row => row.workingpeople),
											backgroundColor: "rgba(75, 192, 192, 0.6)"
									},
									{
											label: "Outstation",
											data: data.map(row => row.outstation),
											backgroundColor: "rgba(255, 206, 86, 0.6)"
									},
									{
											label: "On Leave",
											data: data.map(row => row.leave),
											backgroundColor: "rgba(255, 99, 132, 0.6)"
									},
									{
											label: "Absents",
											data: data.map(row => row.absent),
											backgroundColor: "rgba(153, 102, 255, 0.6)"
									},
									{
											label: "Half Absents",
											data: data.map(row => row.halfabsent),
											backgroundColor: "rgba(54, 162, 235, 0.6)"
									},
									{
											label: "Total Staff",
											data: data.map(row => row.workday),
											backgroundColor: "rgba(201, 203, 207, 0.6)"
									}
							]
					},
					options: {
							responsive: true,
							scales: {
									y: { beginAtZero: true }
							},
							interaction: {
									intersect: false,
									mode: "index"
							},
							plugins: {
									legend: { position: "top" },
									title: { display: true, text: "Attendance Statistic Daily" }
							}
					}
			});
	}
});

@endsection
