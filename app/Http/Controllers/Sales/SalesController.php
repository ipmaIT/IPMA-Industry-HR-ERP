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
use App\Models\Staff;
use App\Models\Sales\Sales;

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

class SalesController extends Controller
{
	function __construct()
	{
		$this->middleware('auth');
		$this->middleware('highMgmtAccess:1|2|5,6|24', ['only' => ['index', 'show']]);
		$this->middleware('highMgmtAccessLevel1:1|2|5,6|24', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
	}

	public function index(): View
	{
		$sales = Sales::all();
		return view('sales.sales.index', ['sales' => $sales]);
	}

	public function create(): View
	{
		return view('sales.sales.create');
	}

	public function store(Request $request): RedirectResponse
	{
		// dd($request->except('_token'));
		$validated = $request->validate(
				[
					'date_order' => 'required|date',
					'customer_id' => 'nullable',
					'sales_type_id' => 'required',
					'special_request' => 'required_if:spec_req,true',
					'po_number' => 'nullable',
					'delivery_at' => 'required',
					'urgency' => 'nullable',
					'delivery' => 'required',
					'delivery.*.sales_delivery_id' => 'required',
					'special_delivery_instruction' => 'nullable',
					'jobdesc' => 'required|array|min:1',
					'jobdesc.*.job_description' => 'required',
					'jobdesc.*.quantity' => 'required',
					'jobdesc.*.uom_id' => 'required',
					'jobdesc.*.gItems' => 'required|array|min:1',
					'jobdesc.*.gItems.*.sales_get_item_id' => 'required',
					'jobdesc.*.machine_id' => 'required',
					'jobdesc.*.machine_accessory_id' => 'nullable',
					'jobdesc.*.job_description' => 'required',
				],
				[],
				[
					'date_order' => 'Date',
					'customer_id' => 'Customer',
					'sales_type_id' => 'Order Type',
					'special_request' => 'Special Request Remarks',
					'po_number' => 'PO Number',
					'delivery_at' => 'Estimation Delivery Date',
					'urgency' => 'Mark As Urgent',
					'delivery' => 'Delivery Instruction',
					'delivery.*.sales_delivery_id' => 'Delivery Instruction Method',
					'special_delivery_instruction' => 'Special Delivery Instruction',
					'jobdesc' => 'Job Description',
					'jobdesc.*.job_description' => 'Job Description',
					'jobdesc.*.quantity' => 'Job Description Quantity',
					'jobdesc.*.uom_id' => 'Job Description UOM ',
					'jobdesc.*.gItems' => 'Job Description Acquire Items',
					'jobdesc.*.gItems.*.sales_get_item_id' => 'Job Description Acquire Items Method',
					'jobdesc.*.machine_id' => 'Job Description Machine',
					'jobdesc.*.machine_accessory_id' => 'Job Description Machine Accessories',
					'jobdesc.*.remarks' => 'Job Description Remarks',
				]
			);

		$user = \Auth::user()->belongstostaff->belongstomanydepartment()->wherePivot('main', 1)->first()->id;
		if ($user == 6) {
			$sales_by = 2;
		} else {
			$sales_by = 1;
		}

		$count = Sales::whereYear('created_at', now()->format('Y'))->get()->count() + 1;

		$data = $request->only([
														'date_order',
														'customer_id',
														'sales_type_id',
														'delivery_at',
														'urgency',
														'special_delivery_instruction',
														'spec_req',
														'special_request',
													]);

		$data += ['year' => now()->format('Y')];
		$data += ['sales_by_id' => $sales_by];
		$data += ['no' => $count];

		$sal = \Auth::user()->belongstostaff->hasmanysales()->create($data);

		// checkbox: no need to check id
		foreach ($request->delivery ?? [] as $deli) {
			$sal->belongstomanydelivery()->attach($deli);
		}

		foreach ($request->jobdesc ?? [] as $jobd) {
			$sjd = $sal->hasmanyjobdescription()->create(Arr::except($jobd, ['gItems']));
			$sjd->belongstomanysalesgetitem()->attach($jobd['gItems']);
		}

		return redirect()->route('sale.index')->with('success', 'Successfully Add New Customer Order');
	}

	public function show(Sales $sale): View
	{
	}

	public function edit(Sales $sale): View
	{
		return view('sales.sales.edit', ['sale' => $sale]);
	}

	public function update(Request $request, Sales $sale): RedirectResponse
	{
		// dd($request->all());
		$validated = $request->validate(
				[
					'date_order' => 'required|date',
					'customer_id' => 'nullable',
					'sales_type_id' => 'required',
					'special_request' => 'required_if:spec_req,true',
					'po_number' => 'nullable',
					'delivery_at' => 'required',
					'urgency' => 'nullable',
					'delivery' => 'required',
					'delivery.*.sales_delivery_id' => 'required',
					'special_delivery_instruction' => 'nullable',
					'jobdesc' => 'required|array|min:1',
					'jobdesc.*.job_description' => 'required',
					'jobdesc.*.quantity' => 'required',
					'jobdesc.*.uom_id' => 'required',
					'jobdesc.*.gItems' => 'required|array|min:1',
					'jobdesc.*.gItems.*.sales_get_item_id' => 'required',
					'jobdesc.*.machine_id' => 'required',
					'jobdesc.*.machine_accessory_id' => 'nullable',
					'jobdesc.*.job_description' => 'required',
				],
				[],
				[
					'date_order' => 'Date',
					'customer_id' => 'Customer',
					'sales_type_id' => 'Order Type',
					'special_request' => 'Special Request Remarks',
					'po_number' => 'PO Number',
					'delivery_at' => 'Estimation Delivery Date',
					'urgency' => 'Mark As Urgent',
					'delivery' => 'Delivery Instruction',
					'delivery.*.sales_delivery_id' => 'Delivery Instruction Method',
					'special_delivery_instruction' => 'Special Delivery Instruction',
					'jobdesc' => 'Job Description',
					'jobdesc.*.job_description' => 'Job Description',
					'jobdesc.*.quantity' => 'Job Description Quantity',
					'jobdesc.*.uom_id' => 'Job Description UOM ',
					'jobdesc.*.gItems' => 'Job Description Acquire Items',
					'jobdesc.*.gItems.*.sales_get_item_id' => 'Job Description Acquire Items Method',
					'jobdesc.*.machine_id' => 'Job Description Machine',
					'jobdesc.*.machine_accessory_id' => 'Job Description Machine Accessories',
					'jobdesc.*.remarks' => 'Job Description Remarks',
				]
			);

		$user = \Auth::user()->belongstostaff->belongstomanydepartment()->wherePivot('main', 1)->first()->id;
		if ($user == 6) {
			$sales_by = 2;
		} else {
			$sales_by = 1;
		}

		$data = $request->only([
														'date_order',
														'customer_id',
														'sales_type_id',
														'delivery_at',
														'urgency',
														'special_delivery_instruction',
														'spec_req',
														'special_request',
													]);

		$data += ['year' => now()->format('Y')];
		$data += ['sales_by_id' => $sales_by];
		$data += ['staff_id' => \Auth::user()->belongstostaff->id];

		$sale->update($request->only($data));

		// checkbox: no need to check id
		foreach ($request->delivery ?? [] as $deli) {
			$sale->belongstomanydelivery()->sync($deli);
		}

		foreach ($request->jobdesc ?? [] as $jobd) {
			$sjd = $sale->hasmanyjobdescription()->updateOrCreate(Arr::except($jobd, ['gItems']));
			$sjd->belongstomanysalesgetitem()->sync($jobd['gItems']);
		}

		return redirect()->route('sale.index')->with('success', 'Successfully Edit Order');
	}

	public function destroy(Sales $sale): JsonResponse
	{
		// $sale->detach();
		$sale->delete();
		return response()->json([
			'status' => 'success',
			'message' => 'Your data deleted',
		]);
	}
}
