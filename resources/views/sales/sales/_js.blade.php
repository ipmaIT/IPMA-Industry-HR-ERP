/////////////////////////////////////////////////////////////////////////////////////////
// tooltip
$(document).ready(function(){
	$('[data-bs-toggle="tooltip"]').tooltip();
});

/////////////////////////////////////////////////////////////////////////////////////////
// date
$('#nam, #delivery').datetimepicker({
	format:'YYYY-MM-DD',
	// useCurrent: false,
}).on('dp.change', function(e){
	$('#form').bootstrapValidator('revalidateField', $('[name="date_order"]'));
	$('#form').bootstrapValidator('revalidateField', $('[name="delivery_at"]'));
});

/////////////////////////////////////////////////////////////////////////////////////////
// customer
$('#cust').select2({
	placeholder: 'Please Select',
	width: '100%',
	allowClear: true,
	closeOnSelect: true,
	ajax: {
		url: '{{ route('customer.customer') }}',
		type: 'POST',
		dataType: 'json',
		data: function (params) {
			var query = {
				_token: '{!! csrf_token() !!}',
				search: params.term,
			}
			return query;
		}
	},
});
const oldCust = @json(old('customer_id', @$sale?->customer_id));
$.ajax({
	url: "{{ route('customer.customer') }}",
	type: "POST",
	dataType: 'json',
	data: {
		_token: '{!! csrf_token() !!}',
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

/////////////////////////////////////////////////////////////////////////////////////////
// populate order type
const selectedSalesTypeId = @json(old('sales_type_id', @$sale?->sales_type_id));
$.ajax({
	dataType: 'json',
	url: "{{ route('sales.getOptSalesType') }}",
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


/////////////////////////////////////////////////////////////////////////////////////////
// populate sale delivery
@php
	$itemsa = @$sale?->belongstomanydelivery()
						?->get()
						->map(function ($module) {
							// delivery
							return [
								$module->pivot->id, [
									'sales_delivery_id' => $module->id,
								]
							];
						});

	$itemsArrayb = $itemsa?->toArray()??[];
	$oldItemsValuec = old('delivery', $itemsArrayb)??[];
	// dd($oldItemsValuec);
@endphp

const sdeliveryId = @json($oldItemsValuec)??[];

$.ajax({
	url: "{{ route('sales.getOptSalesDeliveryType') }}",
	type: "GET",
	dataType: 'json',
	data: {
		_token: '{{csrf_token()}}'
	},
	success: (function(response) {

		// begin with empty it and append data into $("#ger")
		let cdeliveries = $("#ger");
		cdeliveries.empty();

		// convert array to object from old data
		const obj = Array.isArray(sdeliveryId) ? sdeliveryId : Object.entries(sdeliveryId);
		// console.log(sdeliveryId);
		// console.log(obj);

		// map the old data object
		const cicms = obj.map(item =>  item[1]);
		// console.log(cicms);

		// populate the checkbox from ajax
		response.forEach(function(value, i) {

			// compare old data with the ajax data
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

/////////////////////////////////////////////////////////////////////////////////////////
// special request description
let sr = @json(old('special_request', @$sale->special_request));
//if(sr){
//	$(`#specReq`).prop('checked').trigger('chamge');
//}

$('#specReq').change(function() {
	if(this.checked == true) {
		if ($('#sreq').length == 0) {
			$('#wraptextarea').append(`
				<textarea name="special_request" id="sreq" class="form-control form-control-sm col-sm-12 @error('special_request') is-invalid @enderror" placeholder="Special Request Remarks">{{ old('special_request', @$sale?->special_request) }}</textarea>
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


/////////////////////////////////////////////////////////////////////////////////////////
// select2
function populateSelect(i = 0){
	let ids = [];

	$('.machine_accessory').each(function () {
		const val = $(this).val();
		if (val) ids.push(val);
	});

	$(`#jdu_${i},.uom`).select2({
		theme: 'bootstrap-5',
		placeholder: 'UOM',
		width: '100%',
		allowClear: true,
		closeOnSelect: true,
		ajax: {
			url: '{{ route('uom.uom') }}',
			type: 'POST',
			dataType: 'json',
			data: function (params) {
				var query = {
					_token: '{!! csrf_token() !!}',
					search: params.term,
				}
				return query;
			}
		},
	});

	$(`#jobdescmach_${i},.machine`).select2({
		theme: 'bootstrap-5',
		placeholder: 'Machine',
		width: '100%',
		allowClear: true,
		closeOnSelect: true,
		ajax: {
			url: '{{ route('machine.machine') }}',
			type: 'GET',
			dataType: 'json',
			data: function (params) {
				var query = {
					_token: '{!! csrf_token() !!}',
					search: params.term,
				}
				return query;
			}
		},
	}).on('change', function(){
		$(`#jobdescmachacc_${i}`).empty().trigger('change');
	});

	$(`#jobdescmachacc_${i},.machine_accessory`).select2({
		theme: 'bootstrap-5',
		placeholder: 'Machine Accessories',
		width: '100%',
		allowClear: true,
		closeOnSelect: true,
		ajax: {
			url: '{{ route('machineaccessories.machineaccessories') }}',
			type: 'GET',
			dataType: 'json',
			data: function (params) {
				var query = {
					_token: '{!! csrf_token() !!}',
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

/////////////////////////////////////////////////////////////////////////////////////////
// populate checkbox acquire item

function populateCheckbox(i = 0, name = '', getItem = []) {

	// getOptSalesGetItem
	$.ajax({
		url: "{{ route('getOptSalesGetItem') }}",
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
							class="form-check-input @error('jobdesc.*.gItems.*.sales_get_item_id') is-invalid @enderror"
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

/////////////////////////////////////////////////////////////////////////////////////////
// add item
$('#jdesc_wrap').remAddRow({
	addBtn: '#jdesc_add',
	maxRows: 1000,
	fieldName: 'jobdesc',
	rowSelector: 'rowjdesc',
	removeClass: 'jdesc_remove',
	swal: {
		ajax: {
			url: `{{ url('salesjobdescription') }}`,
			method: 'DELETE',
			dataType: 'json',
			data: {
				_token: '{{ csrf_token() }}'
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

			<div class="col m-0 p-1 row form-group my-auto @error('jobdesc.*.job_description') has-error @enderror">
				<textarea
					name="${name}[${i}][job_description]"
					id="jdi_${i}"
					class="form-control form-control-sm @error('jobdesc.*.job_description') is-invalid @enderror"
					placeholder="Job Description"
				></textarea>
				@error('jobdesc.*.job_description')
					<div class="invalid-feedback">
						{{ $message }}
					</div>
				@enderror
			</div>

			<div class="col m-0 p-1 row form-group my-auto @error('jobdesc.*.quantity') has-error @enderror">
				<input
					type="text"
					name="${name}[${i}][quantity]"
					id="jdq_${i}"
					class="form-control form-control-sm m-1 @error('jobdesc.*.quantity') is-invalid @enderror"
					placeholder="Quantity"
				>
				@error('jobdesc.*.quantity')
					<div class="invalid-feedback">
						{{ $message }}
					</div>
				@enderror
			</div>

			<div class="col m-0 p-1 row form-group my-auto @error('jobdesc.*.uom_id') has-error @enderror">
				<select
					name="${name}[${i}][uom_id]"
					id="jdu_${i}"
					class="form-select form-select-sm m-1 uom @error('jobdesc.*.uom_id') is-invalid @enderror"
					placeholder="UOM"
				>
				</select>
				@error('jobdesc.*.uom_id')
					<div class="invalid-feedback">
						{{ $message }}
					</div>
				@enderror
			</div>

		</div>

		<div class="col-sm-12 row">

			<div class="col m-0 p-1 row form-group my-auto @error('jobdesc.*.machine_id') has-error @enderror">
				<select
					name="${name}[${i}][machine_id]"
					id="jobdescmach_${i}"
					class="form-select form-select-sm m-1 machine @error('jobdesc.*.machine_id') is-invalid @enderror"
					placeholder="Machine"
				></select>
				@error('jobdesc.*.machine_id')
					<div class="invalid-feedback">
						{{ $message }}
					</div>
				@enderror
			</div>

			<div class="col m-0 p-1 row form-group my-auto @error('jobdesc.*.machine_accessory_id') has-error @enderror">
				<select
					name="${name}[${i}][machine_accessory_id]"
					id="jobdescmachacc_${i}"
					class="form-select form-select-sm m-1 machine_accessory @error('jobdesc.*.machine_accessory_id') is-invalid @enderror"
					placeholder="Machine Accessory"
				>
				</select>
				@error('jobdesc.*.machine_accessory_id')
					<div class="invalid-feedback">
						{{ $message }}
					</div>
				@enderror
			</div>


			<div class="col m-0 p-1 row form-group my-auto row @error('jobdesc.*.sales_get_item_id.*') has-error @enderror" id="gar_${i}">
				@error('jobdesc.*.sales_get_item_id.*')
					<div class="invalid-feedback">
						{{ $message }}
					</div>
				@enderror

			</div>
		</div>

		<div class="col-sm-12 row">

			<div class="col m-0 p-1 row  form-group my-auto @error('jobdesc.*.remarks') has-error @enderror">
				<textarea
					name="${name}[${i}][remarks]"
					id="jdr_${i}"
					class="form-control form-control-sm @error('jobdesc.*.remarks') is-invalid @enderror"
					placeholder="Remarks"
				></textarea>
				@error('jobdesc.*.remarks')
					<div class="invalid-feedback">
						{{ $message }}
					</div>
				@enderror
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

/////////////////////////////////////////////////////////////////////////////////////////
// restore old data
<?php
$items = @$sale
				?->hasmanyjobdescription()
				?->get()
				->map(function ($applicant) {
					$modules = $applicant
										?->belongstomanysalesgetitem()
										?->get()
										->map(function ($module) {
											return [
												$module->pivot->id, [
													'sales_get_item_id' => $module->id,
												]
											];
										})
										->toArray();

					return [
						'id'       => $applicant->id,
						'job_description'   => $applicant->job_description,
						'quantity'    => $applicant->quantity,
						'uom_id' => $applicant->uom_id,
						'machine_id' => $applicant->machine_id,
						'machine_accessory_id' => $applicant->machine_accessory_id,
						'remarks' => $applicant->remarks,
						'gItems'     => $modules,
					];
				})
				->toArray() ?? [];



$salesJD = old('jobdesc', $items);
// dd($salesJD);
?>

const sJD = @json($salesJD);
if (sJD.length > 0) {
	sJD.forEach(function (sajobDesc, j) {
		$("#jdesc_add").trigger('click');
		const $row = $(`#rowjdesc_${j}`);

		if (sajobDesc.uom_id) {
			$.ajax({
				type: 'POST',
				url: '{{ route('uom.uom') }}',
				data: {
					_token: '{!! csrf_token() !!}',
					id: sajobDesc.uom_id,
				},
			}).then(function (data) {
				var option = new Option(data.results[0].text, data.results[0].id, true, true);
				$(`#jdu_${j}`).append(option).trigger('change');
			});
		}

		if (sajobDesc.machine_id) {
			$.ajax({
				url: '{{ route('machine.machine') }}',
				type: 'GET',
				data: {
					_token: '{!! csrf_token() !!}',
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
				url: '{{ route('machineaccessories.machineaccessories') }}',
				data: {
					_token: '{!! csrf_token() !!}',
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


/////////////////////////////////////////////////////////////////////////////////////////
// validator
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
