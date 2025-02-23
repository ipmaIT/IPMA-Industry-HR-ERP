		$('#remove').remove();
		$('#wrapper').append(
			'<div id="remove">' +
				<!-- maternity leave -->
				@include('humanresources.leave.jspartial.fromtojs')

				@if( $userneedbackup == 99 )
					@include('humanresources.leave.jspartial.backupperson')
				@endif
				'</div>'
		);


		/////////////////////////////////////////////////////////////////////////////////////////
		// more option
		//add bootstrapvalidator
		// more option
		$('#form').bootstrapValidator('addField', $('.nrl').find('[name="leave_id"]'));
		@if( $userneedbackup == 1 )
		// $('#form').bootstrapValidator('addField', $('.backup').find('[name="staff_id"]'));
		@endif
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_start"]'));
		$('#form').bootstrapValidator('addField', $('.datetime').find('[name="date_time_end"]'));
		$('#form').bootstrapValidator('addField', $('.supportdoc').find('[name="document"]'));
		$('#form').bootstrapValidator('addField', $('.suppdoc').find('[name="documentsupport"]'));

		/////////////////////////////////////////////////////////////////////////////////////////
		//enable select 2 for backup
		@include('humanresources.leave.method.backupperson')

		/////////////////////////////////////////////////////////////////////////////////////////
		// enable datetime for the 1st one
		@include('humanresources.leave.method.fromdatetimepicker')
		.on('dp.change dp.update', function(e) {
			$('#form').bootstrapValidator('revalidateField', 'date_time_start');
			var minDate = $('#from').val();
			$('#to').datetimepicker('minDate', moment( minDate, 'YYYY-MM-DD').add(59, 'days').format('YYYY-MM-DD') );
			$('#to').val( moment( minDate, 'YYYY-MM-DD').add(59, 'days').format('YYYY-MM-DD') );
		});

		@include('humanresources.leave.method.todatetimepicker')
		.on('dp.change dp.update', function(e) {
			$('#form').bootstrapValidator('revalidateField', 'date_time_end');
			var maxDate = $('#to').val();

			// $('#from').datetimepicker('maxDate', moment( maxDate, 'YYYY-MM-DD').subtract(59, 'days').format('YYYY-MM-DD'));
			// $('#from').val( moment( maxDate, 'YYYY-MM-DD').subtract(59, 'days').format('YYYY-MM-DD') );
		});
