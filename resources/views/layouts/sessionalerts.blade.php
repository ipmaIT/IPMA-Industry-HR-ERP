<!-- IF STATUS -->
@if(Session::has('message'))
	<h6 class="pb-4 mb-4 border-bottom text-center alert alert-primary">
		{{ Session::get('message') }}
	</h6>
@endif
