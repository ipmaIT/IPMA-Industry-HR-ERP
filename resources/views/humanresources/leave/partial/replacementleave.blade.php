		$('#remove').remove();
		$('#wrapper').append(
			'<div id="remove">' +
				'<div class="form-group row m-2 {{ $errors->has('leave_id') ? 'has-error' : '' }}">' +
					'{{ Form::label('nrla', 'Please Choose Your Replacement Leave : ', ['class' => 'col-sm-4 col-form-label']) }}' +
					'<div class="col-sm-8 nrl">' +
						'<p>Total Replacement Leave = {{ $oi->sum('leave_balance') }} days</p>' +
						'<select name="id" id="nrla" class="form-control form-select form-select-sm">' +
							'<option value="">Please select</option>' +
						@foreach( $oi as $po )
							'<option value="{{ $po->id }}" data-nrlbalance="{{ $po->leave_balance }}">On ' + moment( '{{ $po->date_start }}', 'YYYY-MM-DD' ).format('ddd Do MMM YYYY') + ', your leave balance = {{ $po->leave_balance }} day</option>' +
						@endforeach
						'</select>' +
					'</div>' +
				'</div>' +

				@include('humanresources.leave.jspartial.fromtojs')

				@include('humanresources.leave.jspartial.formcheckwrapper')

				@if( $userneedbackup == 1 )
				'<div id="backupwrapper">' +
					@include('humanresources.leave.jspartial.backupperson')
				'</div>' +
				@endif

			'</div>'
		);

		/////////////////////////////////////////////////////////////////////////////////////////
		// more option
		$('#form').bootstrapValidator('addField', $('.nrl').find('[name="leave_id"]'));
		@if( $userneedbackup == 1 )
		$('#form').bootstrapValidator('addField', $('.backup').find('[name="staff_id"]'));
		@endif
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_start"]'));
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_end"]'));
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));


		/////////////////////////////////////////////////////////////////////////////////////////
		// enable select2 on nrla
		$('#nrla').select2({ placeholder: 'Please select', 	width: '100%',
		});

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable select2
		@include('humanresources.leave.method.backupperson')

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable datetime for the 1st one
		@include('humanresources.leave.method.fromdatetimepicker')
		.on('dp.change dp.update', function(e) {
			$('#form').bootstrapValidator('revalidateField', 'date_time_start');
			var minDaten = $('#from').val();
			// console.log(minDaten);
			$('#to').datetimepicker('minDate', minDaten);

			if($('#from').val() === $('#to').val()) {
				if( $('.removehalfleave').length === 0) {

					////////////////////////////////////////////////////////////////////////////////////////
					// checking half day leave
					var d = false;
					var itime_start = 0;
					var itime_end = 0;
					$.each(objtime, function() {
					// console.log(this.date_half_leave);
						if(this.date_half_leave == $('#from').val()) {
							return [d = true, itime_start = this.time_start, itime_end = this.time_end];
						}
					});
					// console.log(d);
					if(d === true) {
						$('#wrapperday').append(
							@include('humanresources.leave.jspartial.wrapperdayleavecat1disable')
						);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

						var daynow = moment($('#from').val(), 'YYYY-MM-DD').format('dddd');
						var datenow =$('#from').val();

						obj1(datenow);

						var checkedam = 'checked';
						var checkedpm = 'checked';
						if(obj.time_start_am == itime_start) {
							var toggle_time_start_am = 'disabled';
							var checkedam = '';
							var checkedpm = 'checked';
						}

						if(obj.time_start_pm == itime_start) {
							var toggle_time_start_pm = 'disabled';
							var checkedam = 'checked';
							var checkedpm = '';
						}
						$('#wrappertest').append(
							@include('humanresources.leave.jspartial.halfday')
						);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

					} else {
						$('#wrapperday').append(
							@include('humanresources.leave.jspartial.wrapperdayleavecat1check')
						);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
					}
				}
			}
			if($('#from').val() !== $('#to').val()) {
				$('.removehalfleave').remove();
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
			}
		});
		// end date from

		@include('humanresources.leave.method.todatetimepicker')
		.on('dp.change dp.update', function(e) {
			$('#form').bootstrapValidator('revalidateField', 'date_time_end');
			var maxDate = $('#to').val();
			$('#from').datetimepicker('maxDate', maxDate);
			if($('#from').val() === $('#to').val()) {
				if( $('.removehalfleave').length === 0) {

					////////////////////////////////////////////////////////////////////////////////////////
					// checking half day leave
					var d = false;
					var itime_start = 0;
					var itime_end = 0;
					$.each(objtime, function() {
					// console.log(this.date_half_leave);
						if(this.date_half_leave == $('#from').val()) {
							return [d = true, itime_start = this.time_start, itime_end = this.time_end];
						}
					});
					// console.log(d);
					if(d === true) {
						$('#wrapperday').append(
							@include('humanresources.leave.jspartial.wrapperdayleavecat1disable')
						);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

						var daynow = moment($('#from').val(), 'YYYY-MM-DD').format('dddd');
						var datenow =$('#from').val();

						obj1(datenow);

						var checkedam = 'checked';
						var checkedpm = 'checked';
						if(obj.time_start_am == itime_start) {
							var toggle_time_start_am = 'disabled';
							var checkedam = '';
							var checkedpm = 'checked';
						}

						if(obj.time_start_pm == itime_start) {
							var toggle_time_start_pm = 'disabled';
							var checkedam = 'checked';
							var checkedpm = '';
						}
						$('#wrappertest').append(
							@include('humanresources.leave.jspartial.halfday')
						);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));

					} else {
						$('#wrapperday').append(
							@include('humanresources.leave.jspartial.wrapperdayleavecat1check')
						);
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
						$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
					}
				}
			}
			if($('#from').val() !== $('#to').val()) {
				$('.removehalfleave').remove();
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
			}
		});
		// end date to

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable radio
		$(document).on('change', '#appendleavehalf :radio', function () {
			if (this.checked) {
				var daynow = moment($('#from').val(), 'YYYY-MM-DD').format('dddd');
				var datenow =$('#from').val();

				obj1(datenow);

				// checking so there is no double
				if( $('.removetest').length == 0 ) {
					$('#wrappertest').append(
						@include('humanresources.leave.jspartial.halfdaycheck')
					);
				}
			}
		});

		$(document).on('change', '#removeleavehalf :radio', function () {
		// $('#removeleavehalf :radio').change(function() {
			if (this.checked) {
				$('.removetest').remove();
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
			}
		});
