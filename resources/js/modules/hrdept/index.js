const { route } = window.data;

$(document).ready(function () {

	// Show the progress bar
	$("#progress-bar").show();

	let progress = 0;
	const spark = $("#spark");
	const bar = $("#progress-bar");
	const progressEl = $("#progress");
	let intervalSpeed = 300;
	let progressInterval = setInterval(runProgress, intervalSpeed);
	function runProgress() {
		progress += 5;
		if (progress >= 95 && progress < 100) {
			// SLOW MOTION MODE
			clearInterval(progressInterval);
			intervalSpeed = 800; // slower
			progressInterval = setInterval(runProgress, intervalSpeed);
		}

		$('#progress-bar').attr('aria-valuenow', progress);
		progressEl.css('width', progress + '%').html(progress + '%');
		spark.css('left', progress + '%');
		createTrail(progress);
		if (progress >= 100) {
			clearInterval(progressInterval);
			dramaticExplosion();
		}
	}

	function createTrail(progress) {
		const trail = $('<div class="spark-trail"></div>');
		const barHeight = $("#progress-bar").height();
		trail.css({
			left: progress + '%',
			top: barHeight / 2 + (Math.random() * 10 - 5)
		});
		$("#progress-bar").append(trail);
		setTimeout(() => trail.remove(), 600);
	}

	function dramaticExplosion() {
		const boom = $('<div class="boom"></div>');
		const barHeight = $("#progress-bar").height();
		boom.css({
			left: '100%',
			top: barHeight / 2,
			transform: 'translate(-50%, -50%)'
		});
		$("#progress-bar").append(boom);
		$("#spark").fadeOut(200);

		// ⚡ Flash screen
		$("#flash").addClass("flash-active");
		// 💥 Screen shake
		$("body").addClass("shake");
		setTimeout(() => {
			$("#flash").removeClass("flash-active");
			$("body").removeClass("shake");
			boom.remove();
		}, 500);
	}

	$.ajax({
		url: route.staffdaily,
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
