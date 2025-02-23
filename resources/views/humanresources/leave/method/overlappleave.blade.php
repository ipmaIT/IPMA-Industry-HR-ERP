// checking for overlapp leave on half day (if it is turn on)
var data10 = $.ajax({
	url: "{{ route('unblockhalfdayleave.unblockhalfdayleave') }}",
	type: "POST",
	data: {
			id: {{ \Auth::user()->belongstostaff->id }},
			_token: '{!! csrf_token() !!}',
		},
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

// convert data10 into json
var objtime = $.parseJSON( data10 );