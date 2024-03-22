<div>
	<h4>Outstation Attendance</h4>




	<form wire:submit.prevent="store">
		<p>Click button below to mark your attendance</p>
		<div class="row my-2 @error('outstation_id') is-invalid @enderror">
			<label for="outstation" class="col-sm-4 form-label @error('outstation_id') is-invalid @enderror">Location :</label>
			<div class="col-sm-8">
				<select wire:model="outstation_id" id="outstation" class="form-select form-select-sm col-sm-auto @error('outstation_id') is-invalid @enderror" aria-describedby="in1">
					<option value="">Please choose</option>
					@foreach ($locations as $location)
						<option value="{{ $location->id }}">{{ $location->belongstocustomer?->customer }}</option>
					@endforeach
				</select>
				@error('outstation_id') <div id="in1" class="invalid-feedback">{{ $message }}</div> @enderror
			</div>
		</div>
		<div class="offset-sm-4 col-sm-8">
			{{ Form::submit('Mark Attendance', ['class' => 'btn btn-sm btn-primary']) }}
		</div>
	</form>

</div>

@script
<script>
	jQuery.noConflict ();
	(function($){
		$(document).ready(function(){
			$.getJSON("https://ipgeolocation.abstractapi.com/v1/?api_key={{ env('API_GEOLOCATION_KEY') }}", function(data) {
				console.log(data);
			})

			let api_key = "{!! env('API_GEOLOCATION_KEY') !!}";
			$.getJSON("https://ipgeolocation.abstractapi.com/v1/?api_key=" + api_key, function(data) {
				var loc_info = "Your location details :\n";
				loc_info += "Latitude: "+data.latitude +"\n";
				loc_info += "Longitude: "+data.longitude+"\n";
				loc_info += "Timezone: GMT"+data.gmt_offset+"\n";
				loc_info += "Country: "+data.country+"\n";
				loc_info += "Region: "+data.region+"\n";
				loc_info += "City: "+data.city+"\n";
				console.log(loc_info);
			})
		});
	})(jQuery);
</script>
@endscript