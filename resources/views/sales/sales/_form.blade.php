<div class="col-sm-12 row">
	<div class="col-sm-6">

		<div class="form-group row m-2 @error('date_order') has-error @enderror">
			<label for="nam" class="col-form-label col-sm-4">Date : </label>
			<div class="col-sm-8" style="position: relative;">
				<input type="text" name="date_order" value="{{ old('date_order', @$sale?->date_order) }}" id="nam" class="form-control form-control-sm col-sm-12 @error('date_order') is-invalid @enderror" placeholder="Date">
				@error('date_order')
				<div class="invalid-feedback">
					{{ $message }}
				</div>
				@enderror
			</div>
		</div>

		<div class="form-group row m-2 @error('customer_id') has-error @enderror">
			<label for="cust" class="col-form-label col-sm-4">Customer : </label>
			<div class="col-sm-8">
				<select name="customer_id" id="cust" class="form-select form-select-sm @error('customer_id') is-invalid @enderror" placeholder="Please choose"></select>
				@error('customer_id')
				<div class="invalid-feedback">
					{{ $message }}
				</div>
				@enderror
			</div>
		</div>

		<div class="form-group row m-2 @error('deliveryby_id') has-error @enderror">
			<label for="otype" class="col-form-label col-sm-4">Order Type : </label>
			<div class="col-sm-8 @error('sales_type_id') has-error is-invalid @enderror" id="sale_Order"></div>
			@error('sales_type_id.*')
				<div class="invalid-feedback">
					{{ $message }}
				</div>
			@enderror
		</div>

		<div class="form-group row m-2 @error('special_request') is-invalid @enderror">
			<div class="col form-check">
				<input type="checkbox" name="spec_req" class="form-check-input m-1 " value="1" id="specReq" {{ old('spec_req', @$sale?->spec_req) == 1 ?'checked':NULL }}>
				<label class="form-check-label col" for="specReq">
					Special Request
				</label>
			</div>
			<div class="form-group col-sm-8" id="wraptextarea">
			</div>
		</div>

	</div>
	<div class="col-sm-6">

		<div class="form-group row m-2 @error('po_number') has-error @enderror">
			<label for="pon" class="col-form-label col-sm-4">PO Number : </label>
			<div class="col-sm-8">
				<input type="text" name="po_number" value="{{ old('po_number', @$sale->po_number) }}" id="pon" class="form-control form-control-sm col-sm-12 @error('po_number') is-invalid @enderror" placeholder="PO Number">
				@error('po_number')
				<div class="invalid-feedback">
					{{ $message }}
				</div>
				@enderror
			</div>
		</div>

		<div class="form-group row m-2 @error('delivery_at') has-error @enderror">
			<label for="delivery" class="col-form-label col-sm-4">Estimate Delivery Date : </label>
			<div class="col-sm-8" style="position: relative;">
				<input
					type="text"
					name="delivery_at"
					value="{{ old('delivery_at', @$sale?->delivery_at) }}"
					id="delivery"
					class="form-control form-control-sm col-sm-12 @error('delivery_at') is-invalid @enderror"
					placeholder="Estimate Delivery Date">
				@error('delivery_at')
				<div class="invalid-feedback">
					{{ $message }}
				</div>
				@enderror
			</div>
		</div>

		<div class="form-group row m-2 @error('urgency') has-error @enderror">
			<label for="urgency1" class="col-form-label col-sm-4">Mark As Urgent : </label>
			<div class="col-sm-8 my-auto">
				<div class="form-check @error('urgency') is-invalid @enderror">
					<input type="checkbox" name="urgency" class="form-check-input" value="1" id="urgency1" {{ old('urgency', @$sale->urgency) == 1 ?'checked':NULL }}>

					<label class="form-check-label col-sm-4 " for="urgency1">
						Yes
					</label>
				</div>
				@error('urgency')
				<div class="invalid-feedback">
					{{ $message }}
				</div>
				@enderror
			</div>
		</div>

		<div class="form-group row m-2 @error('sales_delivery_id') has-error @enderror">
			<label for="devi" class="col-form-label col-sm-4">Delivery Instruction : </label>
			<div class="col row @error('sales_delivery_id') is-invalid has-error @enderror" id="ger">
			</div>
			@error('sales_delivery_id')
				<div class="invalid-feedback">
					{{ $message }}
				</div>
			@enderror
			<textarea name="special_delivery_instruction" id="sdev" class="form-control form-control-sm col-sm-12 my-3 @error('special_delivery_instruction') is-invalid @enderror" placeholder="Special Delivery Instruction">{{ old('special_delivery_instruction', @$sale->special_delivery_instruction) }}</textarea>
			@error('special_delivery_instruction')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
			@enderror
		</div>
	</div>

	<h5>Job Description</h5>
	<div class="col-sm-12">
		<div class="row" id="jdesc_wrap"></div>
		<button type="button" id="jdesc_add" class="btn btn-sm btn-outline-secondary">
			<i class="fa-solid fa-list-check"></i>
			&nbsp;Add Job Description
		</button>
	</div>

</div>
<div class="d-flex justify-content-center m-3">
	<button type="submit" class="btn btn-sm btn-outline-secondary">Add Order</button>
</div>
