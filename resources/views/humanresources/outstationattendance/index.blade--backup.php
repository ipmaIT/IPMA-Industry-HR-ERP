<?php
use App\Models\HumanResources\HROutstation;
// load db facade
use Illuminate\Database\Eloquent\Builder;

$r = HROutstation::where('staff_id', \Auth::user()->belongstostaff->id)
		->where(function (Builder $query) {
			$query->whereDate('date_from', '<=', now())
			->whereDate('date_to', '>=', now());
		})
		->where('active', 1)
		->get();
		// ->ddrawsql();
// dd($r);
if ($r->count()) {
	$t = true;
	foreach ($r as $k => $v) {
			$loc[$v->id] = $v->belongstocustomer?->customer;
	}
} else {
	$t = false;
}
?>
@extends('layouts.app')
@section('content')
<div class="container row align-items-start justify-content-center">
	@if ($t)
		@livewire('HumanResources.OutstationAttendance')
	@else
		<h2 class="p-4 m-3 border border-bottom text-center alert alert-danger">Please note, this page can be only use for the outstation personnel. If you are eligible to use this page and mark your attendance, please ask your superior (HR or CS Officer) to assists you by adding your ID into the outstation list.</h2>
	@endif
</div>
	@endsection