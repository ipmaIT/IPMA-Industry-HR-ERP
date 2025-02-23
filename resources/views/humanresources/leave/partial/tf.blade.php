		$('#remove').remove();
		$('#wrapper').append(
			'<div id="remove">' +
				<!-- time off -->
				'<div class="form-group row m-2 {{ $errors->has('date_time_start') ? 'has-error' : '' }}">' +
					'{{ Form::label('from', 'Date : ', ['class' => 'col-sm-4 col-form-label']) }}' +
					'<div class="col-sm-8 datetime" style="position: relative">' +
						'{{ Form::text('date_time_start', @$value, ['class' => 'form-control form-control-sm', 'id' => 'from', 'placeholder' => 'Date', 'autocomplete' => 'off']) }}' +
					'</div>' +
				'</div>' +

				'<div class="form-group row m-2 {{ $errors->has('date_time_end') ? 'has-error' : '' }}">' +
					'{{ Form::label('to', 'Time : ', ['class' => 'col-sm-4 col-form-label']) }}' +
					'<div class="col-sm-8">' +
							'<div class="form-row time">' +
								'<div class="col-sm-8 m-2" style="position: relative">' +
									'{{ Form::text('time_start', @$value, ['class' => 'form-control form-control-sm', 'id' => 'start', 'placeholder' => 'From', 'autocomplete' => 'off']) }}' +
								'</div>' +
								'<div class="col-sm-8 m-2" style="position: relative">' +
									'{{ Form::text('time_end', @$value, ['class' => 'form-control form-control-sm', 'id' => 'end', 'placeholder' => 'To', 'autocomplete' => 'off']) }}' +
								'</div>' +
							'</div>' +
					'</div>' +
				'</div>' +

				@if( $userneedbackup == 1 )
				'<div id="backupwrapper">' +
					@include('humanresources.leave.jspartial.backupperson')
				'</div>' +
				@endif

				@include('humanresources.leave.jspartial.uploadsupportdoc')

				@include('humanresources.leave.jspartial.acknowledgesuppdoc')

			'</div>'
		);
		/////////////////////////////////////////////////////////////////////////////////////////
		// more option
		//add bootstrapvalidator
		@if( $userneedbackup == 1 )
		$('#form').bootstrapValidator('addField', $('.backup').find('[name="staff_id"]'));
		@endif
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_start"]'));
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
		$('#form').bootstrapValidator('addField', $('.supportdoc').find('[name="document"]'));
		$('#form').bootstrapValidator('addField', $('.suppdoc').find('[name="documentsupport"]'));

		/////////////////////////////////////////////////////////////////////////////////////////
		//enable select 2 for backup
		$('#backupperson').select2({
			placeholder: 'Please Choose',
			width: '100%',
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
						date_to: $('#from').val(),
					}
					return query;
				}
			},
			allowClear: true,
			closeOnSelect: true,
		});

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable datetime for the 1st one
		@include('humanresources.leave.method.fromdatetimepickerdata4')
		.on('dp.change ', function(e) {
			$('#form').bootstrapValidator('revalidateField', 'date_time_start');

			@if( $userneedbackup == 1 )
			// enable backup if date from is greater or equal than today.
			//cari date now dulu
			if( $('#from').val() >= moment().format('YYYY-MM-DD') ) {
				// console.log( moment().add(1, 'days').format('YYYY-MM-DD') );
				// console.log($( '#rembackup').children().length + ' <= rembackup length' );
				if( $('#backupwrapper').children().length == 0 ) {
					$('#backupwrapper').append(
						@include('humanresources.leave.jspartial.backupperson')
						''
					);
					$('#form').bootstrapValidator('addField', $('.backup').find('[name="staff_id"]'));
					@include('humanresources.leave.method.backupperson')
				}
			} else {
				$('#form').bootstrapValidator('removeField', $('.backup').find('[name="staff_id"]'));
				$('#backupwrapper').children().remove();
			}
			@endif
		});

		/////////////////////////////////////////////////////////////////////////////////////////
		// time start
		// need to get working hour for each user
		// lazy to implement this 1. :P
		// moment(obj.time_start_am, 'HH:mm:ss').format('h:mm a')
		// moment(obj.time_end_am, 'HH:mm:ss').format('h:mm a')
		// moment(obj.time_start_pm, 'HH:mm:ss').format('h:mm a')
		// moment(obj.time_end_pm, 'HH:mm:ss').format('h:mm a')

		$('#start').datetimepicker({
			icons: {
				time: "fas fas-regular fa-clock fa-beat",
				date: "fas fas-regular fa-calendar fa-beat",
				up: "fas fa-regular fa-circle-up fa-beat",
				down: "fas fa-regular fa-circle-down fa-beat",
				previous: 'fas fas-regular fa-arrow-left fa-beat',
				next: 'fas fas-regular fa-arrow-right fa-beat',
				today: 'fas fas-regular fa-calenday-day fa-beat',
				clear: 'fas fas-regular fa-broom-wide fa-beat',
				close: 'fas fas-regular fa-rectangle-xmark fa-beat'
			},
			format: 'h:mm A',
			// enabledHours: [8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18],
		})
		.on('dp.change dp.update', function(e){
			$('#form').bootstrapValidator('revalidateField', 'time_start');
			// $('#end').datetimepicker('minDate', moment($('#start').val(), 'h:mm A'));
		});

		$('#end').datetimepicker({
			icons: {
				time: "fas fas-regular fa-clock fa-beat",
				date: "fas fas-regular fa-calendar fa-beat",
				up: "fas fa-regular fa-circle-up fa-beat",
				down: "fas fa-regular fa-circle-down fa-beat",
				previous: 'fas fas-regular fa-arrow-left fa-beat',
				next: 'fas fas-regular fa-arrow-right fa-beat',
				today: 'fas fas-regular fa-calenday-day fa-beat',
				clear: 'fas fas-regular fa-broom-wide fa-beat',
				close: 'fas fas-regular fa-rectangle-xmark fa-beat'
			},
			format: 'h:mm A',
			// enabledHours: [8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18],
		})
		.on('dp.change dp.update', function(e){
			$('#form').bootstrapValidator('revalidateField', 'time_end');
			// $('#start').datetimepicker('minDate', moment($('#end').val(), 'h:mm A'));
		});
