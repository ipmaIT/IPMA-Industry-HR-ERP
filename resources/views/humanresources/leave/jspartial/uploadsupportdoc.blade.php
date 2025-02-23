'<div class="form-group row m-2 {{ $errors->has('document') ? 'has-error' : '' }}">' +
	'{{ Form::label( 'doc', 'Upload Supporting Document : ', ['class' => 'col-sm-4 col-form-label'] ) }}' +
	'<div class="col-sm-8 supportdoc">' +
		'{{ Form::file( 'document', ['class' => 'form-control form-control-sm form-control-file', 'id' => 'doc', 'placeholder' => 'Supporting Document']) }}' +
	'</div>' +
'</div>' +