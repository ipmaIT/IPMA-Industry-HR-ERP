//  global variable : ajax to get the unavailable date

var data2 = $.ajax({
	url: "{{ route('leavedate.unavailabledate') }}",
	type: "POST",
	data : {
				id: {{ \Auth::user()->belongstostaff->id }},
				type: 1,
				_token: '{!! csrf_token() !!}',
			},
	dataType: 'json',
	global: false,
	async:false,
	success: function (response) {
		// you will get response from your php page (what you echo or print)
		// return response;
		var arrItems = [];              		// The array to store JSON data.
		$.each(response, function (index, value) {
			arrItems.push(value);       		// Push the value inside the array.
		});
		return arrItems;
	},
	error: function(jqXHR, textStatus, errorThrown) {
		console.log(textStatus, errorThrown);
	}
}).responseText;

// this is how u cange from json to array type data
var data = $.parseJSON( data2 );

var data3 = $.ajax({
	url: "{{ route('leavedate.unavailabledate') }}",
	type: "POST",
	data : {
				id: {{ \Auth::user()->belongstostaff->id }},
				type: 2,
				_token: '{!! csrf_token() !!}',
			},
	dataType: 'json',
	global: false,
	async:false,
	success: function (response) {
		// you will get response from your php page (what you echo or print)
		// return response;
		var arrItems = [];              		// The array to store JSON data.
		$.each(response, function (index, value) {
			arrItems.push(value);       		// Push the value inside the array.
		});
		return arrItems;
	},
	error: function(jqXHR, textStatus, errorThrown) {
		console.log(textStatus, errorThrown);
	}
}).responseText;

// this is how u change from json to array type data
var data4 = $.parseJSON( data3 );
