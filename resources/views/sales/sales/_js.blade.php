/* populate sale delivery */
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


window.data = {
	route: {
		customer: '{{ route('customer.customer') }}',
		getOptSalesType: "{{ route('sales.getOptSalesType') }}",
		getOptSalesDeliveryType: "{{ route('sales.getOptSalesDeliveryType') }}",
		uom: '{{ route('uom.uom') }}',
		machine: '{{ route('machine.machine') }}',
		machineaccessories: '{{ route('machineaccessories.machineaccessories') }}',
		getOptSalesGetItem: "{{ route('getOptSalesGetItem') }}",
	},
	url: {
		salesjobdescription: `{{ url('salesjobdescription') }}`,
	},
	old: {
		customerid: @json(old('customer_id', @$sale?->customer_id)),
		salestypeid: @json(old('sales_type_id', @$sale?->sales_type_id)),
		oldItemsValuec: @json($oldItemsValuec)??[],
		specialrequest: @json(old('special_request', @$sale->special_request)),
		salesJD: @json($salesJD),
		specialrequest: @json(old('special_request', @$sale?->special_request)),
	},
	errors: @json($errors->toArray()),
};

