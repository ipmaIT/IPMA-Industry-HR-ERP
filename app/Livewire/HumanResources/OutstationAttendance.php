<?php

namespace App\Livewire\HumanResources;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;

// load model
use App\Models\HumanResources\HROutstation;
use App\Models\HumanResources\HROutstationAttendance;

// load db facade
use Illuminate\Database\Eloquent\Builder;

class OutstationAttendance extends Component
{
	#[Rule('required', 'Outstation Location')]
	public $outstation_id = [];

	public $longitude = '';
	public $latitude = '';

	public $in;
	public $out;

	// protected $listeners = [
	// 	'getLatitude',
	// 	'getLongitude',
	// 	'getAccuracy',
	// ];

	public function updated()
	{

	}
	public function render()
	{
		$locations = HROutstation::where('staff_id', \Auth::user()->belongstostaff->id)
												->where(function (Builder $query) {
													$query->whereDate('date_from', '<=', now())
														->whereDate('date_to', '>=', now());
												})
												->where('active', 1)
												->get();
		$inouts = HROutstationAttendance::where([['staff_id', \Auth::user()->belongstostaff->id], ['date_attend', now()->format('Y-m-d')]])->get();
		// dd($inouts->isEmpty());
		// foreach ($inouts as $inout) {
		// 	$in[] = $inout->in;
		// 	$out[] = $inout->out;
		// }
		return view('livewire.humanresources.outstationattendance', [
			'locations' => $locations,
			'inouts' => $inouts,
		]);
	}

	public function mount()
	{
	}

	public function store()
	{
		$this->validate();

		HROutstationAttendance::create([
			'outstation_id' => $this->outstation_id,
			'staff_id' => \Auth::user()->belongstostaff->id,
			'date_attend' => now(),
			'in' => time(),
			'in_latitude' => $this->inlat,
			'in_longitude' => $this->inlong,
			'in_regionName' => $this->inregName,
			'in_cityName' => $this->incityName,
		]);
		$this->reset();
	}
}
