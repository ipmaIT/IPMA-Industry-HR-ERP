$('#backupperson').select2({
	placeholder: 'Please Choose',
	width: '100%',
	allowClear: true,
	closeOnSelect: true,
	ajax: {
		url: '{{ route('backupperson') }}',
		// data: { '_token': '{!! csrf_token() !!}' },
		type: 'POST',
		dataType: 'json',
		data: function (params) {
			var query = {
				id: {{ \Auth::user()->belongstostaff->id }},
				_token: '{!! csrf_token() !!}',
				date_from: $('#from').val(),
				date_to: $('#to').val(),
			}
			return query;
		}
	},
});