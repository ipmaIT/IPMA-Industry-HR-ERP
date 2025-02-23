		$('#remove').remove();
		if($selection.val() == '3') {
			$('#wrapper').append(
				'<div id="remove">' +
					<!-- UNPAID LEAVE | UPL -->
					@include('humanresources.leave.jspartial.fromtojs')

					@include('humanresources.leave.jspartial.formcheckwrapper')

					@if( $userneedbackup == 1 )
						@include('humanresources.leave.jspartial.backupperson')
					@endif

					@include('humanresources.leave.jspartial.uploadsupportdoc')

					@include('humanresources.leave.jspartial.acknowledgesuppdoc')

				'</div>'
			);
		} else {
			$('#wrapper').append(
				'<div id="remove">' +
					<!-- ANNUAL LEAVE | AL -->
					@include('humanresources.leave.jspartial.fromtojs')

					@include('humanresources.leave.jspartial.formcheckwrapper')

					@if( $userneedbackup == 1 )
						@include('humanresources.leave.jspartial.backupperson')
					@endif
				'</div>'
			);
		}

		@if( $userneedbackup == 1 )
		$('#form').bootstrapValidator('addField', $('.backup').find('[name="staff_id"]'));
		@endif
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_start"]'));
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_end"]'));
		if($selection.val() == '3') {
			$('#form').bootstrapValidator('addField', $('.supportdoc').find('[name="document"]'));
			$('#form').bootstrapValidator('addField', $('.suppdoc').find('[name="documentsupport"]'));
		}

		/////////////////////////////////////////////////////////////////////////////////////////
		//enable select 2 for backup
		@include('humanresources.leave.method.backupperson')

		/////////////////////////////////////////////////////////////////////////////////////////
		// start date
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
						if(this.date_half_leave == $('#to').val()) {
							return [d = true, itime_start = this.time_start, itime_end = this.time_end];
						}
					});
					console.log(d, itime_start, itime_end);
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
						console.log(obj.time_start_am, itime_start);
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
					$('#form').bootstrapValidator('addField', $('.time').find('[name="time_start"]'));
					$('#form').bootstrapValidator('addField', $('.time').find('[name="time_end"]'));
				}
			}
		});

		$(document).on('change', '#removeleavehalf :radio', function () {
		//$('#removeleavehalf :radio').change(function() {
			if (this.checked) {
				$('.removetest').remove();
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_start"]'));
				$('#form').bootstrapValidator('removeField', $('.time').find('[name="time_end"]'));
			}
		});
