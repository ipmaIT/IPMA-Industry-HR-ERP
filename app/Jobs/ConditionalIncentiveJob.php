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

	protected function str_putcsv($data) {
		# Generate CSV data from array
		$fh = fopen('php://temp', 'rw'); # don't create a file, attempt
											# to use memory instead

		# write out the headers
		fputcsv($fh, array_keys(current($data)));

		# write out the data
		foreach ( $data as $row ) {
				fputcsv($fh, $row);
		}
		rewind($fh);
		$csv = stream_get_contents($fh);
		fclose($fh);

		return $csv;
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

		// finding what week for today
		$weeks = OptWeekDates::where(function(Builder $query) {
																	$query->whereDate('date_from', '>=', now()->startOfMonth())
																		->whereDate('date_to', '<=', now()->endOfWeek());
																})
																->get();

		foreach ($incentivestaffs as $k1 => $v1) {
			foreach ($v1->belongstomanycicategoryitem()?->get() as $k2 => $v2) {
				$desc[$k1][$k2] = $v2->description;
				foreach ($v2 as $k3 => $v3) {

				}
			}
			$records[$k1] = [$v1->username, $v1->name, $desc[$k1]];
		}

		// foreach ($records as $k1 => $v1) {
		// 	fputcsv($handle, [$v1[0], $v1[1]]);
		// 	foreach ($v1[2] as $k2 => $v2) {
		// 		// dd($v3);
		// 		fputcsv($handle, [null, null, $v2]);
		// 	}
		// }
		// fclose($handle);
		str_putcsv();
	}
}
