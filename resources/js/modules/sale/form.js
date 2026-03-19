const { route, url, old, errors } = window.data;
function getError(name) {
	return errors[name] ? errors[name][0] : null;
}

/* tooltip */
$(document).ready(function(){
	$('[data-bs-toggle="tooltip"]').tooltip();
});


/* date */
$('#nam, #delivery').datetimepicker({
	format:'YYYY-MM-DD',
	// useCurrent: false,
}).on('dp.change', function(e){
	$('#form').bootstrapValidator('revalidateField', $('[name="date_order"]'));
	$('#form').bootstrapValidator('revalidateField', $('[name="delivery_at"]'));
});


/* customer */
$('#cust').select2({
	...config.select2,
	ajax: {
		url: route.customer,
		type: 'POST',
		dataType: 'json',
		data: function (params) {
			var query = {
				search: params.term,
			}
			return query;
		}
	},
});
const oldCust = old.customerid;
$.ajax({
	url: route.customer,
	type: "POST",
	dataType: 'json',
	data: {
		id: `${oldCust}`,
	},
	success: function (data) {
		const itema = Array.isArray(data) ? data[0] : data;	// change object to array
		if (!itema) return;
		console.log(itema.results[0].text);
		const option1 = new Option(itema.results[0].text, itema.results[0].id, true, true);
		$(`#cust`).append(option1).trigger('change');
	},
	error: function (jqXHR, textStatus, errorThrown) {
		// console.log(textStatus, errorThrown);
	}
});

/* populate order type */
const selectedSalesTypeId = old.salestypeid;
$.ajax({
	dataType: 'json',
	url: route.getOptSalesType,
	type: "GET",
	success: function (response){
	// console.log(response);
		let checkicmsmodule = $("#sale_Order");
		checkicmsmodule.empty();
		// $.each(response, function (i, value) {
		response.forEach(function(value, i) {
			let checked = (selectedSalesTypeId == value.id) ? 'checked' : '';
			let row = `
				<div class="form-check form-check-inline m-1">
					<label class="form-check-label" for="db_${i}">
						<input class="form-check-input"
							type="radio"
							name="sales_type_id"
							id="db_${i}"
							value="${value.id}"
							${checked}
						>
						${value.order_type}
					</label>
				</div>
			`;

			checkicmsmodule.append(row);
		});
	},
	error: function (jqXHR, textStatus, errorThrown) {
		// console.log(textStatus, errorThrown);
	}
});

/* populate sale delivery */
const sdeliveryId = old.oldItemsValuec;
$.ajax({
	url: route.getOptSalesDeliveryType,
	type: "GET",
	dataType: 'json',
	data: {
	},
	success: (function(response) {

		/* begin with empty it and append data into $("#ger") */
		let cdeliveries = $("#ger");
		cdeliveries.empty();

		/* convert array to object from old data */
		const obj = Array.isArray(sdeliveryId) ? sdeliveryId : Object.entries(sdeliveryId);
		// console.log(sdeliveryId);
		// console.log(obj);

		/* map the old data object */
		const cicms = obj.map(item =>  item[1]);
		// console.log(cicms);

		/* populate the checkbox from ajax */
		response.forEach(function(value, i) {

			/* compare old data with the ajax data */
			let found = cicms.find(m => m.sales_delivery_id == value.id);
			let isChecked = found ? 'checked' : '';

			let $row = `
			<div class="form-check form-check-inline m-1">
				<label class="form-check-label" for="dbdid_${i}">
					<input type="checkbox"
						name="delivery[${i}][sales_delivery_id]"
						value="${value.id}"
						id="dbdid_${i}"
						class="form-check-input m-1"
						${isChecked}
					>
					${value.delivery_type}
				</label>
			</div>
			`;
			cdeliveries.append($row);
		});
	}),
	error: (function(jqXHR, textStatus, errorThrown) {
		alert( "error" );
		// console.log(textStatus, errorThrown);
	}),
	complete: (function() {
		// alert( "complete" );
	})
});

/* special request description */
let sr = old.specialrequest;
//if(sr){
//	$(`#specReq`).prop('checked').trigger('chamge');
//}

