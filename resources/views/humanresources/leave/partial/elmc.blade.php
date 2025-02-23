		$('#remove').remove();
		$('#wrapper').append(
			'<div id="remove">' +
				<!-- mc leave -->
				@include('humanresources.leave.jspartial.fromtojs')

				@if($setHalfDayMC == 1)
				@include('humanresources.leave.jspartial.formcheckwrapper')
				@endif

				@if( $userneedbackup == 99 )
				'<div id="backupwrapper">' +
					@include('humanresources.leave.jspartial.backupperson')
				'</div>' +
				@endif

				@include('humanresources.leave.jspartial.uploadsupportdoc')

				@include('humanresources.leave.jspartial.acknowledgesuppdoc')

			'</div>'
		);

		//add bootstrapvalidator
		@if( $userneedbackup == 1 )
		$('#form').bootstrapValidator('addField', $('.backup').find('[name="staff_id"]'));
		@endif
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_start"]'));
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_end"]'));
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
		$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
		$('#form').bootstrapValidator('addField', $('.supportdoc').find('[name="document"]'));
		$('#form').bootstrapValidator('addField', $('.suppdoc').find('[name="documentsupport"]'));

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable datetime for the 1st one
		@include('humanresources.leave.method.fromdatetimepickerdata4')
		.on('dp.change dp.update', function(e) {
			$('#form').bootstrapValidator('revalidateField', 'date_time_start');
			var minDaten = $('#from').val();
			$('#to').datetimepicker('minDate', minDaten);

			@if($setHalfDayMC == 1)
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
			@endif
			if($('#from').val() !== $('#to').val()) {
				$('.removehalfleave').remove();
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
			}

			// for backup person based on from date
			@if( $userneedbackup == 99 )
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

		@include('humanresources.leave.method.todatetimepickerdata4')
		.on('dp.change dp.update', function(e) {
			$('#form').bootstrapValidator('revalidateField', 'date_time_end');
			var maxDate = $('#to').val();
			$('#from').datetimepicker('maxDate', maxDate);

			@if($setHalfDayMC == 1)
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
			@endif
			if($('#from').val() !== $('#to').val()) {
				$('.removehalfleave').remove();
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
			}
		});
		// end date

		/////////////////////////////////////////////////////////////////////////////////////////
		//enable select 2 for backup
		@if( $userneedbackup == 99 )
			@include('humanresources.leave.method.backupperson')
		@endif
		/////////////////////////////////////////////////////////////////////////////////////////
		@if($setHalfDayMC == 1)
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
		//$('#removeleavehalf :radio').change(function() {
			if (this.checked) {
				$('.removetest').remove();
			}
		});
		@endif
