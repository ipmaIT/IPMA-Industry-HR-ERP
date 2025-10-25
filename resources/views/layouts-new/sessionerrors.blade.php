<!-- IF ERROR -->
@if(Session::has('danger'))
	<h6 class="pb-4 mb-4 border-bottom text-center alert alert-danger">
		{{ Session::get('danger') }}
	</h6>
@endif
