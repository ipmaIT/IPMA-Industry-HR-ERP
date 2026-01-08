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
use App\Models\HumanResources\HRAppraisalMark;
use App\Models\HumanResources\HRAppraisalSectionSub;

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
        // Get username safely
        $login = $v->hasmanylogin()->where('active', 1)->first();
        $username = $login ? $login->username : '';
        $name = $v->name;

        // Only fetch evaluatees for the given year
        $evaluatees = AppraisalPivot::where('evaluatee_id', $v->id)
            ->where('year', $year)
            ->get();

        $loop = 1;

        foreach ($evaluatees as $evaluatee) {

            if ($evaluatee && $evaluatee->evaluator_id != null && $evaluatee->year == $year) {

                // Fetch evaluator name
                $evaluator = Staff::find($evaluatee->evaluator_id);
                $appraisal_staff = $evaluator ? $evaluator->name : '';

                // Marks
                $full_mark = $evaluatee->full_mark ?? '';
                $total_mark = $evaluatee->total_mark ?? '';
                $average = ($full_mark != '' && $total_mark != '') ? round(($total_mark * 100) / $full_mark, 2) : '';

                // Remarks
                $remarks = HRAppraisalMark::where('pivot_apoint_id', $evaluatee->id)
                    ->whereNotNull('remark')
                    ->get();

                $remarksQuestion = [];
                $remarksList = [];

                foreach ($remarks as $remark) {
                    $question = HRAppraisalSectionSub::find($remark->section_sub_id);
                    $remarksQuestion[] = $question ? $question->section_sub : '';
                    $remarksList[] = $remark->remark;
                }

            } else {
                // If no evaluator or evaluatee not matching year
                $appraisal_staff = '';
                $full_mark = '';
                $total_mark = '';
                $average = '';
                $remarksQuestion = [];
                $remarksList = [];
            }

            // Prepare CSV row
            if ($loop == 1) {
                $records[$i] = [
                    $username,
                    $name,
                    $appraisal_staff,
                    $full_mark,
                    $total_mark,
                    $average,
                    (string)(($remarksQuestion[0] ?? '') . "\n" . ($remarksList[0] ?? '')),
                    (string)(($remarksQuestion[1] ?? '') . "\n" . ($remarksList[1] ?? '')),
                    (string)(($remarksQuestion[2] ?? '') . "\n" . ($remarksList[2] ?? '')),
                    (string)(($remarksQuestion[3] ?? '') . "\n" . ($remarksList[3] ?? ''))
                ];
            } else {
                $records[$i] = [
                    '',
                    '',
                    $appraisal_staff,
                    $full_mark,
                    $total_mark,
                    $average,
                    (string)(($remarksQuestion[0] ?? '') . "\n" . ($remarksList[0] ?? '')),
                    (string)(($remarksQuestion[1] ?? '') . "\n" . ($remarksList[1] ?? '')),
                    (string)(($remarksQuestion[2] ?? '') . "\n" . ($remarksList[2] ?? '')),
                    (string)(($remarksQuestion[3] ?? '') . "\n" . ($remarksList[3] ?? ''))
                ];
            }

            $i++;
            $loop++;
        }
    }

    // Export to CSV
    if (!empty($records)) {
        foreach ($records as $value) {
            fputcsv($handle, $value);
        }
    }

    fclose($handle);
	}

}