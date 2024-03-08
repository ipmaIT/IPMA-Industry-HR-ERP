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


// load helper
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

class ConditionalIncentiveJob implements ShouldQueue
{
	use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	protected $staffs;
	protected $request;

	/**
	 * Create a new job instance.
	 */
	public function __construct($staffs, $request)
	{
		$this->staffs = $staffs;
		$this->request = $request;
		// dd($staffs, $request);
	}

	/**
	 * Execute the job.
	 */
	public function handle()//: void
	{
		$staffs = $this->staffs;
		$request = $this->request;

		$handle = fopen(storage_path('app/public/excel/cistaff.csv'), 'a+') or die();
		// $handle = fopen(storage_path('app/public/excel/Staff_Appraisal_'.$year.'_'.now()->format('j F Y g.i').'.csv'), 'a+') or die();

		$incentivestaffs = Staff::select('staffs.id', 'logins.username', 'staffs.name')->join('logins', 'staffs.id', '=', 'logins.staff_id')->orderBy('logins.username')->whereIn('staffs.id', $staffs)->where('logins.active', 1)->get();

		foreach ($incentivestaffs as $k1 => $v1) {
			$records[$k1] = [$v1->username, $v1->name];
			foreach ($v1->belongstomanycicategoryitem()?->get() as $k2 => $v2) {
				$records[$k1][$k2] = nl2br($v2->description);
			}
		}

		foreach ($records as $value) {
			fputcsv($handle, $value);
		}
		fclose($handle);
	}
}
