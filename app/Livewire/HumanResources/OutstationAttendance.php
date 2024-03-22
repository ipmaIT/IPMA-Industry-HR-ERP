<?php

namespace App\Livewire\HumanResources;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;

// load model
use App\Models\HumanResources\HROutstation;
// use App\Models\HumanResources\HROutstationAttendance;

// load db facade
use Illuminate\Database\Eloquent\Builder;

class OutstationAttendance extends Component
{
	#[Rule('required', 'Outstation Location')]
	public $outstation_id = [];

	public function render()
	{
		$locations = HROutstation::where('staff_id', \Auth::user()->belongstostaff->id)
												->where(function (Builder $query) {
													$query->whereDate('date_from', '<=', now())
														->whereDate('date_to', '>=', now());
												})
												->where('active', 1)
												->get();
		return view('livewire.humanresources.outstationattendance', ['locations' => $locations]);
	}

	public function mount()
	{
	}

	public function store()
	{
		$this->validate();
	}
}