$('#specReq').change(function() {
	if(this.checked == true) {
		if ($('#sreq').length == 0) {
			$('#wraptextarea').append(`
				<textarea
				 name="special_request"
				 id="sreq"
				 class="form-control form-control-sm col-sm-12 ${getError(`special_request`) ? 'is-invalid' : ''}"
				 placeholder="Special Request Remarks"
				 >${old.specialrequest}</textarea>
				${getError(`special_request`) ? `
				<div class="invalid-feedback">
					${getError(`special_request`)}
				</div>
			` : ''}
			`);
			$('#form').bootstrapValidator('addField', $('#wraptextarea').find('[name="special_request"]'));
			// $('#wraptextarea').find('[name="special_request"]').css('border', '5px solid black');
		}
	} else {
		$('#form').bootstrapValidator('removeField', $('#wraptextarea').find('[name="special_request"]'));
		$('#sreq').remove();
	}
});

if ($('#specReq').prop('checked')) {
	$('#specReq').trigger('change');
}

/* select2 */
function populateSelect(i = 0){
	let ids = [];

	$('.machine_accessory').each(function () {
		const val = $(this).val();
		if (val) ids.push(val);
	});

	$(`#jdu_${i},.uom`).select2({
		...config.select2,
		ajax: {
			url: route.uom,
			type: 'POST',
			dataType: 'json',
			data: function (params) {
				var query = {
					search: params.term,
				}
				return query;
			}
		},
	});

	$(`#jobdescmach_${i},.machine`).select2({
		...config.select2,
		ajax: {
			url: route.machine,
			type: 'GET',
			dataType: 'json',
			data: function (params) {
				var query = {
					search: params.term,
				}
				return query;
			}
		},
	}).on('change', function(){
		$(`#jobdescmachacc_${i}`).empty().trigger('change');
	});

	$(`#jobdescmachacc_${i},.machine_accessory`).select2({
		...config.select2,
		closeOnSelect: true,
		ajax: {
			url: route.machineaccessories,
			type: 'GET',
			dataType: 'json',
			data: function (params) {
				var query = {
					search: params.term,
					machine_id: $(`#jobdescmach_${i}`).val(),
					idNotIn: ids,
				};
				return query;
			},
			processResults: function (data) {
				// console.log(data);
				return {
					results: data.map(function (machAcsry) {
						return {
							id: machAcsry.id,
							text: machAcsry.accessory,
						}
					})
				};
			}

		},
	});

}

/* populate checkbox acquire item */
function populateCheckbox(i = 0, name = '', getItem = []) {

	$.ajax({
		url: route.getOptSalesGetItem,
		dataType: 'json',
		type: "GET",
		success: function (response) {

			const $checkbox = $("#gar_"+i);
			if($checkbox.length > 0) $checkbox.empty();

			// console.log(getItem);
			const obj = Array.isArray(getItem) ? getItem : Object.entries(getItem);
			// console.log(obj);

			const cgetItems = obj.map(item =>  item[1]);

			response.forEach(function(value, j) {
				const checkboxId = `jdescitem${i}_${j}`;

				// Check if this module_id exists in cicms
				let found = cgetItems.find(m => m.sales_get_item_id == value.id);

				// If found, mark checked
				let isChecked = found ? 'checked' : '';

				let row = `
					<div class="form-check">
						<input
							type="checkbox"
							name="${name}[${i}][gItems][${j}][sales_get_item_id]"
							value="${value.id}"
							id="${checkboxId}"
							class="form-check-input
							${getError(`${name}.${i}.gItems.${j}.sales_get_item_id`) ? 'is-invalid' : ''}"
							${isChecked}
						>
						<label
							class="form-check-label"
							for="${checkboxId}"
						>
							${value.get_item}
						</label>
					</div>
				`;
				$checkbox.append(row);
			});
		},
		error: function (jqXHR, textStatus, errorThrown) {
			// console.log(textStatus, errorThrown);
		}
	});

}


