'<div class="form-group row m-0 {{ $errors->has('documentsupport') ? 'has-error' : '' }}">' +
	'<div class="col-sm-8 offset-sm-4 form-check">' +
		'{{ Form::checkbox('documentsupport', 1, @$value, ['class' => 'form-check-input', 'id' => 'suppdoc']) }}' +
		'<label for="suppdoc" class="form-check-label p-1 bg-warning text-danger rounded">' +
			'<p class="my-auto">Please ensure you will submit <strong>Supporting Documents</strong> within <strong>3 Days</strong> after date leave.</p>' +
		'</label>' +
	'</div>' +
'</div>' +
