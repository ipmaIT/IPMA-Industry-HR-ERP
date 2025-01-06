<?php

namespace App\Jobs;

// load batch and queue
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Batchable;

// load db facade
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

// load models
use App\Models\Staff;
use App\Models\Login;
use App\Models\HumanResources\AppraisalPivot;

// load helper
use App\Helpers\UnavailableDateTime;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

// load lib
use \Carbon\Carbon;
use \Carbon\CarbonPeriod;
use \Carbon\CarbonInterval;

use Session;
use Throwable;
use Log;
use Exception;

// load laravel-excel
// use Maatwebsite\Excel\Facades\Excel;
// use App\Exports\StaffAppraisalExport;

class AppraisalMark implements ShouldQueue
{
	use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	protected $staffs;
	protected $year;

	/**
	 * Create a new job instance.
	 */
	public function __construct($staffs, $year)
	{
		$this->staffs = $staffs;
		$this->year = $year;
	}

	/**
	 * Execute the job.
	 */
	public function handle(): void
	{
		$staffs = $this->staffs;
		$year = $this->year;

		$handle = fopen(storage_path('app/public/excel/export.csv'), 'a+') or die();

		$i = 1;
		foreach ($staffs as $v) {
			$username = $v->hasmanylogin()->where('active', 1)->first()->username;
			$name = $v->name;




			$evaluatees = AppraisalPivot::where('evaluatee_id', $v->id)->get();
			$loop = 1;
			foreach ($evaluatees as $evaluatee) {

				if ($evaluatee && $evaluatee->evaluator_id != NULL) {

					$evaluator = Staff::where('id', $evaluatee->evaluator_id)->first(); // Use first() instead of get()

					// Check if an evaluator was found
					if ($evaluator) {
						$appraisal_staff = $evaluator->name;
					} else {
						$appraisal_staff = ''; // If no evaluator was found
					}

					// Check if an full mark was found
					if ($evaluatee->full_mark != NULL) {
						$full_mark = $evaluatee->full_mark;
					} else {
						$full_mark = ''; // If no full_mark was found
					}

					// Check if an total mark was found
					if ($evaluatee->total_mark != NULL) {
						$total_mark = $evaluatee->total_mark;
					} else {
						$total_mark = ''; // If no total_mark was found
					}
				} else {
					$appraisal_staff = ''; // If no evaluatee or evaluator_id is null
					$full_mark = '';
					$total_mark ='';
				}

				if ($loop == 1) {
					$records[$i] = [$username, $name, $appraisal_staff, $full_mark, $total_mark];
					$i++;
					$loop++;
				} else {
					$records[$i] = ['', '', $appraisal_staff, $full_mark, $total_mark];
					$i++;
					$loop++;
				}
			}

			// 		$pivotappraisal = DB::table('pivot_category_appraisals')
			//   ->join('option_appraisal_categories', 'option_appraisal_categories.id', '=', 'pivot_category_appraisals.category_id')
			//   ->where('pivot_category_appraisals.category_id', $staff->catid)
			//   ->orderBy('version', 'DESC')
			//   ->first();
		}
		// $combine = $header + $records;
		// $dataappraisal = collect($combine);
		// Excel::store(new StaffAppraisalExport($dataappraisal), 'Staff_Appraisal_'.$year.'.xlsx');
		foreach ($records as $value) {
			fputcsv($handle, $value);
		}
		fclose($handle);
	}
}