/* add item */
$('#jdesc_wrap').addRemRow({
	addBtn: '#jdesc_add',
	maxRows: 1000,
	fieldName: 'jobdesc',
	rowSelector: 'rowjdesc',
	removeClass: 'jdesc_remove',
	swal: {
		ajax: {
			url: url.salesjobdescription,
			method: 'DELETE',
			dataType: 'json',
			data: {
				// _token: '{{ csrf_token() }}'
			}
		}
	},
	validator: {
		form: '#form',
		fields: {
			'[job_description]': {
				validators: {
					notEmpty: {
						message: 'Please insert Job Dscription'
					}
				}
			},
			'[quantity]': {
				validators: {
					notEmpty: {
						message: 'Please insert Quantity'
					},
				}
			},
			'[uom_id]': {
				validators: {
					notEmpty: {
						message: 'Please choose UOM'
					},
				}
			},
			'[machine_id]': {
				validators: {
					notEmpty: {
						message: 'Please choose Machine'
					},
				}
			},
			'[machine_accessory_id]': {
				validators: {
					notEmpty: {
						message: 'Please choose Machine Accessory'
					},
				}
			},
			'[remarks]': {
				validators: {
					notEmpty: {
						message: 'Please insert remarks'
					},
				}
			},

		}
	},
	rowTemplate: (i, name) => `

	<div class="col-sm-12 row border border-info mb-3 rounded rowjdesc" id="rowjdesc_${i}">

		<div class="col-sm-1 m-0 p-1 row my-auto">
			<input type="hidden" name="${name}[${i}][id]">
			<button type="button" class="btn btn-sm btn-outline-danger jdesc_remove" data-index="${i}">
				<i class="fas fa-trash"></i>
			</button>
		</div>

		<div class="col-sm-11 row">

			<div class="col m-0 p-1 row form-group my-auto ${getError(`${name}.${i}.job_description`) ? 'has-error' : ''}">
				<textarea
					name="${name}[${i}][job_description]"
					id="jdi_${i}"
					class="form-control form-control-sm ${getError(`${name}.${i}.job_description`) ? 'is-invalid' : ''}"
					placeholder="Job Description"
				></textarea>
				${getError(`${name}.${i}.job_description`) ? `
				<div class="invalid-feedback">
					${getError(`${name}.${i}.job_description`)}
				</div>
			` : ''}
			</div>

			<div class="col m-0 p-1 row form-group my-auto ${getError(`${name}.${i}.quantity`) ? 'has-error' : ''}">
				<input
					type="text"
					name="${name}[${i}][quantity]"
					id="jdq_${i}"
					class="form-control form-control-sm m-1 ${getError(`${name}.${i}.quantity`) ? 'is-invalid' : ''}"
					placeholder="Quantity"
				>
				${getError(`${name}.${i}.quantity`) ? `
				<div class="invalid-feedback">
					${getError(`${name}.${i}.quantity`)}
				</div>
			` : ''}
			</div>

			<div class="col m-0 p-1 row form-group my-auto ${getError(`${name}.${i}.uom_id`) ? 'has-error' : ''}">
				<select
					name="${name}[${i}][uom_id]"
					id="jdu_${i}"
					class="form-select form-select-sm m-1 uom ${getError(`${name}.${i}.uom_id`) ? 'is-invalid' : ''}"
					placeholder="UOM"
				></select>
				${getError(`${name}.${i}.uom_id`) ? `
				<div class="invalid-feedback">
					${getError(`${name}.${i}.uom_id`)}
				</div>
			` : ''}
			</div>

		</div>

		<div class="col-sm-12 row">

			<div class="col m-0 p-1 row form-group my-auto ${getError(`${name}.${i}.machine_id`) ? 'has-error' : ''}">
				<select
					name="${name}[${i}][machine_id]"
					id="jobdescmach_${i}"
					class="form-select form-select-sm m-1 machine ${getError(`${name}.${i}.machine_id`) ? 'is-invalid' : ''}"
					placeholder="Machine"
				></select>
				${getError(`${name}.${i}.machine_id`) ? `
				<div class="invalid-feedback">
					${getError(`${name}.${i}.machine_id`)}
				</div>
			` : ''}
			</div>

			<div class="col m-0 p-1 row form-group my-auto ${getError(`${name}.${i}.machine_accessory_id`) ? 'has-error' : ''}">
				<select
					name="${name}[${i}][machine_accessory_id]"
					id="jobdescmachacc_${i}"
					class="form-select form-select-sm m-1 machine_accessory ${getError(`${name}.${i}.machine_accessory_id`) ? 'is-invalid' : ''}"
					placeholder="Machine Accessory"
				>
				</select>
				${getError(`${name}.${i}.machine_accessory_id`) ? `
				<div class="invalid-feedback">
					${getError(`${name}.${i}.machine_accessory_id`)}
				</div>
			` : ''}
			</div>


			<div class="col m-0 p-1 row form-group my-auto row ${getError(`${name}.${i}.sales_get_item_id`) ? 'has-error' : ''}" id="gar_${i}">
				${getError(`${name}.${i}.sales_get_item_id`) ? `
				<div class="invalid-feedback">
					${getError(`${name}.${i}.sales_get_item_id`)}
				</div>
			` : ''}
			</div>
		</div>

		<div class="col-sm-12 row">

			<div class="col m-0 p-1 row  form-group my-auto ${getError(`${name}.${i}.remarks`) ? 'has-error' : ''}">
				<textarea
					name="${name}[${i}][remarks]"
					id="jdr_${i}"
					class="form-control form-control-sm ${getError(`${name}.${i}.remarks`) ? 'is-invalid' : ''}"
					placeholder="Remarks"
				></textarea>
				${getError(`${name}.${i}.remarks`) ? `
				<div class="invalid-feedback">
					${getError(`${name}.${i}.remarks`)}
				</div>
			` : ''}
			</div>

		</div>

	</div>


	`,
	onAdd: (i, event, $row, name) => {
		// console.log(`Added row ${i} with field name: ${name}`);
		populateSelect(i);
		populateCheckbox(i, name);
	},
	onRemove: async (i, event, $row, name) => {
		// console.log(`Remove row ${i} with field name: ${name}`);
	}
});

