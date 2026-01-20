<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// for controller output
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

// load facade
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

// load models
use App\Models\Sales\Sales;
use App\Models\Sales\OptSalesType;
use App\Models\Sales\OptSalesDeliveryType;

// load batch and queue
// use Illuminate\Bus\Batch;
// use Illuminate\Support\Facades\Bus;

// load helper
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

// load Carbon
use \Carbon\Carbon;
use \Carbon\CarbonPeriod;
use \Carbon\CarbonInterval;

use Session;
use Throwable;
use Exception;
use Log;

class AjaxController extends Controller
{
	public function saleamend(Request $request, Sales $saleamend): RedirectResponse
	{
		// dd($request->all());
		$validated = $request->validate(
				[
					'amend' => 'required',
				],
				[
					// 'amend.required' => 'Please insert year',
				],
				[
					'amend' => 'Amendment',
				]
			);
		$amend = ucwords(Str::lower($request->amend));
		$saleamend->update(['amend' => $amend]);
		return redirect()->route('sale.index');
	}

	public function saleapproved(Request $request, Sales $saleapproved)
	{
		$validated = $request->validate(
				[
					'id' => 'required',
				],
				[
					// 'id.required' => 'Please insert year',
				],
				[
					'id' => 'Sale Order ID',
				]
			);
		$saleapproved->update([
			'approved_by' => \Auth::user()->belongstostaff->id,
			'approved_date' => now(),
		]);
		return response()->json([
			'message' => 'Sales Order Approved!',
			'status' => 'success'
		]);
	}

	public function salesend(Request $request, Sales $salesend)
	{
		$validated = $request->validate(
				[
					'id' => 'required',
				],
				[
					// 'id.required' => 'Please insert year',
				],
				[
					'id' => 'Sale Order ID',
				]
			);
		$salesend->update([
			'confirm' => \Auth::user()->belongstostaff->id,
			'confirm_date' => now(),
		]);
		return response()->json([
			'message' => 'Sales Order Send to next process!',
			'status' => 'success'
		]);
	}

	public function getOptSalesType(Request $request): JsonResponse
	{
		$values = OptSalesType::when($request->id, function($q1) use ($request){
								$q1->where('id', $request->id);
							})
							->when($request->search, function($q1) use ($request){
								$q1->where('delivery_type', 'LIKE', '%'.$request->search.'%');
							})
							->get();
		return response()->json($values);
	}

	public function getOptSalesDeliveryType(Request $request): JsonResponse
	{
		$values = OptSalesDeliveryType::when($request->id, function($q1) use ($request){
								$q1->where('id', $request->id);
							})
							->when($request->search, function($q1) use ($request){
								$q1->where('delivery_type', 'LIKE', '%'.$request->search.'%');
							})
							->when($request->idNotIn, function($q1) use ($request){
								$q1->whereNotIn('id', $request->idNotIn);
							})
							->get();
		return response()->json($values);
	}
















}
