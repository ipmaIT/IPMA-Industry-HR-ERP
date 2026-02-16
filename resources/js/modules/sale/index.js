const { route, url, old } = window.data;

/* tooltip */
$(document).ready(function(){
	$('[data-bs-toggle="tooltip"]').tooltip();
});

/* datatables */
$.fn.dataTable.moment( 'D MMM YYYY' );
$.fn.dataTable.moment( 'YYYY' );
$.fn.dataTable.moment( 'h:mm a' );
var table = $('#sales').DataTable({
	...config.datatable,
	"columnDefs": [
		{ type: 'date', 'targets': [1,3] },
	],
})
.on( 'length.dt page.dt order.dt search.dt', function ( e, settings, len ) {
	$(document).ready(function(){
		$('[data-bs-toggle="tooltip"]').tooltip();
	});
});

/* Use event delegation (important if inside DataTable) */
$(document).on('click', '.sale-approve, .sale-send', async function (e) {
	e.preventDefault();

	const id   = $(this).data('id');
	const type = $(this).hasClass('sale-approve') ? 'approve' : 'send';

	const configMap = {
		approve: {
			url: `${url.saleapproved}/${id}`,
			successTitle: 'Approved!',
			cancelText: 'Sale is not approved'
		},
		send: {
			url: `${url.salesend}/${id}`,
			successTitle: 'Processed!',
			cancelText: 'Sale Order not proceed to the next process'
		}
	};
	await handleSwalAction(id, configMap[type]);
});

async function handleSwalAction(id, options) {
	try {
		const result = await swal.fire({
			...config.swal,
			preConfirm: async () => {
				try {
					const response = await $.ajax({
						url: options.url,
						type: 'PATCH',
						dataType: 'json',
						data: { id }
					});
					return response;
				} catch (err) {
					swal.showValidationMessage('Request failed');
				}
			}
		});

		/* If confirmed */
		if (result.isConfirmed && result.value) {
			await swal.fire(
				options.successTitle,
				result.value.message,
				result.value.status
			);

			table.ajax.reload(null, false); // keep pagination
		}

		/* If cancelled */
		if (result.dismiss === swal.DismissReason.cancel) {
			swal.fire('Cancelled', options.cancelText, 'info');
		}

	} catch (err) {
		swal.fire('Oops...', 'Something went wrong with ajax!', 'error');
	}
}
