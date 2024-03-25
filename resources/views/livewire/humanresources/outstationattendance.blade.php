<div>
	<h4>Outstation Attendance</h4>
	<form wire:submit.prevent="store">
		<div class="col-sm-12">

			<dl class="row">
				<dt class="col-sm-3">Latitude :</dt>
				<dd class="col-sm-9">{{ $latitude }}</dd>

				<dt class="col-sm-3">Longitude :</dt>
				<dd class="col-sm-9">{{ $longitude }}</dd>
			</dl>

		</div>
		<p>Click button below to mark your attendance</p>
		<div class="row my-2 @error('outstation_id') has-error @enderror">
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
		<div class="row offset-sm-4 col-sm-8">

			<button wire:model="in" class="mx-2 col-sm-auto btn btn-sm btn-success">Mark Attendance In</button>
			<button wire:model="out" class="mx-2 col-sm-auto btn btn-sm btn-danger @if($inouts->isEmpty()) disabled @endif">Mark Attendance Out</button>
		</div>
	</form>

</div>
@script
<script>
	jQuery.noConflict ();
	(function($){
		$(document).ready(function(){

			navigator.geolocation.getCurrentPosition(function(location) {
				console.log(location.coords.latitude);
				// Livewire.emit('getLatitude', location.coords.latitude);
				$wire.$set('getLatitude', location.coords.latitude)
				console.log(location.coords.longitude);
				// Livewire.emit('getLongitude', location.coords.longitude);
				console.log(location.coords.accuracy);
				// Livewire.emit('getAccuracy', location.coords.accuracy);
			});

		});
	})(jQuery);
</script>
@endscript