<?php
namespace App\Http\Controllers\HumanResources\HRDept;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// for controller output
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

// models
use App\Models\Staff;
// use App\Models\HumanResources\OptWeekDates;
use App\Models\HumanResources\ConditionalIncentiveCategoryItem;
// use App\Models\HumanResources\ConditionalIncentiveCategory;

// load db facade
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

// load batch and queue
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use App\Jobs\ConditionalIncentiveJob;

// load array helper
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

use \Carbon\Carbon;
use \Carbon\CarbonImmutable;
use Throwable;
use Log;
use Session;
use Exception;

class ConditionalIncentiveStaffCheckingReportController extends Controller
{
	function __construct()
	{
		$this->middleware(['auth']);
		$this->middleware('highMgmtAccess:1|2|5,14|31', ['only' => ['index', 'show']]);
		$this->middleware('highMgmtAccessLevel1:1|5,14', ['only' => ['create', 'store', 'edit', 'update', 'destroy']]);
	}

	// public function index(): View
	// {
	// }

	public function create(): View
	{
		// $from = Carbon::parse(session()->get('from'))->format('j_M_Y');
		// $to = Carbon::parse(session()->get('to'))->format('j_M_Y');
		// if (!$request->id) {
		// 	if (session()->exists('lastBatchIdPay')) {
		// 		$bid = session()->get('lastBatchIdPay');
		// 	} else {
		// 		$bid = 1;
		// 	}
		// } else {
		// 	$bid = $request->id;
		// }
		// $batch = Bus::findBatch($bid);

		// if (Storage::exists('public/excel/payslip.csv')) {

		// 	$header[-1] = ['Emp No', 'Name', 'Category', 'AL', 'NRL', 'MC', 'UPL', 'Absent', 'UPMC', 'Lateness(minute)', 'Early Out(minute)', 'No Pay Hour', 'Maternity', 'Hospitalization', 'Other Leave', 'Compassionate Leave', 'Marriage Leave', 'Day Work', '1.0 OT', '1.5 OT', 'OT', 'TF'];

		// 	// (A) READ EXISTING CSV FILE INTO ARRAY
		// 	$csv = fopen(storage_path('app/public/excel/payslip.csv'), 'r');
		// 	while (($r=fgetcsv($csv)) !== false) {
		// 		$rows[] = $r;
		// 	}
		// 	fclose($csv);

		// 	// (B) PREPEND NEW ROWS
		// 	$rows = array_merge($header, $rows);
		// 	// dd($rows);

		// 	// (C) SAVE UPDATED CSV
		// 	// $csv = fopen(storage_path('app/public/excel/payslip.csv'), 'w');
		// 	$filename = 'Staff_Attendance_Payslip_'.$from.'_-_'.$to.'.csv';
		// 	$file = fopen(storage_path('app/public/excel/'.$filename), 'w');
		// 	foreach ($rows as $r) {
		// 		fputcsv($file, $r);
		// 	}
		// 	fclose($file);
		// 	Storage::delete('public/excel/payslip.csv');
		// 	$url = Storage::url('public/excel/'.$filename);
		// 	// return redirect($url);
		// 	session()->forget('from');
		// 	session()->forget('to');
		// 	return Storage::download('public/excel/'.$filename);
		// }

		// $batch = null;
		// return view('humanresources.hrdept.conditionalincentive.staffcheckreport.create', ['batch' => $batch]);
		return view('humanresources.hrdept.conditionalincentive.staffcheckreport.create');
	}

	public function store(Request $request)// : RedirectResponse
	{
		// dd($request->all());
		$validated = $request->validate(
			[
				'date_from' => 'required|lte:date_to',
				'date_to' => 'required|gte:date_from',
			],
			[
				'date_from.lte' => 'The :attribute field must be less than or equal to To Week.',
				'date_to.gte' => 'The :attribute field must be greater than or equal to From Week.',
			],
			[
				'date_from' => 'From Week',
				'date_to' => 'To Week',
			]
		);

		$cistaff = ConditionalIncentiveCategoryItem::all();
		$staf = [];
		foreach ($cistaff as $v) {
			foreach ($v->belongstomanystaff()->get() as $v1) {
				$staf[] = $v1->pivot->staff_id;
			}
		}
		$staffs = array_unique($staf);
		$incentivestaffs = Staff::select('staffs.id', 'logins.username', 'staffs.name')->join('logins', 'staffs.id', '=', 'logins.staff_id')->orderBy('logins.username')->whereIn('staffs.id', $staffs)->where('logins.active', 1)->get();

		// $stchunk = array_chunk($staffs, 2);
		// // process collection
		// // $batch = Bus::batch([])->name('Conditional Incentive Staff on -> '.now())->dispatch();
		// foreach ($stchunk as $index => $values) {
		// 	// $data[$index] = $values;
		// 	foreach ($values as $value) {
		// 		$data[$index][] = $value;
		// 	}
		// // 	// dd($data[$index]);
		// // 	// $batch->add(new AttendancePayslipJob($data[$index], $year));
		// 	$dat[] = new ConditionalIncentiveJob($data[$index], $request->only(['date_from', 'date_to']));
		// }
		// // dd($incentivestaffs, $staff, $stchunk, $data[$index]);

		// $batch = Bus::batch($dat)
		// 			->name('Conditional Incentive Staff on -> '.now()->format('j M Y'))
		// // 			// ->progress(function (Batch $batch) {
		// // 			// 	// A single job has completed successfully...
		// // 			// })
		// // 			// ->then(function (Batch $batch) {
		// // 			// 	// All jobs completed successfully...
		// // 			// })
		// // 			// ->catch(function (Batch $batch, Throwable $e) {
		// // 			// 	// First batch job failure detected...
		// // 			// })
		// // 			// ->finally(function (Batch $batch) {
		// // 			// 	// The batch has finished executing...
		// // 			// })
		// 			->dispatch();
		// session(['lastBatchIdPay' => $batch->id]);
		// session(['date_from' => $request->from]);
		// session(['date_to' => $request->to]);
		// return redirect()->route('cicategorystaffcheckreport.create', ['id' => $batch->id]);
		return redirect()->route('cicategorystaffcheckreport.create', ['incentivestaffs' => $incentivestaffs, 'date_from' => $request->date_from, 'date_to' => $request->date_to]);
	}

	// public function show(ConditionalIncentiveCategoryItem $cicategoryitem): View
	// {
	// 	//
	// }

	// public function edit(ConditionalIncentiveCategoryItem $cicategoryitem): View
	// {
	// 	//
	// }

	// public function update(Request $request, ConditionalIncentiveCategoryItem $cicategoryitem): RedirectResponse
	// {
	// 	//
	// }

	// public function destroy(Request $request, ConditionalIncentiveCategoryItem $cicategoryitem): JsonResponse
	// {
	// 	//
	// }
}
