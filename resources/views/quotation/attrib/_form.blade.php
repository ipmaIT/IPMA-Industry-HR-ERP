<div class="card">
	<div class="card-header">Product Attribute</div>
	<div class="card-body">

		<div class="form-group row {{ $errors->has('attribute')?'has-error':'' }}">
			<label for="it" class="col-form-label col-sm-2">Product Attribute : </label>
			<div class="col-sm-10">
				<input type="text" name="attribute" value="{{ old('attribute', @$quotItemAttrib->attribute) }}" id="it" class="form-control form-control-sm col-sm-12 @error('attribute') is-invalid @enderror" placeholder="Product Attibute" aria-describedby="emailHelp">
			</div>
		</div>

		<div class="form-group row">
			<div class="col-sm-10 offset-sm-2">
				<button type="submit" class="btn btn-sm btn-outline-secondary">Save</button>
			</div>
		</div>

	</div>
</div>