/* restore old data */
const sJD = old.salesJD;
if (sJD.length > 0) {
	sJD.forEach(function (sajobDesc, j) {
		$("#jdesc_add").trigger('click');
		const $row = $(`#rowjdesc_${j}`);

		if (sajobDesc.uom_id) {
			$.ajax({
				type: 'POST',
				url: route.uom,
				data: {
					id: sajobDesc.uom_id,
				},
			}).then(function (data) {
				var option = new Option(data.results[0].text, data.results[0].id, true, true);
				$(`#jdu_${j}`).append(option).trigger('change');
			});
		}

		if (sajobDesc.machine_id) {
			$.ajax({
				url: route.machine,
				type: 'GET',
				data: {
					id: sajobDesc.machine_id,
				},
			}).then(function (data) {
				var option1 = new Option(data.results[0].text, data.results[0].id, true, true);
				$(`#jobdescmach_${j}`).append(option1).trigger('change');
			});
		}

		if (sajobDesc.machine_accessory_id) {
			$.ajax({
				type: 'GET',
				url: route.machineaccessories,
				data: {
					id: sajobDesc.machine_accessory_id,
				},
			}).then(function (data) {
				// console.log(data[0].id, data[0].accessory);
				var option = new Option(data[0].accessory, data[0].id, true, true);
				$(`#jobdescmachacc_${j}`).append(option).trigger('change');
			});
		}

		// console.log(sajobDesc.gItems);
		populateCheckbox(j, 'jobdesc', sajobDesc.gItems);

		$row.find(`[name="jobdesc[${j}][id]"]`).val(sajobDesc.id || '');
		$row.find(`[name="jobdesc[${j}][job_description]"]`).val(sajobDesc.job_description || '');
		$row.find(`[name="jobdesc[${j}][quantity]"]`).val(sajobDesc.quantity || '');
		$row.find(`[name="jobdesc[${j}][remarks]"]`).val(sajobDesc.remarks || '');
	});
}

/* validator */
$('#form').bootstrapValidator({
	fields: {

		date_order: {
			validators: {
				notEmpty: {
					message: 'Please insert'
				},
			}
		},
		customer_id: {
			validators: {
				// notEmpty: {
				// 	message: 'Please choose'
				// },
			}
		},
		'sales_type_id': {
			validators: {
				notEmpty: {
					message: 'Please choose'
				},
			}
		},
		special_request: {
			validators: {
				notEmpty: {
					message: 'Please insert'
				},
			}
		},
		po_number: {
			validators: {
				// notEmpty: {
				// 	message: 'Please insert'
				// },
			}
		},
		delivery_at: {
			validators: {
				// notEmpty: {
				// 	message: 'Please insert'
				// },
			}
		},
		urgency: {
			validators: {
				// notEmpty: {
				// 	message: 'Please choose'
				// },
			}
		},
		'sales_delivery_id[]': {
			validators: {
				notEmpty: {
					message: 'Please choose'
				},
			}
		},
		special_delivery_instruction: {
			validators: {
				// notEmpty: {
				// 	message: 'Please choose'
				// },
			}
		},

	}
})
.find('[name="reason"]')
// .ckeditor()
// .editor
	.on('change', function() {
		// Revalidate the bio field
	$('#form').bootstrapValidator('revalidateField', 'reason');
	// console.log($('#reason').val());
});
