$('#to').datetimepicker({
	icons: {
		time: "fas fas-regular fa-clock fa-beat",
		date: "fas fas-regular fa-calendar fa-beat",
		up: "fas fas-regular fa-arrow-up fa-beat",
		down: "fas fas-regular fa-arrow-down fa-beat",
		previous: 'fas fas-regular fa-arrow-left fa-beat',
		next: 'fas fas-regular fa-arrow-right fa-beat',
		today: 'fas fas-regular fa-calenday-day fa-beat',
		clear: 'fas fas-regular fa-broom-wide fa-beat',
		close: 'fas fas-regular fa-rectangle-xmark fa-beat'
	},
	format:'YYYY-MM-DD',
	useCurrent: false,
	disabledDates:data4,
	// minDate: moment().format('YYYY-MM-DD'),
	// daysOfWeekDisabled: [0],
})