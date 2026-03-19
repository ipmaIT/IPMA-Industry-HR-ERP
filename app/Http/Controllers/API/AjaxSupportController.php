<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

// for controller output
use Illuminate\Http\JsonResponse;
// use Illuminate\Http\RedirectResponse;
// use Illuminate\Support\Facades\Redirect;
// use Illuminate\Http\Response;
// use Illuminate\View\View;

// models

// load db facade
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

// load validation
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

// load batch and queue
// use Illuminate\Bus\Batch;
// use Illuminate\Support\Facades\Bus;

// load email & notification
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

// load pdf
// use Barryvdh\DomPDF\Facade\Pdf;

// load helper
use Illuminate\Support\{
                          Arr,
                          Str,
                          Collection
                        };
use Illuminate\Support\Facades\Storage;

// load Carbon library
use \Carbon\{
              Carbon,
              CarbonPeriod,
              CarbonInterval
            };

use Session;
use Throwable;
use Exception;
use Log;

/* helper */
use App\Helpers\UnavailableDateTime;

// load model
use App\Models\{
  Login, Staff, Setting, Customer };
use App\Models\Sales\{
  OptUOM, OptMachine, OptSalesGetItem, OptMachineAccessories
};

use App\Models\HumanResources\{
  HRLeave,
  HRLeaveMC,
  HROvertime,
  HROutstation,
  HRLeaveAmend,
  HRAttendance,
  HRLeaveAnnual,
  HROvertimeRange,
  HRLeaveMaternity,
  HRLeaveApprovalHR,
  HRHolidayCalendar,
  HRLeaveApprovalHOD,
  HRLeaveEntitlement,
  HRLeaveApprovalBackup,
  HROutstationAttendance,
  HRLeaveApprovalDirector,
  HRLeaveApprovalSupervisor,
  DepartmentPivot,
  OptRace,
  OptTcms,
  OptBranch,
  OptGender,
  OptStatus,
  OptCountry,
  OptDayType,
  OptCategory,
  OptDivision,
  OptReligion,
  OptAuthorise,
  OptLeaveType,
  OptWeekDates,
  OptDepartment,
  OptLeaveStatus,
  OptWorkingHour,
  OptHealthStatus,
  OptRelationship,
  OptRestdayGroup,
  OptMaritalStatus,
  OptEducationLevel,
  OptTaxExemptionPercentage,
};

class AjaxSupportController extends Controller
{
  public function loginuser(Request $request): JsonResponse
  {
    $valid = true;
    $log = Login::all();
    foreach($log as $k) {
      if($k->username == $request->username) {
        $valid = false;
      }
    }
    return response()->json([
      'valid' => $valid,
    ]);
  }

  public function icuser(Request $request): JsonResponse
  {
    $valid = true;
    $log = Staff::all();
    foreach($log as $k) {
      if($k->ic == $request->ic) {
        $valid = false;
      }
    }
    return response()->json([
      'valid' => $valid,
    ]);
  }

  public function emailuser(Request $request): JsonResponse
  {
    $valid = true;
    $log = Staff::all();
    foreach($log as $k) {
      if($k->email == $request->email) {
        $valid = false;
      }
    }
    return response()->json([
      'valid' => $valid,
    ]);
  }

  // get types of leave according to user
  public function leaveType(Request $request): JsonResponse
  {
    $user = Staff::find($request->id);
    // tahun lepas
    $pastyear = now()->subYear()->year;
    // tahun sekarang ni
    $year = now()->year;
    $nextyear = Carbon::parse(now()->addYear())->year;
    // dd(Setting::find(6)->active, $year, $nextyear);

    // group entitlement by year
    for ($i = ((Setting::find(7)->active != 1)?$pastyear:$year); $i <= ((Setting::find(6)->active != 1)?$nextyear:$year); ++$i) {

      // checking for annual leave, mc, nrl and maternity
      // hati-hati dgn yg ni sbb melibatkan masa
      $leaveAL =  $user->hasmanyleaveannual()->where('year', $i)->first();
      $leaveMC =  $user->hasmanyleavemc()->where('year', $i)->first();
      $leaveMa =  $user->hasmanyleavematernity()->where('year', $i)->first();
      // cari kalau ada replacement leave
      $oi = $user->hasmanyleavereplacement()?->where('leave_balance', '<>', 0)->whereYear('date_start', $i)->get();

      // dd($oi?->sum('leave_balance'));

      if(Setting::where('id', 3)->first()->active == 1){                                    // special unpaid leave activated
        if($user->gender_id == 1){                                              // laki
          if($oi?->sum('leave_balance') < 0.5){                                     // laki | no nrl
            if($leaveAL?->annual_leave_balance < 0.5){                                  // laki | no nrl | no al
              if($leaveMC?->mc_leave_balance < 0.5){                                  // laki | no nrl | no al | no mc
                $er[$i] = OptLeaveType::whereIn('id', [3,6,9,11,12])->get()->sortBy('sorting');
              } else {                                                // laki | no nrl | no al | mc
                $er[$i] = OptLeaveType::whereIn('id', [2,3,6,9,12])->get()->sortBy('sorting');
              }
            } else {                                                  // laki | no nrl | al
              if($leaveMC?->mc_leave_balance < 0.5){                                  // laki | no nrl | al | no mc
                $er[$i] = OptLeaveType::whereIn('id', [1,5,9,11,12])->get()->sortBy('sorting');
              } else {                                                // laki | no nrl | al | mc
                $er[$i] = OptLeaveType::whereIn('id', [1,2,5,9,12])->get()->sortBy('sorting');
              }
            }
          } else {                                                    // laki | nrl
            if($leaveAL?->annual_leave_balance < 0.5){                                  // laki | nrl | no al
              if($leaveMC?->mc_leave_balance < 0.5){                                  // laki | nrl | no al | no mc
                $er[$i] = OptLeaveType::whereIn('id', [3,4,6,9,10,11,12])->get()->sortBy('sorting');
              } else {                                                // laki | nrl | no al | mc
                $er[$i] = OptLeaveType::whereIn('id', [2,3,4,6,9,10,12])->get()->sortBy('sorting');
              }
            } else {                                                  // laki | nrl | al
              if($leaveMC?->mc_leave_balance < 0.5){                                  // laki | nrl | al | no mc
                $er[$i] = OptLeaveType::whereIn('id', [1,4,5,9,10,11,12])->get()->sortBy('sorting');
              } else {                                                // laki | nrl | al | mc
                $er[$i] = OptLeaveType::whereIn('id', [1,2,4,5,9,10,12])->get()->sortBy('sorting');
              }
            }
          }
        } else {                                                      // pempuan
          if($oi?->sum('leave_balance') < 0.5){                                     // pempuan | no nrl
            if($leaveAL?->annual_leave_balance < 0.5){                                  // pempuan | no nrl | no al
              if($leaveMC?->mc_leave_balance < 0.5){                                  // pempuan | no nrl | no al | no mc
                if($leaveMa?->maternity_leave_balance < 0.5){                           // pempuan | no nrl | no al |  no mc | no maternity
                  $er[$i] = OptLeaveType::whereIn('id', [3,6,9,11,12])->get()->sortBy('sorting');
                } else {                                              // pempuan | no nrl | no al |  no mc | maternity
                  $er[$i] = OptLeaveType::whereIn('id', [3,6,7,9,11,12])->get()->sortBy('sorting');
                }
              } else {                                                // pempuan | no nrl | no al | mc
                if($leaveMa?->maternity_leave_balance < 0.5){                           // pempuan | no nrl | no al | mc | no maternity
                  $er[$i] = OptLeaveType::whereIn('id', [2,3,6,9,12])->get()->sortBy('sorting');
                } else {                                              // pempuan | no nrl | no al | mc | maternity
                  $er[$i] = OptLeaveType::whereIn('id', [2,3,6,7,9,12])->get()->sortBy('sorting');
                }
              }
            } else {                                                  // pempuan | no nrl | al
              if($leaveMC?->mc_leave_balance < 0.5){                                  // pempuan | no nrl | al | no mc
                if($leaveMa?->maternity_leave_balance < 0.5){                           // pempuan | no nrl | al | no mc | no maternity
                  $er[$i] = OptLeaveType::whereIn('id', [1,5,9,11,12])->get()->sortBy('sorting');
                } else {                                              // pempuan | no nrl | al | no mc | maternity
                  $er[$i] = OptLeaveType::whereIn('id', [1,5,7,9,11,12])->get()->sortBy('sorting');
                }
              } else {                                                // pempuan | no nrl | al | mc
                if($leaveMa?->maternity_leave_balance < 0.5){                           // pempuan | no nrl | al | mc | no maternity
                  $er[$i] = OptLeaveType::whereIn('id', [1,2,5,9,12])->get()->sortBy('sorting');
                } else {                                              // pempuan | no nrl | al | mc | maternity
                  $er[$i] = OptLeaveType::whereIn('id', [1,2,5,7,9,12])->get()->sortBy('sorting');
                }
              }
            }
          } else {                                                    // pempuan | nrl
            if($leaveAL?->annual_leave_balance < 0.5){                                  // pempuan | nrl | no al
              if($leaveMC?->mc_leave_balance < 0.5){                                  // pempuan | nrl | no al | no mc
                if($leaveMa?->maternity_leave_balance < 0.5){                           // pempuan | nrl | no al | no mc | no maternity
                  $er[$i] = OptLeaveType::whereIn('id', [3,4,6,7,9,10,11,12])->get()->sortBy('sorting');
                } else {                                              // pempuan | nrl | no al | no mc | maternity
                  $er[$i] = OptLeaveType::whereIn('id', [3,4,6,7,9,10,11,12])->get()->sortBy('sorting');
                }
              } else {                                                // pempuan | nrl | no al | mc
                if($leaveMa?->maternity_leave_balance < 0.5){                           // pempuan | nrl | no al | mc | no maternity
                  $er[$i] = OptLeaveType::whereIn('id', [2,3,4,6,9,10,12])->get()->sortBy('sorting');
                } else {                                              // pempuan | nrl | no al | mc | maternity
                  $er[$i] = OptLeaveType::whereIn('id', [2,3,4,6,7,9,10,12])->get()->sortBy('sorting');
                }
              }
            } else {                                                  // pempuan | nrl | al
              if($leaveMC?->mc_leave_balance < 0.5){                                  // pempuan | nrl | al | no mc
                if($leaveMa?->maternity_leave_balance < 0.5){                           // pempuan | nrl | al | no mc | no maternity
                  $er[$i] = OptLeaveType::whereIn('id', [1,4,5,9,10,11,12])->get()->sortBy('sorting');
                } else {                                              // pempuan | nrl | al | no mc | maternity
                  $er[$i] = OptLeaveType::whereIn('id', [1,4,5,7,9,10,11,12])->get()->sortBy('sorting');
                }
              } else {                                                // pempuan | nrl | al | mc
                if($leaveMa?->maternity_leave_balance < 0.5){                           // pempuan | nrl | al | mc | no maternity
                  $er[$i] = OptLeaveType::whereIn('id', [1,2,4,5,9,10,12])->get()->sortBy('sorting');
                } else {                                              // pempuan | nrl | al | mc | maternity
                  $er[$i] = OptLeaveType::whereIn('id', [1,2,4,5,7,9,10,12])->get()->sortBy('sorting');
                }
              }
            }
          }
        }
      } else {                                                        // special unpaid leave deactivated
        if($user->gender_id == 1){                                              // laki
          if($oi?->sum('leave_balance') < 0.5){                                     // laki | no nrl
            if($leaveAL?->annual_leave_balance < 0.5){                                  // laki | no nrl | no al
              if($leaveMC?->mc_leave_balance < 0.5){                                  // laki | no nrl | no al | no mc
                $er[$i] = OptLeaveType::whereIn('id', [3,6,9,11])->get()->sortBy('sorting');
              } else {                                                // laki | no nrl | no al | mc
                $er[$i] = OptLeaveType::whereIn('id', [2,3,6,9])->get()->sortBy('sorting');
              }
            } else {                                                  // laki | no nrl | al
              if($leaveMC?->mc_leave_balance < 0.5){                                  // laki | no nrl | al | no mc
                $er[$i] = OptLeaveType::whereIn('id', [1,5,9,11])->get()->sortBy('sorting');
              } else {                                                // laki | no nrl | al | mc
                $er[$i] = OptLeaveType::whereIn('id', [1,2,5,9])->get()->sortBy('sorting');
              }
            }
          } else {                                                    // laki | nrl
            if($leaveAL?->annual_leave_balance < 0.5){                                  // laki | nrl | no al
              if($leaveMC?->mc_leave_balance < 0.5){                                  // laki | nrl | no al | no mc
                $er[$i] = OptLeaveType::whereIn('id', [3,4,6,9,10,11])->get()->sortBy('sorting');
              } else {                                                // laki | nrl | no al | mc
                $er[$i] = OptLeaveType::whereIn('id', [2,3,4,6,9,10])->get()->sortBy('sorting');
              }
            } else {                                                  // laki | nrl | al
              if($leaveMC?->mc_leave_balance < 0.5){                                  // laki | nrl | al | no mc
                $er[$i] = OptLeaveType::whereIn('id', [1,4,5,9,10,11])->get()->sortBy('sorting');
              } else {                                                // laki | nrl | al | mc
                $er[$i] = OptLeaveType::whereIn('id', [1,2,4,5,9,10])->get()->sortBy('sorting');
              }
            }
          }
        } else {                                                      // pempuan
          if($oi?->sum('leave_balance') < 0.5){                                     // pempuan | no nrl
            if($leaveAL?->annual_leave_balance < 0.5){                                  // pempuan | no nrl | no al
              if($leaveMC?->mc_leave_balance < 0.5){                                  // pempuan | no nrl | no al | no mc
                if($leaveMa?->maternity_leave_balance < 0.5){                           // pempuan | nrl | al | mc | no maternity
                  $er[$i] = OptLeaveType::whereIn('id', [3,6,9,11])->get()->sortBy('sorting');
                } else {                                              // pempuan | nrl | al | mc | maternity
                  $er[$i] = OptLeaveType::whereIn('id', [3,6,7,9,11])->get()->sortBy('sorting');
                }
              } else {                                                // pempuan | no nrl | no al | mc
                if($leaveMa?->maternity_leave_balance < 0.5){                           // pempuan | no nrl | no al | mc | no maternity
                  $er[$i] = OptLeaveType::whereIn('id', [2,3,6,9])->get()->sortBy('sorting');
                } else {                                              // pempuan | no nrl | no al | mc | maternity
                  $er[$i] = OptLeaveType::whereIn('id', [2,3,6,7,9])->get()->sortBy('sorting');
                }
              }
            } else {                                                  // pempuan | no nrl | al
              if($leaveMC?->mc_leave_balance < 0.5){                                  // pempuan | no nrl | al | no mc
                if($leaveMa?->maternity_leave_balance < 0.5){                           // pempuan | no nrl | al | no mc | no maternity
                  $er[$i] = OptLeaveType::whereIn('id', [1,5,7,9,11])->get()->sortBy('sorting');
                } else {                                              // pempuan | no nrl | al | no mc | maternity
                  $er[$i] = OptLeaveType::whereIn('id', [1,5,7,9,11])->get()->sortBy('sorting');
                }
              } else {                                                // pempuan | no nrl | al | mc
                if($leaveMa?->maternity_leave_balance < 0.5){                           // pempuan | no nrl | al | mc | no maternity
                  $er[$i] = OptLeaveType::whereIn('id', [1,2,5,9])->get()->sortBy('sorting');
                } else {                                              // pempuan | no nrl | al | mc | maternity
                  $er[$i] = OptLeaveType::whereIn('id', [1,2,5,7,9])->get()->sortBy('sorting');
                }
              }
            }
          } else {                                                    // pempuan | nrl
            if($leaveAL?->annual_leave_balance < 0.5){                                  // pempuan | nrl | no al
              if($leaveMC?->mc_leave_balance < 0.5){                                  // pempuan | nrl | no al | no mc
                if($leaveMa?->maternity_leave_balance < 0.5){                           // pempuan | nrl | no al | no mc | no maternity
                  $er[$i] = OptLeaveType::whereIn('id', [3,4,6,9,10,11])->get()->sortBy('sorting');
                } else {                                              // pempuan | nrl | no al | no mc | maternity
                  $er[$i] = OptLeaveType::whereIn('id', [3,4,6,7,9,10,11])->get()->sortBy('sorting');
                }
              } else {                                                // pempuan | nrl | no al | mc
                if($leaveMa?->maternity_leave_balance < 0.5){                           // pempuan | nrl | no al | mc | no maternity
                  $er[$i] = OptLeaveType::whereIn('id', [2,3,4,6,9,10])->get()->sortBy('sorting');
                } else {                                              // pempuan | nrl | no al | mc | maternity
                  $er[$i] = OptLeaveType::whereIn('id', [2,3,4,6,7,9,10])->get()->sortBy('sorting');
                }
              }
            } else {                                                  // pempuan | nrl | al
              if($leaveMC?->mc_leave_balance < 0.5){                                  // pempuan | nrl | al | no mc
                if($leaveMa?->maternity_leave_balance < 0.5){                           // pempuan | nrl | al | no mc | no maternity
                  $er[$i] = OptLeaveType::whereIn('id', [1,4,5,9,10,11])->get()->sortBy('sorting');
                } else {                                              // pempuan | nrl | al | no mc | maternity
                  $er[$i] = OptLeaveType::whereIn('id', [1,4,5,7,9,10,11])->get()->sortBy('sorting');
                }
              } else {                                                // pempuan | nrl | al | mc
                if($leaveMa?->maternity_leave_balance < 0.5){                           // pempuan | nrl | al | no mc | no maternity
                  $er[$i] = OptLeaveType::whereIn('id', [1,2,4,5,9,10])->get()->sortBy('sorting');
                } else {                                              // pempuan | nrl | al | no mc | maternity
                  $er[$i] = OptLeaveType::whereIn('id', [1,2,4,5,7,9,10])->get()->sortBy('sorting');
                }
              }
            }
          }
        }
      }
    }
    // dd($i, $er);


    // https://select2.org/data-sources/formats
    // $cuti = [];
    foreach ($er as $key => $values) {
      $g = ['text' => $key, 'children' => []];
      foreach ($values as $value) {
        $g['children'][] = [
                    'id' => $value->id,
                    'text' => $value->leave_type_code.' | '.$value->leave_type,
                  ];
      }
      $cuti['results'][] = $g;
      // $cuti['pagination'] = ['more' => true];
    }
    return response()->json( $cuti );
  }

  public function unavailabledate(Request $request): JsonResponse
  {
    $blockdate = UnavailableDateTime::blockDate($request->id);

    $lusa1 = Carbon::now()->addDays(Setting::find(5)->active - 1)->format('Y-m-d');
    $period2 = \Carbon\CarbonPeriod::create(Carbon::now()->format('Y-m-d'), '1 days', $lusa1);
    $lusa = [];
    // dd(Setting::find(4)->active);
    if(Setting::find(4)->active == 1){                              // enable N days checking : 1
      foreach ($period2 as $key1) {
        $lusa[] = $key1->format('Y-m-d');
      }
    }
    // dd($lusa);

    if($request->type == 1){
      $unavailableleave = Arr::collapse([$blockdate, $lusa]);
    }
    if($request->type == 2) {
      $unavailableleave = $blockdate;
    }
    return response()->json($unavailableleave);
  }

  public function unblockhalfdayleave(Request $request): JsonResponse
  {
    $blocktime = UnavailableDateTime::unblockhalfdayleave($request->id);
    return response()->json($blocktime);
  }

  public function backupperson(Request $request): JsonResponse
  {
    // we r going to find a backup person
    // 1st, we need to take a look into his/her department.
    // dd($request->all());
    $user = Staff::find($request->id);
    // dd($request->id, $user);
    $dept = $user->belongstomanydepartment()->wherePivot('main', 1)->first();
    $userindept = $dept->belongstomanystaff()->where('active', 1)->get();
    // dd($dept, $userindept);

    // backup from own department if he/she have
    // https://select2.org/data-sources/formats
    $backup['results'][] = [];
    if ($userindept->count()) {
      foreach($userindept as $key){
        if($key->id != $user->id){
          $chkavailability = $key->hasmanyleave()
                  ->where(function (Builder $query) use ($request){
                    // $query->whereDate('date_time_start', '>=', '2023-09-21')
                    // ->whereDate('date_time_start', '<=', '2023-09-22');
                    $query->whereDate('date_time_start', '<=', $request->date_from)
                    ->whereDate('date_time_end', '>=', $request->date_to);
                  })
                  ->where(function (Builder $query){
                    $query->where('leave_type_id', '<>', 9)
                    ->where(function (Builder $query){
                      $query->where('half_type_id', '<>', 2)
                      ->orWhereNull('half_type_id');
                    });
                  })
                  ->where(function (Builder $query){
                    $query->whereIn('leave_status_id', [5,6])
                      ->orWhereNull('leave_status_id');
                  })
                  ->get();
                  // ->dumpRawSql();

          // dump($chkavailability);
          if($key->id != $chkavailability->first()?->staff_id) {
            $backup['results'][] = [
                        'id' => $key->id,
                        'text' => $key->name,
                      ];
          }
        }
      }
    }

    $crossbacku = $user->crossbackupto()?->wherePivot('active', 1)->get();
    // $crossbackup['results'][] = [];
    if($crossbacku) {
      foreach($crossbacku as $key){
        $chkavailability = $key->hasmanyleave()
                ->where(function (Builder $query) use ($request){
                  $query->whereDate('date_time_start', '<=', $request->date_from)
                  ->whereDate('date_time_end', '>=', $request->date_to);
                })
                ->where(function (Builder $query){
                  $query->where('leave_type_id', '<>', 9)
                  ->where(function (Builder $query){
                    $query->where('half_type_id', '<>', 2)
                    ->orWhereNull('half_type_id');
                  });
                })
                ->where(function (Builder $query){
                  $query->whereIn('leave_status_id', [5,6])
                    ->orWhereNull('leave_status_id');
                })
                ->get();
                // ->dumpRawSql();

        if($key->id != $chkavailability->first()?->staff_id) {
          $backup['results'][] = [
                      'id' => $key->id,
                      'text' => $key->name,
                    ];
        }
      }
    }
    // $allbackups = Arr::collapse([$backup, $crossbackup]);
    // $allbackups = array_merge_recursive($backup, $crossbackup);
    // return response()->json( $allbackups );
    return response()->json( $backup );
  }

  public function timeleave(Request $request): JsonResponse
  {
    $whtime = UnavailableDateTime::workinghourtime($request->date, $request->id);
    return response()->json($whtime->first());
  }

  public function leavestatus(Request $request): JsonResponse
  {

    // $ls['results'] = [];
    if(\Auth::user()->belongstostaff->div_id != 2) {
      $c = OptLeaveStatus::where('id', '<>', 6)->where('id', '<>', 3)->get();
    } else {
      $c = OptLeaveStatus::where('id', '<>', 3)->get();
    }
    foreach ($c as $v) {
      $ls['results'][] = [
                  'id' => $v->id,
                  'text' => $v->status
                ];
    }
    return response()->json($ls);
  }

  public function staffcrossbackup(Request $request): JsonResponse
  {
    $s = Staff::join('logins', 'staffs.id', '=', 'logins.staff_id')
          ->where('staffs.active', 1)
          ->where('logins.active', 1)
          ->where(function(Builder $query) use ($request) {
            $query->where('logins.username','LIKE','%'.$request->search.'%')
            ->orWhere('staffs.name', 'LIKE', '%'.$request->search.'%');
          })
          ->select('staffs.id as staffid', 'staffs.name', 'logins.username')
          ->orderBy('username')
          ->get();
    foreach ($s as $v) {
        $ls['results'][] = [
                    'id' => $v->staffid,
                    'text' => $v->username.'  '.$v->name
                  ];
    }
    return response()->json($ls);
  }

  public function department(Request $request): JsonResponse
  {
    $au = DepartmentPivot::where([['category_id', $request->category_id], ['branch_id', $request->branch_id]])->get();
    foreach ($au as $key) {
      if($key->id != 31) {
        $cuti['results'][] = [
                    'id' => $key->id,
                    'text' => $key->department.' | '.$key->code,
                  ];
        // $cuti['pagination'] = ['more' => true];
        //for jquery-chained
        // $cuti[$key->id] = $key->department.' | '.$key->code;
      }
    }
    return response()->json( $cuti );
  }

  public function restdaygroup(Request $request): JsonResponse
  {
    $au = OptRestdayGroup::where('group','LIKE','%'.$request->search.'%')->get();
    foreach ($au as $key) {
      $cuti['results'][] = [
                  'id' => $key->id,
                  'text' => $key->group,
                ];
      // $cuti['pagination'] = ['more' => true];
      // $cuti[$key->id] = $key->department.' | '.$key->code;
    }
    return response()->json( $cuti );
  }

  public function authorise(Request $request): JsonResponse
  {
    // https://select2.org/data-sources/formats
    $au = OptAuthorise::where('group','LIKE','%'.$request->search.'%')->get();
    foreach ($au as $key) {
      $cuti['results'][] = [
                  'id' => $key->id,
                  'text' => $key->authorise,
                ];
      // $cuti['pagination'] = ['more' => true];
    }
    return response()->json( $cuti );
  }

  public function branch(Request $request): JsonResponse
  {
    // https://select2.org/data-sources/formats
    $au = OptBranch::where('location','LIKE','%'.$request->search.'%')->get();
    foreach ($au as $key) {
      $cuti['results'][] = [
                  'id' => $key->id,
                  'text' => $key->location,
                ];
      // $cuti['pagination'] = ['more' => true];
    }
    return response()->json( $cuti );
  }

  public function customer(Request $request): JsonResponse
  {
    $cuti = [];
    // https://select2.org/data-sources/formats
    if ($request->has('search')) {
      $au = Customer::orderBy('customer')
              ->where('customer','LIKE','%'.$request->search.'%')
              ->get();
    } elseif($request->has('id')) {
      $au = Customer::orderBy('customer')
              ->Where('id', $request->id)
              ->get();
    } else {
      $au = Customer::orderBy('customer')
              ->get();
    }
    foreach ($au as $key) {
      $cuti['results'][] = [
                  'id' => $key->id,
                  'text' => $key->customer,
                ];
      // $cuti['pagination'] = ['more' => true];
    }
    return response()->json( $cuti );
  }

  public function country(Request $request): JsonResponse
  {
    // https://select2.org/data-sources/formats
    $au = OptCountry::where('country','LIKE','%'.$request->search.'%')->get();
    foreach ($au as $key) {
      $cuti['results'][] = [
                  'id' => $key->id,
                  'text' => $key->country,
                ];
      // $cuti['pagination'] = ['more' => true];
    }
    return response()->json( $cuti );
  }

  public function educationlevel(Request $request): JsonResponse
  {
    // https://select2.org/data-sources/formats
    $au = OptEducationLevel::where('education_level','LIKE','%'.$request->search.'%')->get();
    foreach ($au as $key) {
      $cuti['results'][] = [
                  'id' => $key->id,
                  'text' => $key->education_level,
                ];
      // $cuti['pagination'] = ['more' => true];
    }
    return response()->json( $cuti );
  }

  public function gender(Request $request): JsonResponse
  {
    // https://select2.org/data-sources/formats
    $au = OptGender::where('gender','LIKE','%'.$request->search.'%')->get();
    foreach ($au as $key) {
      $cuti['results'][] = [
                  'id' => $key->id,
                  'text' => $key->gender,
                ];
      // $cuti['pagination'] = ['more' => true];
    }
    return response()->json( $cuti );
  }

  public function uom(Request $request): JsonResponse
  {
    // https://select2.org/data-sources/formats
    if ($request->has('search')) {
      $au = OptUOM::where('uom','LIKE','%'.$request->search.'%')->get();
    } elseif ($request->has('id')) {
      $au = OptUOM::where('id', $request->id)->get();
    } else {
      $au = OptUOM::all();
    }
    foreach ($au as $key) {
      $cuti['results'][] = [
                  'id' => $key->id,
                  'text' => $key->uom,
                ];
      // $cuti['pagination'] = ['more' => true];
    }
    return response()->json( $cuti );
  }

  public function week_dates(Request $request): JsonResponse
  {
    // https://select2.org/data-sources/formats
    if ($request->has('search')) {
      $au = OptWeekDates::where('week','LIKE','%'.$request->search.'%')->get();
    } elseif ($request->has('id')) {
      $au = OptWeekDates::where('id', $request->id)->get();
    } else {
      $au = OptWeekDates::whereDate('date_from', '>=', now()->startOfYear())->whereDate('date_to', '<=', now()->endOfWeek())->get();
    }
    foreach ($au as $key) {
      $cuti['results'][] = [
                  'id' => $key->id,
                  'text' => $key->week.' ('.Carbon::parse($key->date_from)->format('j M Y').' -> '.Carbon::parse($key->date_to)->format('j M Y').')',
                ];
      // $cuti['pagination'] = ['more' => true];
    }
    return response()->json( $cuti );
  }

  // public function jdescgetitem(Request $request): JsonResponse
  // {
  //  // https://select2.org/data-sources/formats
  //  $au = SalesGetItem::where('get_item','LIKE','%'.$request->search.'%')->get();
  //  foreach ($au as $key) {
  //    $cuti['results'][] = [
  //                'id' => $key->id,
  //                'text' => $key->get_item,
  //              ];
  //    // $cuti['pagination'] = ['more' => true];
  //  }
  //  return response()->json( $cuti );
  // }

  public function status(Request $request): JsonResponse
  {
    // https://select2.org/data-sources/formats
    $au = OptStatus::where('status','LIKE','%'.$request->search.'%')->get();
    foreach ($au as $key) {
      $cuti['results'][] = [
                  'id' => $key->id,
                  'text' => $key->status,
                ];
      // $cuti['pagination'] = ['more' => true];
    }
    return response()->json( $cuti );
  }

  public function machine(Request $request): JsonResponse
  {
    // https://select2.org/data-sources/formats
    if ($request->has('search')) {
      $au = OptMachine::where('machine','LIKE','%'.$request->search.'%')->orderBy('id')->get();
    } elseif ($request->has('id')) {
      $au = OptMachine::where('id', $request->id)->get();
    } elseif ($request->has('idNotIn')) {
      $au = OptMachine::where('id', $request->idNotIn)->get();
    } else {
      $au = OptMachine::all();
    }
    foreach ($au as $key) {
      $cuti['results'][] = [
                  'id' => $key->id,
                  'text' => $key->machine,
                ];
      // $cuti['pagination'] = ['more' => true];
    }
    return response()->json( $cuti );
  }

  public function machineaccessories(Request $request): JsonResponse
  {
    $values = OptMachineAccessories::when($request->search, function($q1) use ($request) {
                $q1->where('accessory','LIKE','%'.$request->search.'%');
              })
            ->when($request->id, function($q2) use ($request) {
              $q2->where('id', $request->id);
            })
            ->when($request->idNotIn, function($q3) use ($request) {
              $q3->whereNotIn('id', $request->idNotIn);
            })
            ->when($request->machine_id, function($q3) use ($request) {
              $q3->where('machine_id', $request->machine_id);
            })
            ->get();
    return response()->json( $values );
  }

  public function category(Request $request): JsonResponse
  {
    // https://select2.org/data-sources/formats
    $au = OptCategory::where('category','LIKE','%'.$request->search.'%')->get();
    foreach ($au as $key) {
      $cuti['results'][] = [
                  'id' => $key->id,
                  'text' => $key->category,
                ];
      // $cuti['pagination'] = ['more' => true];
    }
    return response()->json( $cuti );
  }

  public function healthstatus(Request $request): JsonResponse
  {
    // https://select2.org/data-sources/formats
    $au = OptHealthStatus::where('health_status','LIKE','%'.$request->search.'%')->get();
    foreach ($au as $key) {
      $cuti['results'][] = [
                  'id' => $key->id,
                  'text' => $key->health_status,
                ];
      // $cuti['pagination'] = ['more' => true];
    }
    return response()->json( $cuti );
  }

  public function maritalstatus(Request $request): JsonResponse
  {
    // https://select2.org/data-sources/formats
    $au = OptMaritalStatus::where('marital_status','LIKE','%'.$request->search.'%')->get();
    foreach ($au as $key) {
      $cuti['results'][] = [
                  'id' => $key->id,
                  'text' => $key->marital_status,
                ];
      // $cuti['pagination'] = ['more' => true];
    }
    return response()->json( $cuti );
  }

  public function race(Request $request): JsonResponse
  {
    // https://select2.org/data-sources/formats
    $au = OptRace::where('race','LIKE','%'.$request->search.'%')->get();
    foreach ($au as $key) {
      $cuti['results'][] = [
                  'id' => $key->id,
                  'text' => $key->race,
                ];
      // $cuti['pagination'] = ['more' => true];
    }
    return response()->json( $cuti );
  }

  public function religion(Request $request): JsonResponse
  {
    // https://select2.org/data-sources/formats
    $au = OptReligion::where('religion','LIKE','%'.$request->search.'%')->get();
    foreach ($au as $key) {
      $cuti['results'][] = [
                  'id' => $key->id,
                  'text' => $key->religion,
                ];
      // $cuti['pagination'] = ['more' => true];
    }
    return response()->json( $cuti );
  }

  public function taxexemptionpercentage(Request $request): JsonResponse
  {
    // https://select2.org/data-sources/formats
    $au = OptTaxExemptionPercentage::where('tax_exemption_percentage','LIKE','%'.$request->search.'%')->get();
    foreach ($au as $key) {
      $cuti['results'][] = [
                  'id' => $key->id,
                  'text' => $key->tax_exemption_percentage,
                ];
      // $cuti['pagination'] = ['more' => true];
    }
    return response()->json( $cuti );
  }

  public function relationship(Request $request): JsonResponse
  {
    // https://select2.org/data-sources/formats
    $au = OptRelationship::where('relationship','LIKE','%'.$request->search.'%')->get();
    foreach ($au as $key) {
      $cuti['results'][] = [
                  'id' => $key->id,
                  'text' => $key->relationship,
                ];
      // $cuti['pagination'] = ['more' => true];
    }
    return response()->json( $cuti );
  }

  public function division(Request $request): JsonResponse
  {
    // https://select2.org/data-sources/formats
    $au = OptDivision::where('div','LIKE','%'.$request->search.'%')->get();
    foreach ($au as $key) {
      $cuti['results'][] = [
                  'id' => $key->id,
                  'text' => $key->div,
                ];
      // $cuti['pagination'] = ['more' => true];
    }
    return response()->json( $cuti );
  }

  public function leaveevents(Request $request): JsonResponse
  {
    // dd($request->all());
    $now = now();
    $nowYear = $now->copy()->year;
    $lastYear = $now->copy()->subYear()->year;
    $nextYear = $now->copy()->addYear()->year;
    // please note that the full calendar for end date is EXCLUSIVE
    // https://fullcalendar.io/docs/event-object
    $l1 = HRLeave::
    where(function (Builder $query) use ($lastYear, $nowYear, $nextYear) {
      $query->whereYear('date_time_start', '>=', $lastYear)
      ->whereYear('date_time_end', '<=', $nextYear);
    })
    ->where(function (Builder $query){
      $query->whereIn('leave_status_id', [5,6])
      ->orWhereNull('leave_status_id');
    })
    // ->ddRawSql();
    ->get();
    // dump($l1);
    // $l2 = [];
    foreach ($l1 as $v) {
      $dts = \Carbon\Carbon::parse($v->date_time_start)->format('Y');
      $dte = \Carbon\Carbon::parse($v->date_time_end)->addDay()->format('j M Y g:i a');
      // only available if only now is before date_time_start and active is 1
      $dtsl = \Carbon\Carbon::parse( $v->date_time_start );
      $dt = \Carbon\Carbon::now()->lte( $dtsl );

      if (($v->leave_type_id == 9) || ($v->leave_type_id != 9 && is_null($v->half_type_id == 2))) {
        $bool = false;
      } else {
        $bool = true;
      }
      $l2[] = [
          'title' => 'HR9-'.str_pad( $v->leave_no, 5, "0", STR_PAD_LEFT ).'/'.$v->leave_year,
          'start' => $v->date_time_start,
          'end' => Carbon::parse($v->date_time_end)->addDay(),
          'url' => route('hrleave.show', $v->id),
          'allDay' => $bool,
          // 'extendedProps' => [
                      // 'department' => 'BioChemistry'
                    // ],
          'description' => $v->belongstooptleavetype?->leave_type_code,
        ];
    }
      return response()->json( $l2 );
  }

  public function staffattendance(Request $request): JsonResponse
  {
    // this is for fullcalendar, its NOT INCLUSIVE for the last date
    // get the attandence 1st
    $attendance = HRAttendance::where('staff_id', $request->staff_id)->get();
    // foreach ($attendance as $s) {
    //  if (Carbon::parse($s->attend_date) != Carbon::SUNDAY) {

    //  }
    // }

    // mark sunday as a rest day
    $sun = Carbon::parse('2020-01-01')->toPeriod(Carbon::now()->addYear());
    foreach ($sun as $v) {
      if($v->dayOfWeek == 0){
        $l3[] = [
              'title' => 'RESTDAY',
              'start' => Carbon::parse($v)->format('Y-m-d'),
              'end' => Carbon::parse($v)->format('Y-m-d'),
              // 'url' => ,
              'allDay' => true,
              'description' => 'RESTDAY',
              // 'extendedProps' => [
              //            'department' => 'BioChemistry'
              //          ],
              'color' => 'grey',
              'textcolor' => 'white',
          ];
      }
    }

    // mark saturday as restday
    $sat = Staff::find($request->staff_id)->belongstorestdaygroup?->hasmanyrestdaycalendar()->get();
    if (!is_null($sat)) {
      foreach ($sat as $v) {
        $l4[] = [
              'title' => 'RESTDAY',
              'start' => Carbon::parse($v->saturday_date)->format('Y-m-d'),
              'end' => Carbon::parse($v->saturday_date)->format('Y-m-d'),
              // 'url' => ,
              'allDay' => true,
              'description' => 'RESTDAY',
              // 'extendedProps' => [
              //            'department' => 'BioChemistry'
              //          ],
              'color' => 'grey',
              'textcolor' => 'white',
          ];
      }
    } else {
      $l4[] = [];
    }

    // mark all holiday
    $hdate = HRHolidayCalendar::
        where(function (Builder $query){
          $query->whereYear('date_start', '<=', Carbon::now()->format('Y'))
          ->orWhereYear('date_end', '>=', Carbon::now()->addYear(1)->format('Y'));
        })
        ->get();
        // ->ddRawSql();
    if (!is_null($hdate)) {
      foreach ($hdate as $v) {
        $l1[] = [
              'title' => $v->holiday,
              'start' => $v->date_start,
              'end' => Carbon::parse($v->date_end)->addDay(),
              // 'url' => ,
              'allDay' => true,
              // 'extendedProps' => [
              //            'department' => 'BioChemistry'
              //          ],
              'description' => $v->holiday??'null',
              'color' => 'blue',
              'textColor' => 'white',
          ];
      }
    } else {
      $l1[] = [];
    }

    // looking for leave of each staff
    $l = HRLeave::where('staff_id', $request->staff_id)
          ->where(function (Builder $query) {
            $query->whereIn('leave_status_id', [5,6])->orWhereNull('leave_status_id');
          })
          ->get();

    // if(!is_null($l)) {
    if($l->count()) {
      foreach ($l as $v) {
        $dts = Carbon::parse($v->date_time_start)->format('Y');
        $dte = Carbon::parse($v->date_time_end)->addDay()->format('j M Y g:i a');
        // only available if only now is before date_time_start and active is 1
        $dtsl = Carbon::parse( $v->date_time_start );
        $dt = Carbon::now()->lte( $dtsl );

        if (($v->leave_type_id == 9) || ($v->leave_type_id != 9 && $v->half_type_id == 2) || ($v->leave_type_id != 9 && $v->half_type_id == 1)) {
          $l2[] = [
                'title' => 'HR9-'.str_pad( $v->leave_no, 5, "0", STR_PAD_LEFT ).'/'.$v->leave_year,
                'start' => $v->date_time_start,
                'end' => $v->date_time_end,
                'url' => route('hrleave.show', $v->id),
                'allDay' => false,
                // 'extendedProps' => [
                //            'department' => 'BioChemistry'
                //          ],
                'description' => $v->belongstooptleavetype?->leave_type_code??'null',
                'color' => 'purple',
                'textColor' => 'white',
                'borderColor' => 'purple',
            ];

        } else {
          $l2[] = [
              'title' => 'HR9-'.str_pad( $v->leave_no, 5, "0", STR_PAD_LEFT ).'/'.$v->leave_year,
              'start' => $v->date_time_start,
              'end' => Carbon::parse($v->date_time_end)->addDay(),
              'url' => route('hrleave.show', $v->id),
              'allDay' => true,
              // 'extendedProps' => [
                          // 'department' => 'BioChemistry'
                        // ],
              'description' => $v->belongstooptleavetype?->leave_type_code??'null',
              'color' => 'purple',
              'textColor' => 'white',
              'borderColor' => 'red',
            ];
        }
      }
    } else {
      $l2[] = [];
    }

    $outstation = HROutstation::where('staff_id', $request->staff_id)->where('active', 1)->get();
    if ($outstation->isNotEmpty()) {
      foreach ($outstation as $v) {
        $l5[] = [
              'title' => 'Outstation',
              'start' => $v->date_from,
              'end' => Carbon::parse($v->date_to)->addDay(),
              // 'url' => route('hrleave.show', $v->id),
              'allDay' => true,
              // 'extendedProps' => [
              //            'department' => 'BioChemistry'
              //          ],
              'description' => $v->belongstocustomer?->customer??$v->remarks??'null',
              'color' => 'teal',
              'textColor' => 'yellow',
              'borderColor' => 'green',
          ];
      }
    } else {
      $l5[] = [];
    }
    $l0 = array_merge($l1, $l2, $l3, $l4, $l5);
    return response()->json( $l0 );
  }

  public function staffattendancelist(Request $request): JsonResponse
  {
    $sa = HRAttendance::leftjoin('logins', 'hr_attendances.staff_id', '=', 'logins.staff_id')
      ->select('hr_attendances.staff_id', 'logins.username')
      ->where(function (Builder $query) use ($request){
        $query->whereDate('hr_attendances.attend_date', '>=', $request->from)
        ->whereDate('hr_attendances.attend_date', '<=', $request->to);
      })
      ->where('logins.active', 1)
      ->groupBy('hr_attendances.staff_id')
      ->orderBy('logins.username', 'ASC')
      ->get();
    foreach ($sa as $v) {
      $l0[] = ['id' => $v->staff_id, 'username' => $v->username, 'name' => Staff::find($v->staff_id)->name, 'branch' => Staff::find($v->staff_id)->belongstomanydepartment()->wherePivot('main', 1)->first()->branch_id, 'department' => Staff::find($v->staff_id)->belongstomanydepartment()->wherePivot('main', 1)->first()->department];
    }
    return response()->json( $l0 );
  }

  public function branchattendancelist(Request $request): JsonResponse
  {
    $sa = OptBranch::all();
    foreach ($sa as $v) {
      $l1[] = ['id' => $v->id, 'location' => $v->location];
    }
    return response()->json( $l1 );
  }

  public function staffpercentage(Request $request): JsonResponse
  {
    $st = Staff::find($request->id);          // need to check date join

    $soy = now()->copy()->startOfYear();        // early this year
    $lsoy = $soy->copy()->subYear();          // early last year
    // dd($lsoy);
    // dd($lsoy->diffInMonths(now()));

    for ($i = 0; $i <= $soy->diffInMonths(now()); $i++) {// take only 2 years back
      $sm = $soy->copy()->addMonth($i);
      $em = $sm->copy()->endOfMonth();
      // dump([$sm, $em]);

      $sq = $st->hasmanyattendance()
        ->whereDate('attend_date', '>=', $sm)
        ->whereDate('attend_date', '<=', $em)
        ->where('daytype_id', 1)
        ->get();
        // ->ddRawSql();

        $fdl = 0;
        $a = 0;
      if ($sq->count()) {
        $workday = $sq->count();                            // working days
        // dump([$workday, $sm->format('M Y')]);

        foreach ($sq as $s) {
          $fulldayleave = $s->belongstoleave()?->where(function (Builder $query){
          // $fulldayleave = HRLeave::where(function (Builder $query){
                      $query->where('leave_type_id', '<>', 9)
                      ->where(function (Builder $query){
                        $query->where('half_type_id', '<>', 2)
                        ->orWhereNull('half_type_id');
                      });
                    })
                    ->where(function (Builder $query){
                      $query->whereIn('leave_status_id', [5,6])
                      ->orWhereNull('leave_status_id');
                    })
                    ->where(function (Builder $query) use ($s){
                      $query->whereDate('date_time_start', '<=', $s->attend_date)
                      ->WhereDate('date_time_end', '>=', $s->attend_date);
                    })
                    ->get();
          $fdl += $fulldayleave->count();
          // dump($fulldayleave->count().' fulldayleave count');

          $absent = $s->where('attendance_type_id', 1)
          // $absent = HRAttendance::where('attendance_type_id', 1)
                ->whereDate('attend_date', $s->attend_date)
                ->where('daytype_id', 1)
                ->where('staff_id', $st->id)
                ->get();
          $a += $absent->count();
          // dump($absent.' absent');
        }
        $percentage = (($workday - $fdl - $a) / $workday) * 100;
      } else {
        $workday = 0;
        // $fdl = 0;
        $percentage = 0;
      }

      $chartdata[] = [
                'month' => $sm->format('M Y'),
                'percentage' => $percentage,
                'workdays' => $workday,
                'leaves' => $fdl,
                'absents' => $a,
                'working_days' => ($workday - $fdl - $a),
              ];
    }
    return response()->json($chartdata);
  }

  public function yearworkinghourstart(Request $request): JsonResponse
  {
    $valid = TRUE;

    $po = OptWorkingHour::groupBy('year')->select('year')->get();

    foreach ($po as $k1) {
      if($k1->year == \Carbon\Carbon::parse($request->effective_date_start)->format('Y')) {
        $valid = FALSE;
      }
    }

    return response()->json([
      'year1' => \Carbon\Carbon::parse($request->effective_date_start)->format('Y'),
      'valid' => $valid
    ]);
  }

  public function yearworkinghourend(Request $request): JsonResponse
  {
    $valid = TRUE;

    $po = OptWorkingHour::groupBy('year')->select('year')->get();

    foreach ($po as $k2) {
      if($k2->year == \Carbon\Carbon::parse($request->effective_date_end)->format('Y')) {
        $valid = FALSE;
      }
    }

    return response()->json([
      'year2' => \Carbon\Carbon::parse($request->effective_date_end)->format('Y'),
      'valid' => $valid
    ]);
  }

  public function hcaldstart(Request $request): JsonResponse
  {
    $valid = true;
    // echo $request->date_start;
    $u = HRHolidayCalendar::all();
    foreach($u as $p) {
      $b = \Carbon\CarbonPeriod::create($p->date_start, '1 day', $p->date_end);
      // echo $p->date_start;
      // echo $p->date_end;
      foreach ($b as $key) {
        // echo $key;
        if($key->format('Y-m-d') == $request->date_start) {
          $valid = false;
        }
      }
    }
    return response()->json([
      'valid' => $valid,
    ]);
  }

  public function hcaldend(Request $request): JsonResponse
  {
    $valid = true;
    // echo $request->date_end;
    $u = HRHolidayCalendar::all();
    foreach($u as $p) {
      $b = \Carbon\CarbonPeriod::create($p->date_start, '1 day', $p->date_end);
      // echo $p->date_start;
      // echo $p->date_end;
      foreach ($b as $key) {
        // echo $key;
        if($key->format('Y-m-d') == $request->date_end) {
          $valid = false;
        }
      }
    }
    return response()->json([
      'valid' => $valid,
    ]);
  }

  public function staffdaily(Request $request): JsonResponse
  {
    $now = now();
    $lsoy = $now->copy()->subDays(6);               // 6 days ago

    $b = 0;
    for ($i = 0; $i <= $lsoy->copy()->diffInDays($now->copy()); $i++) {
      $sd = $lsoy->copy()->addDays($i);
      // dump($sd);

      $sq = HRAttendance::whereDate('attend_date', $sd)->groupBy('attend_date')->get();
      // dump($sq);
      // exit;
      $workday1 = HRAttendance::whereDate('attend_date', $sd)->where('daytype_id', 1)->get();
      $workday = $workday1->count();

      // dump($sq->first()->daytype_id);
      // dump($workday);
      if ($workday >= 1) {
        if (Carbon::parse($sd)->dayOfWeek == Carbon::SATURDAY) {
          $working = OptDayType::find(1)->daytype;
        } else {
          $working = OptDayType::find($sq->first()->daytype_id)->daytype;
        }
        $workingpeople1 = HRAttendance::whereDate('attend_date', $sd)->where('daytype_id', 1)->whereNull('outstation_id')->whereNull('leave_id')->get();
        $workingpeople = $workingpeople1->count();
        $outstation1 = HRAttendance::whereDate('attend_date', $sd)->where('daytype_id', 1)->whereNotNull('outstation_id')->get();
        $outstation = $outstation1->count();
        $absent1 = HRAttendance::whereDate('attend_date', $sd)->where('daytype_id', 1)->where('attendance_type_id', 1)->get();
        $absent = $absent1->count();
        $halfabsent1 = HRAttendance::whereDate('attend_date', $sd)->where('daytype_id', 1)->where('attendance_type_id', 2)->get();
        $halfabsent = $halfabsent1->count();
        // $leave1 = HRLeave::where(function (Builder $query){
        //            $query->where('leave_type_id', '<>', 9)
        //            ->where(function (Builder $query){
        //              $query->where('half_type_id', '<>', 2)
        //              ->orWhereNull('half_type_id');
        //            });
        //          })
        //          ->where(function (Builder $query){
        //            $query->whereIn('leave_status_id', [5,6])
        //            ->orWhereNull('leave_status_id');
        //          })
        //          ->where(function (Builder $query) use ($sd){
        //            $query->whereDate('date_time_start', '<=', $sd)
        //            ->whereDate('date_time_end', '>=', $sd);
        //          });                                             // this will get only full day leave
        $leave1 = HRAttendance::whereDate('attend_date', $sd)->where('daytype_id', 1)->whereNotNull('leave_id');    // this will get all leave including TF and half day leave
        // $leave = $leave1->ddrawsql();
        $leave = $leave1->count();

        $e = 0;
        if ($absent) {
          foreach ($absent1 as $staffidabsent) {
            $branch[$b][$e] = Staff::find($staffidabsent->staff_id)
                  ->belongstomanydepartment()?->wherePivot('main', 1)
                  ->first()->belongstobranch?->location;
            $e++;
          }
        } else {
          $branch[$b] = [];
        }
        if (array_key_exists($b, $branch)) {
          $locabsent1 = array_count_values($branch[$b]);
        } else {
          $locabsent1 = json_decode("{}");
        }

        $eh = 100;
        if($halfabsent) {
          foreach ($halfabsent1 as $staffidhalfabsent) {
            $branchhalfabsent[$b][$eh] = Staff::find($staffidhalfabsent->staff_id)
                  ->belongstomanydepartment()?->wherePivot('main', 1)
                  ->first()->belongstobranch?->location;
            $eh++;
          }
        } else {
          $branchhalfabsent[$b] = [];
        }
        if (array_key_exists($b, $branchhalfabsent)) {
          $lochalfabsent1 = array_count_values($branchhalfabsent[$b]);
        } else {
          $lochalfabsent1 = json_decode("{}");
        }

        $eo = 200;
        if ($outstation) {
          foreach ($outstation1 as $staffidoutstation) {
            $branchoutstaion[$b][$eo] = Staff::find($staffidoutstation->staff_id)
                  ->belongstomanydepartment()?->wherePivot('main', 1)
                  ->first()?->belongstobranch?->location;
            $eo++;
          }
        } else {
          $branchoutstaion[$b] = [];
        }
        if (array_key_exists($b, $branchoutstaion)) {
          $locoutstation1 = array_count_values($branchoutstaion[$b]);
        } else {
          $locoutstation1 = json_decode("{}");
        }

        $leave1 = $leave1->get();
        $ep = 300;
        if ($leave) {
          foreach ($leave1 as $staffidleaveloc) {
            $branchleave[$b][$ep] = Staff::find($staffidleaveloc->staff_id)
                  ->belongstomanydepartment()?->wherePivot('main', 1)
                  ->first()->belongstobranch?->location;
            $ep++;
          }
          // exit;
        } else {
          $branchleave[$b] = [];
        }
        if (array_key_exists($b, $branchleave)) {
          $locleave1 = array_count_values($branchleave[$b]);
        } else {
          $locleave1 = json_decode("{}");
        }
        $overallpercentage = number_format(((($workingpeople + $outstation) - $absent - $leave) / ($workingpeople + $outstation)) * 100, 2);

      } else {

        $workingpeople1 = HRAttendance::whereDate('attend_date', $sd)
                        ->where(function(Builder $query) {
                          $query->where('in', '!=', '00:00:00')
                            ->orwhere('break', '!=', '00:00:00')
                            ->orwhere('resume', '!=', '00:00:00')
                            ->orwhere('out', '!=', '00:00:00');
                        })
                        ->whereNull('outstation_id')
                        ->whereNull('leave_id')
                        ->get();
        $workingpeople = $workingpeople1->count();
        $outstation1 = HRAttendance::whereDate('attend_date', $sd)->whereNotNull('outstation_id')->get();
        $outstation = $outstation1->count();
        $absent = 0;
        $halfabsent = 0;
        $leave = 0;
        $working = OptDayType::find($sq->first()?->daytype_id)?->daytype;
        // $locabsent1 = [];
        // $lochalfabsent1 = [];
        // $locoutstation1 = [];
        // $locleave1 = [];
        $locabsent1 = json_decode("{}");
        $lochalfabsent1 = json_decode("{}");
        $locoutstation1 = json_decode("{}");
        $locleave1 = json_decode("{}");
        // dump($workingpeople);
        $available = $workingpeople + $outstation;

        if ($available == 0) {
          $availableppl = 1;
          $workday = 0;
        } else {
          $availableppl = $available;
          $workday = $available;
        }


        $overallpercentage = number_format((($available - $absent - $leave) / ($availableppl)) * 100, 2);
        // $overallpercentage = 0;
      }

      $chartdata[$b] = [
                'date' => Carbon::parse($sd)->format('j M Y'),
                'overallpercentage' => $overallpercentage,
                'workday' => $workday,
                'workingpeople' => $workingpeople,
                'working' => $working,
                'outstation' => $outstation,
                'leave' => $leave,
                'absent' => $absent,
                'halfabsent' => $halfabsent,
                'locoutstation' => $locoutstation1,
                'locationleave' => $locleave1,
                'locationabsent' => $locabsent1,
                'locationhalfabsent' => $lochalfabsent1,
              ];
      $b++;
    }
    return response()->json($chartdata);
  }

  public function samelocationstaff(Request $request): JsonResponse
  {
    $me = Staff::find($request->id);
    $mede = $me->belongstomanydepartment()->wherePivot('main', 1)->first();
    $branch = $mede->branch_id;
    if ($me->div_id == 1 || $me->div_id == 2 || $me->div_id == 5) {
      $dep = DepartmentPivot::where([['category_id', 2]])->get();
    } elseif ($me->div_id == 4) {
      $dep = DepartmentPivot::where([['branch_id', $branch], ['category_id', 2]])->get();
    } elseif ($me->authorise_id == 1) {
      $dep = DepartmentPivot::all();
    } elseif (is_null($me->div_id) || is_null($me->authorise_id)) {
      $dep = DepartmentPivot::find(0);
    }

    // dd($dep);
    foreach ($dep as $v) {
      $staff = $v->belongstomanystaff()->wherePivot('main', 1)->where('active', 1)->where('name','LIKE','%'.$request->search.'%')->get();
      foreach ($staff as $k) {
        $s['results'][] = ['id' => $k->id, 'text' => $k->name];
      }
    }
    return response()->json($s);
  }

  public function overtimerange(): JsonResponse
  {
    $or = HROvertimeRange::where('active', 1)->orderBy('start', 'ASC')->orderBy('end', 'ASC')->get();
    foreach ($or as $v) {
        $l['results'][] = ['id' => $v->id, 'text' => $v->start.' => '.$v->end];
    }
    return response()->json($l);
  }

  public function outstationattendancelocation(Request $request): JsonResponse
  {
    $st = HROutstation::where(function (Builder $query) use ($request) {
                $query->whereDate('date_from', '<=', $request->date_attend)
                ->whereDate('date_to', '>=', $request->date_attend);
              })
              ->where('active', 1)
              ->groupBy('customer_id')
              ->get();
              // ->ddrawsql();

    // https://select2.org/data-sources/formats
    foreach ($st as $key) {
      $cuti['results'][] = [
                  'id' => $key->id,
                  'text' => Customer::find($key->customer_id)?->customer,
                ];
      // $cuti['pagination'] = ['more' => true];
    }
    return response()->json($cuti);
  }

  public function outstationattendancestaff(Request $request): JsonResponse
  {
    $st = HROutstation::
              // where(function (Builder $query) use ($request) {
              //  $query->whereDate('date_from', '<=', $request->date_attend)
              //  ->whereDate('date_to', '>=', $request->date_attend);
              // })
              where('id', $request->outstation_id)
              ->where('active', 1)
              // ->groupBy('staff_id')
              ->first();
              // ->ddrawsql();
    $cust = HROutstation::where(function (Builder $query) use ($request) {
                  $query->whereDate('date_from', '<=', $request->date_attend)
                  ->whereDate('date_to', '>=', $request->date_attend);
                })
                ->where('customer_id', $st->customer_id)
                ->where('active', 1)
                // ->ddrawsql();
                ->get() ;

    // https://select2.org/data-sources/formats
    foreach ($cust as $key) {
      $cuti['results'][] = [
                  'id' => $key->staff_id,
                  'text' => Staff::find($key->staff_id)->name,
                ];
      // $cuti['pagination'] = ['more' => true];
    }
    return response()->json($cuti);
  }

  public function staffoutstationduration(Request $request): JsonResponse
  {
    $outstation = HROutstation::where('active', 1)->get();
    if ($outstation->count()) {
      foreach ($outstation as $v) {
        $out[] = [
              'title' => ucwords(Str::lower($v->belongstocustomer?->customer??$v->remarks)),
              'start' => $v->date_from,
              'end' => Carbon::parse($v->date_to)->addDay(),
              // 'url' => route('hrleave.show', $v->id),
              'allDay' => true,
              // 'extendedProps' => [
              //            'department' => 'BioChemistry'
              //          ],
              'description' => ((Login::where([['staff_id', $v->staff_id], ['active', 1]])->first()?->username)??'-').' '.Staff::find($v->staff_id)->name,
              'color' => 'green',
              'textColor' => 'yellow',
              'borderColor' => 'green',
          ];
      }
    } else {
      $out[] = [];
    }
    return response()->json( $out );
  }

  public function attendanceabsentindicator(Request $request)
  {
    // dd($request->all());


    $attendance = HRAttendance::groupBy('attend_date')
                ->orderBy('attend_date', 'DESC')
                ->get();

    foreach ($attendance as $k => $v) {
      foreach (HRAttendance::whereDate('attend_date', $v->attend_date)->get() as $k1 => $v1) {
        $in = Carbon::parse($v1->in)->equalTo('00:00:00');
        $break = Carbon::parse($v1->break)->equalTo('00:00:00');
        $resume = Carbon::parse($v1->resume)->equalTo('00:00:00');
        $out = Carbon::parse($v1->out)->equalTo('00:00:00');

        if (
          $v1->daytype_id == 1 && is_null($v1->attendance_type_id) && is_null($v1->attendance_type_id) && is_null($v1->leave_id) && is_null($v1->outstation_id) && $v1->exception == 0
          && (
            ($in && $break && $resume && $out) ||
            ((!$in && $break && $resume && $out) || ($in && !$break && $resume && $out) || ($in && $break && !$resume && $out) || ($in && $break && $resume && !$out)) ||
            ((!$in && !$break && $resume && $out) || ($in && !$break && !$resume && $out) || ($in && $break && !$resume && !$out)) ||
            ((!$in && !$break && !$resume && $out) || ($in && !$break && !$resume && !$out))
          )
        )
        {
          $check[$k][] = true;
        }
        else
        {
          $check[$k][] = false;
        }
      }
      if (in_array(true, $check[$k])) {
        $absent[] = [
              'title' => 'Please Check Absent/Half Absent For This Date',
              'start' => $v->attend_date,
              'end' => $v->attend_date,
              // 'url' => route('hrleave.show', $v->id),
              'allDay' => true,
              // 'extendedProps' => [
              //            'department' => 'BioChemistry'
              //          ],
              'description' => 'Please Check Absent/Half Absent For This Date',
              'color' => 'orange',
              'textColor' => 'white',
              'borderColor' => 'orange',
          ];
      } else {
        $absent[] = [
              'title' => 'Absent/Half Absent Status Verified',
              'start' => $v->attend_date,
              'end' => $v->attend_date,
              // 'url' => route('hrleave.show', $v->id),
              'allDay' => true,
              // 'extendedProps' => [
              //            'department' => 'BioChemistry'
              //          ],
              'description' => 'Absent/Half Absent Status Verified',
              'color' => 'green',
              'textColor' => 'white',
              'borderColor' => 'green',
          ];
      }
    }
    // return response()->json($attendance);
    return response()->json($absent);
  }

  public function getOptSalesGetItem(Request $request)
  {
    $values = OptSalesGetItem::when($request->search, function($q1) use ($request){
                $q1->where('get_item', 'LIKE', '%'.$request->search.'%');
              })
              ->when($request->id, function($q1) use ($request){
                $q1->where('id', $request->id);
              })
              ->when($request->idNotIn, function($q1) use ($request){
                $q1->whereNotIn('id', $request->idNotIn);
              })
              ->get();
    return response()->json($values);
  }







  /* ajax hr */
  // cancel leave
  public function leavecancel(Request $request, HRLeave $hrleave): JsonResponse
  {
    if($request->cancel == 3)
    {
      // all of the debugging echo need to be commented out if using ajax.
      // cari leave type dulu
      $n = HRLeave::find($request->id);
      // echo $n.' staff Leave model<br />';

      // jom cari leave type, jenis yg boleh tolak shj : al, mc, el-al, el-mc, nrl, ml
      // echo $n->leave_type_id.' leave type<br />';

      $dts = \Carbon\Carbon::parse( $n->date_time_start );
      $now = \Carbon\Carbon::now();

      // find the pivot table
      $p1 = $n->belongstomanyleaveannual()->first();
      $p2 = $n->belongstomanyleavemc()->first();
      $p3 = $n->belongstomanyleavematernity()->first();
      $p4 = $n->belongstomanyleavereplacement()->first();

      // leave deduct from AL or EL-AL
      // make sure to cancel at the approver also
      if ( $n->leave_type_id == 1 || $n->leave_type_id == 5 ) {
        // check pivot table
        if (!$p1) {
          return response()->json([
            'status' => 'error',
            'message' => 'Please inform IT Department with this message: "No link between leave and annual leave table (database). This is old leave created from old system."',
          ]);
        }
        // cari al dari staffleave dan tambah balik masuk dalam hasmanyleaveannual

        // cari period cuti
        // echo $n->period_day.' period cuti<br />';

        // cari al dari staff, year yg sama dgn date apply cuti.
        // echo $n->belongstostaff->hasmanyleaveannual()->where('year', $dts->format('Y'))->first()->annual_leave_balance.' applicant annual leave balance<br />';

        $addl = $n->period_day + $n->belongstostaff->hasmanyleaveannual()->where('year', $dts->format('Y'))->first()->annual_leave_balance;
        $addu = $n->belongstostaff->hasmanyleaveannual()->where('year', $dts->format('Y'))->first()->annual_leave_utilize - $n->period_day;
        // echo $addl.' masukkan dalam annual balance<br />';

        // update the al balance
        $n->belongstostaff->hasmanyleaveannual()->where('year', $dts->format('Y'))->update([
          'annual_leave_balance' => $addl,
          'annual_leave_utilize' => $addu,
          // 'remarks' => 'Cancelled By '.\Auth::user()->belongstostaff->name.' reference hr_leaves.id'.$request->id
        ]);
        // update period, status leave of the applicant. status close by HOD/supervisor
        $n->update(['leave_status_id' => 3, 'remarks' => 'Cancelled By '.\Auth::user()->belongstostaff->name]);
        $n->belongstomanyleaveannual()->detach($p1->id);
      }

      if( $n->leave_type_id == 2 ) { // leave deduct from MC
        // check pivot table
        if (!$p2) {
          return response()->json([
            'status' => 'error',
            'message' => 'Please inform IT Department with this message: "No link between leave and MC leave table (database). This is old leave created from old system."',
          ]);
        }
        // sama lebih kurang AL mcm kat atas. so....
        $addl = $n->period_day + $n->belongstostaff->hasmanyleavemc()->where('year', $dts->format('Y'))->first()->mc_leave_balance;
        $addu = $n->belongstostaff->hasmanyleavemc()->where('year', $dts->format('Y'))->first()->mc_leave_utilize - $n->period_day;
        // update the mc balance
        $n->belongstostaff->hasmanyleavemc()->where('year', $dts->format('Y'))->update([
          'mc_leave_balance' => $addl,
          'mc_leave_utilize' => $addu,
          // 'remarks' => 'Cancelled By '.\Auth::user()->belongstostaff->name
        ]);
        // update period, status leave of the applicant. status close by HOD/supervisor
        $n->update(['leave_status_id' => 3, 'remarks' => 'Cancelled By '.\Auth::user()->belongstostaff->name]);
        $n->belongstomanyleavemc()->detach($p2->id);
      }

      if( $n->leave_type_id == 3 || $n->leave_type_id == 6 || $n->leave_type_id == 11  || $n->leave_type_id == 12 ) { // leave deduct from UPL, EL-UPL, MC-UPL & S-UPL
        // echo 'leave deduct from UPL<br />';

        // process a bit different from al and mc
        // we can ignore all the data in hasmanyleaveentitlement mode. just take care all the things in staff leaves only.
        // make period 0 again, regardsless of the ttotal period and then update as al and mc.
        // update period, status leave of the applicant. status close by HOD/supervisor
        $n->update(['leave_status_id' => 3, 'remarks' => 'Cancelled By '.\Auth::user()->belongstostaff->name]);
        // update status for all approval
      }

      if( $n->leave_type_id == 4 || $n->leave_type_id == 10 ) { // leave deduct from NRL & EL-NRL
        if (!$p4) {
          return response()->json([
            'status' => 'error',
            'message' => 'Please inform IT Department with this message: "No link between leave and replacement leave table (database). This is old leave created from old system."',
          ]);
        }
        // echo 'leave deduct from NRL<br />';

        // cari period cuti
        // echo $n->period_day.' period cuti<br />';

        // echo $n->hasmanyleavereplacement()->first().' staffleavereplacement model<br />';
        // hati2 pasai ada 2 kes dgn period, full and half day
        // kena update balik di staffleavereplacement model utk return back period.
        // period campur balik dgn leave utilize (2 table berbeza)
        // echo $n->hasmanyleavereplacement()->first()->leave_utilize.' leave utilize<br />';
        // echo $n->hasmanyleavereplacement()->first()->leave_total.' leave total<br />';

        // untuk update di column leave_balance
        $addr = $n->belongstomanyleavereplacement()->first()->leave_balance + $n->period_day;
        $addru = $n->belongstomanyleavereplacement()->first()->leave_utilize - $n->period_day;
        // echo $addr.' untuk update kat column staff_leave_replacement.leave_utilize<br />';

        // update di table staffleavereplacement. remarks kata sapa reject
        $n->belongstomanyleavereplacement()->first()->update([
          // 'leave_type_id' => NULL,
          'leave_balance' => $addr,
          'leave_utilize' => $addru,
          // 'remarks' => 'Cancelled by '.\Auth::user()->belongstostaff->name,
        ]);
        // update di table staff leave pulokk staffleave
        $n->update(['leave_status_id' => 3, 'remarks' => 'Cancelled By '.\Auth::user()->belongstostaff->name]);
        $n->belongstomanyleavereplacement()->detach($p4->id);
      }

      if( $n->leave_type_id == 7 ) { // leave deduct from ML
        if (!$p3) {
          return response()->json([
            'status' => 'error',
            'message' => 'Please inform IT Department with this message: "No link between leave and maternity leave table (database). This is old leave created from old system."',
          ]);
        }

        // echo 'leave deduct from ML<br />';

        // lebih kurang sama dengan al atau mc, maka..... :) copy paste
        // cari period cuti
        // echo $n->period.' period cuti<br />';

        // cari al dari applicant, year yg sama dgn date apply cuti.
        // echo $n->belongstostaff->hasmanyleavematernity()->where('year', $dts->format('Y'))->first()->maternity_leave_balance.' applicant maternity leave balance<br />';

        $addl = $n->period_day + $n->belongstostaff->hasmanyleavematernity()->where('year', $dts->format('Y'))->first()->maternity_leave_balance;
        $addu = $n->belongstostaff->hasmanyleavematernity()->where('year', $dts->format('Y'))->first()->maternity_leave_utilize - $n->period_day;

        // echo $addl.' masukkan dalam annual balance<br />';

        // find all approval
        // echo $n->hasmanystaffapproval()->get().'find all approval<br />';

        // echo \Auth::user()->belongstostaff->belongtomanyposition()->wherePivot('main', 1)->first()->position.' position <br />';
        // echo \Auth::user()->belongstostaff->name.' position <br />';

        // update the al balance
        $n->belongstostaff->hasmanyleavematernity()->where('year', $dts->format('Y'))->update([
          'maternity_leave_balance' => $addl,
          'maternity_leave_utilize' => $addu,
          // 'remarks' => 'Cancelled By '.\Auth::user()->belongstostaff->name,
        ]);
        // update period, status leave of the applicant. status close by HOD/supervisor
        $n->update(['leave_status_id' => 3, 'remarks' => 'Cancelled By '.\Auth::user()->belongstostaff->name]);
        $n->belongstomanyleavematernity()->detach($p3->id);
      }

      if( $n->leave_type_id == 9 ) { // leave deduct from Time Off
        // echo 'leave deduct from TF<br />';

        // dekat dekat nak sama dgn UPL, maka... :P copy paste

        // process a bit different from al and mc
        // we can ignore all the data in staffannualmcmaternity mode. just take care all the things in staff leaves only.
        // make period 0 again, regardsless of the ttotal period and then update as al and mc.
        // update period, status leave of the applicant. status close by HOD/supervisor
        $n->update(['leave_status_id' => 3, 'remarks' => 'Cancelled By '.\Auth::user()->belongstostaff->name]);
      }
      // finally update at all the approver according to his/her leave flow
      if($n->belongstostaff->belongstoleaveapprovalflow?->backup_approval == 1) {
        $n->hasmanyleaveapprovalbackup()->update([
          'leave_status_id' => 3,
          'remarks' => 'Cancelled By '.\Auth::user()->belongstostaff->name
        ]);
      }
      if($n->belongstostaff->belongstoleaveapprovalflow?->supervisor_approval == 1) {
        $n->hasoneleaveapprovalsupervisor()->update([
          'leave_status_id' => 3,
          'remarks' => 'Cancelled By '.\Auth::user()->belongstostaff->name
        ]);
      }
      if($n->belongstostaff->belongstoleaveapprovalflow?->hod_approval == 1) {
        $n->hasoneleaveapprovalhod()->update([
          'leave_status_id' => 3,
          'remarks' => 'Cancelled By '.\Auth::user()->belongstostaff->name
        ]);
      }
      if($n->belongstostaff->belongstoleaveapprovalflow?->director_approval == 1) {
        $n->hasoneleaveapprovaldir()->update([
          'leave_status_id' => 3,
          'remarks' => 'Cancelled By '.\Auth::user()->belongstostaff->name
        ]);
      }
      if($n->belongstostaff->belongstoleaveapprovalflow?->hr_approval == 1) {
        $n->hasoneleaveapprovalhr()->update([
          'leave_status_id' => 3,
          'remarks' => 'Cancelled By '.\Auth::user()->belongstostaff->name
        ]);
      }
      // remove leave_id from attendance
      $z = HRAttendance::where('leave_id', $request->id)->get();
      foreach ($z as $s) {
        HRAttendance::where('id', $s->id)->update(['leave_id' => null]);
      }

      // done processing the data
      return response()->json([
        'status' => 'success',
        'message' => 'Your leave has been cancelled.',
      ]);
    }
  }

  public function leaverapprove(HRLeaveApprovalBackup $hrleaveapprovalbackup)
  {
    $hrleaveapprovalbackup->update(['leave_status_id' => 5]);
      return response()->json([
        'status' => 'success',
        'message' => 'Your colleague leave has been approved... and he/she says thank you.',
      ]);
  }

  public function leavesapprove(Request $request, HRLeaveApprovalSupervisor $hrleaveapprovalsupervisor)
  {
    $hrleaveapprovalsupervisor->update(['leave_status_id' => $request->id]);
      return response()->json([
        'status' => 'success',
        'message' => 'Leave approved.',
      ]);
  }

  public function supervisorstatus(Request $request)
  {
    // return $request->all();
    // exit;
    $validated = $request->validate([
        'leave_status_id' => 'required',
        'verify_code' => 'required_if:leave_status_id,5|numeric|nullable',    // required if only leave_status_id is 5 (Approved)
        'remarks' => 'required_if:leave_status_id,4|nullable',
      ],
      [
        'leave_status_id.required' => 'Please choose your approval',
        'verify_code.required_if' => 'Please insert :attribute to approve leave, otherwise it wont be necessary for leave application reject',
        'remarks' => 'Please insert :attribute to reject leave, otherwise it wont be necessary for leave application approve',
      ],
      [
        'leave_status_id' => 'Approval Status',
        'verify_code' => 'Verification Code',
        'remarks' => 'Remarks',
      ]
    );

    // get verify code
    $sa = HRLeaveApprovalSupervisor::find($request->id);
    $sal = $sa->belongstostaffleave;                    // this supervisor approval belongs to leave
    $sauser = $sal->belongstostaff;                     // leave belongs to user, not authuser anymore
    // dd($sauser);
    $vc = $sal->verify_code;
    // dd($sal);

    // find the pivot table
    $p1 = $sal->belongstomanyleaveannual()->first();
    $p2 = $sal->belongstomanyleavemc()->first();
    $p3 = $sal->belongstomanyleavematernity()->first();
    $p4 = $sal->belongstomanyleavereplacement()->first();

    if( $request->leave_status_id == 5 ) {                  // leave approve
      if($vc == $request->verify_code) {
        $sa->update([
          'staff_id' => \Auth::user()->belongstostaff->id,
          'leave_status_id' => $request->leave_status_id,
          'remarks' => ucwords(Str::lower($request->remarks)),
        ]);
      } else {
        Session::flash('message', 'Verification Code was incorrect');
        return redirect()->back()->withInput();
      }
    } elseif($request->leave_status_id == 4) {                // leave rejected
      $saly = $sal->leave_type_id;                    // need to find out leave type
      if ($saly == 1 || $saly == 5) {                   // annual leave: put period leave to annual leave entitlement
        if (!$p1) {
          Session::flash('danger', 'Please inform IT Department with this message: "No link between leave and annual leave table (database). This is old leave created from old system."');
          return redirect()->back()->withInput();
        }
        $pd = $sal->period_day;                     // get period day
        $sala = $sal->belongstomanyleaveannual->first();        // get annual leave
        $albal = $sala->annual_leave_balance + $pd;           // annual leave balance
        $aluti = $sala->annual_leave_utilize - $pd;           // annual leave utilize
        $sala->update(['annual_leave_balance' => $albal, 'annual_leave_utilize' => $aluti]);
        $sal->update(['leave_status_id' => $request->leave_status_id]);
        // $sal->belongstomanyleaveannual()->detach($p1->id);
      } elseif($saly == 4 || $saly == 10) {               // replacement leave
        if (!$p4) {
          Session::flash('danger', 'Please inform IT Department with this message: "No link between leave and replacement leave table (database). This is old leave created from old system."');
          return redirect()->back()->withInput();
        }
        $pd = $sal->period_day;                     // get period day
        $sala = $sal->belongstomanyleavereplacement->first();     // get replacement leave
        $albal = $sala->leave_balance + $pd;              // replacement leave balance
        $aluti = $sala->leave_utilize - $pd;              // replacement leave utilize
        $sala->update(['leave_balance' => $albal, 'leave_utilize' => $aluti]);
        $sal->update(['leave_status_id' => $request->leave_status_id]);
        // $sal->belongstomanyleavereplacement()->detach($p4->id);
      } elseif($saly == 2) {                        // mc leave
        if (!$p2) {
          Session::flash('danger', 'Please inform IT Department with this message: "No link between leave and MC leave table (database). This is old leave created from old system."');
          return redirect()->back()->withInput();
        }
        $pd = $sal->period_day;                     // get period day
        $sala = $sal->belongstomanyleavemc->first();          // get mc leave
        $albal = $sala->mc_leave_balance + $pd;             // mc leave balance
        $aluti = $sala->mc_leave_utilize - $pd;             // mc leave utilize
        $sala->update(['mc_leave_balance' => $albal, 'mc_leave_utilize' => $aluti]);
        $sal->update(['leave_status_id' => $request->leave_status_id]);
        // $sal->belongstomanyleavemc()->detach($p2->id);
      } elseif($saly == 7) {
        if (!$p3) {
          Session::flash('danger', 'Please inform IT Department with this message: "No link between leave and maternity leave table (database). This is old leave created from old system."');
          return redirect()->back()->withInput();
        }
        $pd = $sal->period_day;                     // get period day
        $sala = $sal->belongstomanyleavematernity->first();       // get maternity leave
        $albal = $sala->maternity_leave_balance + $pd;          // maternity leave balance
        $aluti = $sala->maternity_leave_utilize - $pd;          // maternity leave utilize
        $sala->update(['maternity_leave_balance' => $albal, 'maternity_leave_utilize' => $aluti]);
        $sal->update(['leave_status_id' => $request->leave_status_id]);
        // $sal->belongstomanyleavematernity()->detach($p3->id);
      } elseif($saly == 3 || $saly == 6 || $saly == 11 || $saly == 12) {
        $sal->update(['leave_status_id' => $request->leave_status_id]);
      } elseif($saly == 9) {
        $sal->update(['leave_status_id' => $request->leave_status_id]);
      }

      if($sauser->belongstoleaveapprovalflow->backup_approval == 1){                                // update on backup
        $sal->hasmanyleaveapprovalbackup()->update([/*'staff_id' => \Auth::user()->belongstostaff->id,*/ 'leave_status_id' => $request->leave_status_id, 'remarks' => 'Rejected by Supervisor ('.\Auth::user()->belongstostaff->name.') on '.\Carbon\Carbon::now()->format('j M Y g:i a').' | '.ucwords(Str::lower($request->remarks))]);
      }
      if($sauser->belongstoleaveapprovalflow->supervisor_approval == 1){                              // update on supervisor
        $sal->hasmanyleaveapprovalsupervisor()->update(['staff_id' => \Auth::user()->belongstostaff->id, 'leave_status_id' => $request->leave_status_id, 'remarks' => 'Rejected by Supervisor ('.\Auth::user()->belongstostaff->name.') on '.\Carbon\Carbon::now()->format('j M Y g:i a').' | '.ucwords(Str::lower($request->remarks))]);
      }
      if($sauser->belongstoleaveapprovalflow->hod_approval == 1){                                 // update on hod
        $sal->hasmanyleaveapprovalhod()->update([/*'staff_id' => \Auth::user()->belongstostaff->id,*/ 'leave_status_id' => $request->leave_status_id, 'remarks' => 'Rejected by Supervisor ('.\Auth::user()->belongstostaff->name.') on '.\Carbon\Carbon::now()->format('j M Y g:i a').' | '.ucwords(Str::lower($request->remarks))]);
      }
      if($sauser->belongstoleaveapprovalflow->director_approval == 1){                              // update on director
        $sal->hasmanyleaveapprovaldir()->update([/*'staff_id' => \Auth::user()->belongstostaff->id,*/ 'leave_status_id' => $request->leave_status_id, 'remarks' => 'Rejected by Supervisor ('.\Auth::user()->belongstostaff->name.') on '.\Carbon\Carbon::now()->format('j M Y g:i a').' | '.ucwords(Str::lower($request->remarks))]);
      }
      if($sauser->belongstoleaveapprovalflow->hr_approval == 1){                                  // update on hr
        $sal->hasmanyleaveapprovalhr()->update([/*'staff_id' => \Auth::user()->belongstostaff->id,*/ 'leave_status_id' => $request->leave_status_id, 'remarks' => 'Rejected by Supervisor ('.\Auth::user()->belongstostaff->name.') on '.\Carbon\Carbon::now()->format('j M Y g:i a').' | '.ucwords(Str::lower($request->remarks))]);
      }
      // remove leave_id from attendance
      $z = HRAttendance::where('leave_id', $sal->id)->get();
      foreach ($z as $s) {
        HRAttendance::where('id', $s->id)->update(['leave_id' => null]);
      }
    }
    // Session::flash('message', 'Successfully make an approval.');
    // return redirect()->back();
    return response()->json([
        'status' => 'success',
        'message' => 'Successfully make an approval.',
      ]);
  }

  public function hodstatus(Request $request)
  {
    // return $request->all();
    // exit;
    $validated = $request->validate([
        'leave_status_id' => 'required',
        'verify_code' => 'required_if:leave_status_id,5|numeric|nullable',    // required if only leave_status_id is 5 (Approved)
        'remarks' => 'required_if:leave_status_id,4|nullable',
      ],
      [
        'leave_status_id.required' => 'Please choose your approval',
        'verify_code.required_if' => 'Please insert :attribute to approve leave, otherwise it wont be necessary for leave application reject',
        'remarks' => 'Please insert :attribute to reject leave, otherwise it wont be necessary for leave application approve',
      ],
      [
        'leave_status_id' => 'Approval Status',
        'verify_code' => 'Verification Code',
        'remarks' => 'Remarks',
      ]
    );

    // get verify code
    $sa = HRLeaveApprovalHOD::find($request->id);
    $sal = $sa->belongstostaffleave;                    // this hod approval belongs to leave
    $sauser = $sal->belongstostaff;                     // leave belongs to user, not authuser anymore
    // dd($sauser);
    $vc = $sal->verify_code;
    // dd($sal);

    // find the pivot table
    $p1 = $sal->belongstomanyleaveannual()->first();
    $p2 = $sal->belongstomanyleavemc()->first();
    $p3 = $sal->belongstomanyleavematernity()->first();
    $p4 = $sal->belongstomanyleavereplacement()->first();

    if( $request->leave_status_id == 5 ) {                  // leave approve
      if($vc == $request->verify_code) {
        $sa->update([
          'staff_id' => \Auth::user()->belongstostaff->id,
          'leave_status_id' => $request->leave_status_id,
          'remarks' => ucwords(Str::lower($request->remarks)),
        ]);
      } else {
        Session::flash('message', 'Verification Code was incorrect');
        return redirect()->back()->withInput();
      }
    } elseif($request->leave_status_id == 4) {                // leave rejected
      $saly = $sal->leave_type_id;                    // need to find out leave type
      if ($saly == 1 || $saly == 5) {                   // annual leave: put period leave to annual leave entitlement
        if (!$p1) {
          Session::flash('danger', 'Please inform IT Department with this message: "No link between leave and annual leave table (database). This is old leave created from old system."');
          return redirect()->back()->withInput();
        }
        $pd = $sal->period_day;                     // get period day
        $sala = $sal->belongstomanyleaveannual->first();        // get annual leave
        $albal = $sala->annual_leave_balance + $pd;           // annual leave balance
        $aluti = $sala->annual_leave_utilize - $pd;           // annual leave utilize
        $sala->update(['annual_leave_balance' => $albal, 'annual_leave_utilize' => $aluti]);
        $sal->update(['leave_status_id' => $request->leave_status_id]);
        // $sal->belongstomanyleaveannual()->detach($p1->id);
      } elseif($saly == 4 || $saly == 10) {               // replacement leave
        if (!$p4) {
          Session::flash('danger', 'Please inform IT Department with this message: "No link between leave and replacement leave table (database). This is old leave created from old system."');
          return redirect()->back()->withInput();
        }
        $pd = $sal->period_day;                     // get period day
        $sala = $sal->belongstomanyleavereplacement->first();     // get replacement leave
        $albal = $sala->leave_balance + $pd;              // replacement leave balance
        $aluti = $sala->leave_utilize - $pd;              // replacement leave utilize
        $sala->update(['leave_balance' => $albal, 'leave_utilize' => $aluti]);
        $sal->update(['leave_status_id' => $request->leave_status_id]);
        // $sal->belongstomanyleavereplacement()->detach($p4->id);
      } elseif($saly == 2) {                        // mc leave
        if (!$p2) {
          Session::flash('danger', 'Please inform IT Department with this message: "No link between leave and MC leave table (database). This is old leave created from old system."');
          return redirect()->back()->withInput();
        }
        $pd = $sal->period_day;                     // get period day
        $sala = $sal->belongstomanyleavemc->first();          // get mc leave
        $albal = $sala->mc_leave_balance + $pd;             // mc leave balance
        $aluti = $sala->mc_leave_utilize - $pd;             // mc leave utilize
        $sala->update(['mc_leave_balance' => $albal, 'mc_leave_utilize' => $aluti]);
        $sal->update(['leave_status_id' => $request->leave_status_id]);
        // $sal->belongstomanyleavemc()->detach($p2->id);
      } elseif($saly == 7) {
        if (!$p3) {
          Session::flash('danger', 'Please inform IT Department with this message: "No link between leave and maternity leave table (database). This is old leave created from old system."');
          return redirect()->back()->withInput();
        }
        $pd = $sal->period_day;                     // get period day
        $sala = $sal->belongstomanyleavematernity->first();       // get maternity leave
        $albal = $sala->maternity_leave_balance + $pd;          // maternity leave balance
        $aluti = $sala->maternity_leave_utilize - $pd;          // maternity leave utilize
        $sala->update(['maternity_leave_balance' => $albal, 'maternity_leave_utilize' => $aluti]);
        $sal->update(['leave_status_id' => $request->leave_status_id]);
        // $sal->belongstomanyleavematernity()->detach($p3->id);
      } elseif($saly == 3 || $saly == 6 || $saly == 11 || $saly == 12) {
        $sal->update(['period_day' => 0, 'leave_status_id' => $request->leave_status_id]);
      } elseif($saly == 9) {
        $sal->update(['period_time' => 0, 'leave_status_id' => $request->leave_status_id]);
      }

      if($sauser->belongstoleaveapprovalflow->backup_approval == 1){                                // update on backup
        $sal->hasmanyleaveapprovalbackup()->update([/*'staff_id' => \Auth::user()->belongstostaff->id,*/ 'leave_status_id' => $request->leave_status_id, 'remarks' => 'Rejected by HOD ('.\Auth::user()->belongstostaff->name.') on '.\Carbon\Carbon::now()->format('j M Y g:i a').' | '.ucwords(Str::lower($request->remarks))]);
      }
      if($sauser->belongstoleaveapprovalflow->supervisor_approval == 1){                              // update on supervisor
        $sal->hasmanyleaveapprovalsupervisor()->update([/*'staff_id' => \Auth::user()->belongstostaff->id,*/ 'leave_status_id' => $request->leave_status_id, 'remarks' => 'Rejected by HOD ('.\Auth::user()->belongstostaff->name.') on '.\Carbon\Carbon::now()->format('j M Y g:i a').' | '.ucwords(Str::lower($request->remarks))]);
      }
      if($sauser->belongstoleaveapprovalflow->hod_approval == 1){                                 // update on hod
        $sal->hasmanyleaveapprovalhod()->update(['staff_id' => \Auth::user()->belongstostaff->id, 'leave_status_id' => $request->leave_status_id, 'remarks' => 'Rejected by HOD ('.\Auth::user()->belongstostaff->name.') on '.\Carbon\Carbon::now()->format('j M Y g:i a').' | '.ucwords(Str::lower($request->remarks))]);
      }
      if($sauser->belongstoleaveapprovalflow->director_approval == 1){                              // update on director
        $sal->hasmanyleaveapprovaldir()->update([/*'staff_id' => \Auth::user()->belongstostaff->id,*/ 'leave_status_id' => $request->leave_status_id, 'remarks' => 'Rejected by HOD ('.\Auth::user()->belongstostaff->name.') on '.\Carbon\Carbon::now()->format('j M Y g:i a').' | '.ucwords(Str::lower($request->remarks))]);
      }
      if($sauser->belongstoleaveapprovalflow->hr_approval == 1){                                  // update on hr
        $sal->hasmanyleaveapprovalhr()->update([/*'staff_id' => \Auth::user()->belongstostaff->id,*/ 'leave_status_id' => $request->leave_status_id, 'remarks' => 'Rejected by HOD ('.\Auth::user()->belongstostaff->name.') on '.\Carbon\Carbon::now()->format('j M Y g:i a').' | '.ucwords(Str::lower($request->remarks))]);
      }
      // remove leave_id from attendance
      $z = HRAttendance::where('leave_id', $sal->id)->get();
      foreach ($z as $s) {
        HRAttendance::where('id', $s->id)->update(['leave_id' => null]);
      }
    }
    // Session::flash('message', 'Successfully make an approval.');
    // return redirect()->back();
    return response()->json([
        'status' => 'success',
        'message' => 'Successfully make an approval.',
      ]);
  }

  public function dirstatus(Request $request)
  {
    // return $request->all();
    // exit;
    $validated = $request->validate([
        'leave_status_id' => 'required',
        'verify_code' => 'required_if:leave_status_id,5|numeric|nullable',    // required if only leave_status_id is 5 (Approved)
        'remarks' => 'required_if:leave_status_id,4|nullable',
      ],
      [
        'leave_status_id.required' => 'Please choose your approval',
        'verify_code.required_if' => 'Please insert :attribute to approve leave, otherwise it wont be necessary for leave application reject',
        'remarks' => 'Please insert :attribute to reject leave, otherwise it wont be necessary for leave application approve',
      ],
      [
        'leave_status_id' => 'Approval Status',
        'verify_code' => 'Verification Code',
        'remarks' => 'Remarks',
      ]
    );

    // get verify code
    $sa = HRLeaveApprovalDirector::find($request->id);
    $sal = $sa->belongstostaffleave;                          // this hod approval belongs to leave
    $sauser = $sal->belongstostaff;                       // leave belongs to user, not authuser anymore
    // dd($sauser);

    // find the pivot table
    $p1 = $sal->belongstomanyleaveannual()->first();
    $p2 = $sal->belongstomanyleavemc()->first();
    $p3 = $sal->belongstomanyleavematernity()->first();
    $p4 = $sal->belongstomanyleavereplacement()->first();

    $vc = $sal->verify_code;
    // dd($sal);
    if( $request->leave_status_id == 5 ) {                  // leave approve
      if($vc == $request->verify_code) {
        $sa->update([
          'staff_id' => \Auth::user()->belongstostaff->id,
          'leave_status_id' => $request->leave_status_id,
          'remarks' => ucwords(Str::lower($request->remarks)),
        ]);
      } else {
        Session::flash('message', 'Verification Code was incorrect');
        return redirect()->back()->withInput();
      }
    } elseif($request->leave_status_id == 4) {                // leave rejected
      $saly = $sal->leave_type_id;                    // need to find out leave type
      if ($saly == 1 || $saly == 5) {                   // annual leave: put period leave to annual leave entitlement
        if (!$p1) {
          Session::flash('danger', 'Please inform IT Department with this message: "No link between leave and annual leave table (database). This is old leave created from old system."');
          return redirect()->back()->withInput();
        }
        $pd = $sal->period_day;                     // get period day
        $sala = $sal->belongstomanyleaveannual->first();        // get annual leave
        $albal = $sala->annual_leave_balance + $pd;           // annual leave balance
        $aluti = $sala->annual_leave_utilize - $pd;           // annual leave utilize
        $sala->update(['annual_leave_balance' => $albal, 'annual_leave_utilize' => $aluti]);
        $sal->update(['leave_status_id' => $request->leave_status_id]);
        // $sal->belongstomanyleaveannual()->detach($p1->id);
      } elseif($saly == 4 || $saly == 10) {               // replacement leave
        if (!$p4) {
          Session::flash('danger', 'Please inform IT Department with this message: "No link between leave and replacement leave table (database). This is old leave created from old system."');
          return redirect()->back()->withInput();
        }
        $pd = $sal->period_day;                     // get period day
        $sala = $sal->belongstomanyleavereplacement->first();     // get replacement leave
        $albal = $sala->leave_balance + $pd;              // replacement leave balance
        $aluti = $sala->leave_utilize - $pd;              // replacement leave utilize
        $sala->update(['leave_balance' => $albal, 'leave_utilize' => $aluti]);
        $sal->update(['leave_status_id' => $request->leave_status_id]);
        // $sal->belongstomanyleavereplacement()->detach($p4->id);
      } elseif($saly == 2) {                        // mc leave
        if (!$p2) {
          Session::flash('danger', 'Please inform IT Department with this message: "No link between leave and MC leave table (database). This is old leave created from old system."');
          return redirect()->back()->withInput();
        }
        $pd = $sal->period_day;                     // get period day
        $sala = $sal->belongstomanyleavemc->first();          // get mc leave
        $albal = $sala->mc_leave_balance + $pd;             // mc leave balance
        $aluti = $sala->mc_leave_utilize - $pd;             // mc leave utilize
        $sala->update(['mc_leave_balance' => $albal, 'mc_leave_utilize' => $aluti]);
        $sal->update(['leave_status_id' => $request->leave_status_id]);
        // $sal->belongstomanyleavemc()->detach($p2->id);
      } elseif($saly == 7) {
        if (!$p3) {
          Session::flash('danger', 'Please inform IT Department with this message: "No link between leave and maternity leave table (database). This is old leave created from old system."');
          return redirect()->back()->withInput();
        }
        $pd = $sal->period_day;                     // get period day
        $sala = $sal->belongstomanyleavematernity->first();       // get maternity leave
        $albal = $sala->maternity_leave_balance + $pd;          // maternity leave balance
        $aluti = $sala->maternity_leave_utilize - $pd;          // maternity leave utilize
        $sala->update(['maternity_leave_balance' => $albal, 'maternity_leave_utilize' => $aluti]);
        $sal->update(['leave_status_id' => $request->leave_status_id]);
        // $sal->belongstomanyleavematernity()->detach($p3->id);
      } elseif($saly == 3 || $saly == 6 || $saly == 11 || $saly == 12) {
        $sal->update(['period_day' => 0, 'leave_status_id' => $request->leave_status_id]);
      } elseif($saly == 9) {
        $sal->update(['period_time' => 0, 'leave_status_id' => $request->leave_status_id]);
      }

      if($sauser->belongstoleaveapprovalflow->backup_approval == 1){                                // update on backup
        $sal->hasmanyleaveapprovalbackup()->update([/*'staff_id' => \Auth::user()->belongstostaff->id,*/ 'leave_status_id' => $request->leave_status_id, 'remarks' => 'Rejected by Director ('.\Auth::user()->belongstostaff->name.') on '.\Carbon\Carbon::now()->format('j M Y g:i a').' | '.ucwords(Str::lower($request->remarks))]);
      }
      if($sauser->belongstoleaveapprovalflow->supervisor_approval == 1){                              // update on supervisor
        $sal->hasmanyleaveapprovalsupervisor()->update([/*'staff_id' => \Auth::user()->belongstostaff->id,*/ 'leave_status_id' => $request->leave_status_id, 'remarks' => 'Rejected by Director ('.\Auth::user()->belongstostaff->name.') on '.\Carbon\Carbon::now()->format('j M Y g:i a').' | '.ucwords(Str::lower($request->remarks))]);
      }
      if($sauser->belongstoleaveapprovalflow->hod_approval == 1){                                 // update on hod
        $sal->hasmanyleaveapprovalhod()->update([/*'staff_id' => \Auth::user()->belongstostaff->id,*/ 'leave_status_id' => $request->leave_status_id, 'remarks' => 'Rejected by Director ('.\Auth::user()->belongstostaff->name.') on '.\Carbon\Carbon::now()->format('j M Y g:i a').' | '.ucwords(Str::lower($request->remarks))]);
      }
      if($sauser->belongstoleaveapprovalflow->director_approval == 1){                              // update on director
        $sal->hasmanyleaveapprovaldir()->update(['staff_id' => \Auth::user()->belongstostaff->id, 'leave_status_id' => $request->leave_status_id, 'remarks' => 'Rejected by Director ('.\Auth::user()->belongstostaff->name.') on '.\Carbon\Carbon::now()->format('j M Y g:i a').' | '.ucwords(Str::lower($request->remarks))]);
      }
      if($sauser->belongstoleaveapprovalflow->hr_approval == 1){                                  // update on hr
        $sal->hasmanyleaveapprovalhr()->update([/*'staff_id' => \Auth::user()->belongstostaff->id,*/ 'leave_status_id' => $request->leave_status_id, 'remarks' => 'Rejected by Director ('.\Auth::user()->belongstostaff->name.') on '.\Carbon\Carbon::now()->format('j M Y g:i a')]);
      }
      // remove leave_id from attendance
      $z = HRAttendance::where('leave_id', $sal->id)->get();
      foreach ($z as $s) {
        HRAttendance::where('id', $s->id)->update(['leave_id' => null]);
      }
    } elseif($request->leave_status_id == 6) {                // leave waived, so need to put back all leave period.
      $saly = $sal->leave_type_id;                    // need to find out leave type
      if ($saly == 1 || $saly == 5) {                   // annual leave: put period leave to annual leave entitlement
        $pd = $sal->period_day;                     // get period day
        $sala = $sal->belongstomanyleaveannual->first();        // get annual leave
        $albal = $sala->annual_leave_balance + $pd;           // annual leave balance
        $aluti = $sala->annual_leave_utilize - $pd;           // annual leave utilize
        $sala->update(['annual_leave_balance' => $albal, 'annual_leave_utilize' => $aluti]);
        $sal->update(['leave_status_id' => $request->leave_status_id]);
      } elseif($saly == 4 || $saly == 10) {               // replacement leave
        $pd = $sal->period_day;                     // get period day
        $sala = $sal->belongstomanyleavereplacement->first();     // get replacement leave
        $albal = $sala->leave_balance + $pd;              // replacement leave balance
        $aluti = $sala->leave_utilize - $pd;              // replacement leave utilize
        $sala->update(['leave_balance' => $albal, 'leave_utilize' => $aluti]);
        $sal->update(['leave_status_id' => $request->leave_status_id]);
      } elseif($saly == 2) {                        // mc leave
        $pd = $sal->period_day;                     // get period day
        $sala = $sal->belongstomanyleavemc->first();          // get mc leave
        $albal = $sala->mc_leave_balance + $pd;             // mc leave balance
        $aluti = $sala->mc_leave_utilize - $pd;             // mc leave utilize
        $sala->update(['mc_leave_balance' => $albal, 'mc_leave_utilize' => $aluti]);
        $sal->update(['leave_status_id' => $request->leave_status_id]);
      } elseif($saly == 7) {
        $pd = $sal->period_day;                     // get period day
        $sala = $sal->belongstomanyleavematernity->first();       // get maternity leave
        $albal = $sala->maternity_leave_balance + $pd;          // maternity leave balance
        $aluti = $sala->maternity_leave_utilize - $pd;          // maternity leave utilize
        $sala->update(['maternity_leave_balance' => $albal, 'maternity_leave_utilize' => $aluti]);
        $sal->update(['leave_status_id' => $request->leave_status_id]);
      } elseif($saly == 3 || $saly == 6 || $saly == 11 || $saly == 12) {
        $sal->update(['leave_status_id' => $request->leave_status_id]);
      } elseif($saly == 9) {
        $sal->update(['leave_status_id' => $request->leave_status_id]);
      }

      if($sauser->belongstoleaveapprovalflow->backup_approval == 1){                                // update on backup
        $sal->hasmanyleaveapprovalbackup()->update([/*'staff_id' => \Auth::user()->belongstostaff->id,*/ 'leave_status_id' => $request->leave_status_id, 'remarks' => ucwords(Str::lower($request->remarks))]);
      }
      if($sauser->belongstoleaveapprovalflow->supervisor_approval == 1){                              // update on supervisor
        $sal->hasmanyleaveapprovalsupervisor()->update([/*'staff_id' => \Auth::user()->belongstostaff->id, */'leave_status_id' => $request->leave_status_id, 'remarks' => ucwords(Str::lower($request->remarks))]);
      }
      if($sauser->belongstoleaveapprovalflow->hod_approval == 1){                                 // update on hod
        $sal->hasmanyleaveapprovalhod()->update([/*'staff_id' => \Auth::user()->belongstostaff->id,*/ 'leave_status_id' => $request->leave_status_id, 'remarks' => ucwords(Str::lower($request->remarks))]);
      }
      if($sauser->belongstoleaveapprovalflow->director_approval == 1){                              // update on director
        $sal->hasmanyleaveapprovaldir()->update(['staff_id' => \Auth::user()->belongstostaff->id, 'leave_status_id' => $request->leave_status_id, 'remarks' => ucwords(Str::lower($request->remarks))]);
      }
      if($sauser->belongstoleaveapprovalflow->hr_approval == 1){                                  // update on hr
        $sal->hasmanyleaveapprovalhr()->update([/*'staff_id' => \Auth::user()->belongstostaff->id,*/ 'leave_status_id' => $request->leave_status_id, 'remarks' => ucwords(Str::lower($request->remarks))]);
      }
    }
    // Session::flash('message', 'Successfully make an approval.');
    // return redirect()->back();
    return response()->json([
        'status' => 'success',
        'message' => 'Successfully make an approval.',
      ]);
  }

  public function hrstatus(Request $request)
  {
    // return $request->all();
    // exit;
    $validated = $request->validate([
        'leave_status_id' => 'required',
        'verify_code' => 'required_if:leave_status_id,5|numeric|nullable',    // required if only leave_status_id is 5 (Approved)
        'remarks' => 'required_if:leave_status_id,4|nullable',
      ],
      [
        'leave_status_id.required' => 'Please choose your approval',
        'verify_code.required_if' => 'Please insert :attribute to approve leave, otherwise it wont be necessary for leave application reject',
        'remarks' => 'Please insert :attribute to reject leave, otherwise it wont be necessary for leave application approve',
      ],
      [
        'leave_status_id' => 'Approval Status',
        'verify_code' => 'Verification Code',
        'remarks' => 'Remarks',
      ]
    );

    // get verify code
    $sa = HRLeaveApprovalHR::find($request->id);
    $sal = $sa->belongstostaffleave;                      // this hr approval belongs to leave
    $sauser = $sal->belongstostaff;                       // leave belongs to user, not authuser anymore
    // dd($sauser);

    // find the pivot table
    $p1 = $sal->belongstomanyleaveannual()->first();
    $p2 = $sal->belongstomanyleavemc()->first();
    $p3 = $sal->belongstomanyleavematernity()->first();
    $p4 = $sal->belongstomanyleavereplacement()->first();

    $vc = $sal->verify_code;
    // dd($sal);
    if( $request->leave_status_id == 5 ) {                  // leave approve
      if($vc == $request->verify_code) {
        $sa->update([
          'staff_id' => \Auth::user()->belongstostaff->id,
          'leave_status_id' => $request->leave_status_id,
          'remarks' => ucwords(Str::lower($request->remarks)),
        ]);
        $sal->update(['leave_status_id' => $request->leave_status_id]);
      } else {
        Session::flash('message', 'Verification Code was incorrect');
        return redirect()->back()->withInput();
      }
    } elseif($request->leave_status_id == 4) {                // leave rejected
      $saly = $sal->leave_type_id;                    // need to find out leave type
      if ($saly == 1 || $saly == 5) {                   // annual leave: put period leave to annual leave entitlement
        if (!$p1) {
          Session::flash('danger', 'Please inform IT Department with this message: "No link between leave and annual leave table (database). This is old leave created from old system."');
          return redirect()->back()->withInput();
        }
        $pd = $sal->period_day;                     // get period day
        $sala = $sal->belongstomanyleaveannual->first();        // get annual leave
        $albal = $sala->annual_leave_balance + $pd;           // annual leave balance
        $aluti = $sala->annual_leave_utilize - $pd;           // annual leave utilize
        $sala->update(['annual_leave_balance' => $albal, 'annual_leave_utilize' => $aluti]);
        $sal->update(['leave_status_id' => $request->leave_status_id]);
        // $sal->belongstomanyleaveannual()->detach($p1->id);
      } elseif($saly == 4 || $saly == 10) {               // replacement leave
        if (!$p4) {
          Session::flash('danger', 'Please inform IT Department with this message: "No link between leave and replacement leave table (database). This is old leave created from old system."');
          return redirect()->back()->withInput();
        }
        $pd = $sal->period_day;                     // get period day
        $sala = $sal->belongstomanyleavereplacement->first();     // get replacement leave
        $albal = $sala->leave_balance + $pd;              // replacement leave balance
        $aluti = $sala->leave_utilize - $pd;              // replacement leave utilize
        $sala->update(['leave_balance' => $albal, 'leave_utilize' => $aluti]);
        $sal->update(['leave_status_id' => $request->leave_status_id]);
        // $sal->belongstomanyleavereplacement()->detach($p4->id);
      } elseif($saly == 2) {                        // mc leave
        if (!$p2) {
          Session::flash('danger', 'Please inform IT Department with this message: "No link between leave and MC leave table (database). This is old leave created from old system."');
          return redirect()->back()->withInput();
        }
        $pd = $sal->period_day;                     // get period day
        $sala = $sal->belongstomanyleavemc->first();          // get mc leave
        $albal = $sala->mc_leave_balance + $pd;             // mc leave balance
        $aluti = $sala->mc_leave_utilize - $pd;             // mc leave utilize
        $sala->update(['mc_leave_balance' => $albal, 'mc_leave_utilize' => $aluti]);
        $sal->update(['leave_status_id' => $request->leave_status_id]);
        // $sal->belongstomanyleavemc()->detach($p2->id);
      } elseif($saly == 7) {
        if (!$p3) {
          Session::flash('danger', 'Please inform IT Department with this message: "No link between leave and maternity leave table (database). This is old leave created from old system."');
          return redirect()->back()->withInput();
        }
        $pd = $sal->period_day;                     // get period day
        $sala = $sal->belongstomanyleavematernity->first();       // get maternity leave
        $albal = $sala->maternity_leave_balance + $pd;          // maternity leave balance
        $aluti = $sala->maternity_leave_utilize - $pd;          // maternity leave utilize
        $sala->update(['maternity_leave_balance' => $albal, 'maternity_leave_utilize' => $aluti]);
        $sal->update(['leave_status_id' => $request->leave_status_id]);
        // $sal->belongstomanyleavematernity()->detach($p3->id);
      } elseif($saly == 3 || $saly == 6 || $saly == 11 || $saly == 12) {
        $sal->update(['period_day' => 0, 'leave_status_id' => $request->leave_status_id]);
      } elseif($saly == 9) {
        $sal->update(['period_time' => 0, 'leave_status_id' => $request->leave_status_id]);
      }

      if($sauser->belongstoleaveapprovalflow->backup_approval == 1){                                // update on backup
        $sal->hasmanyleaveapprovalbackup()->update([/*'staff_id' => \Auth::user()->belongstostaff->id,*/ 'leave_status_id' => $request->leave_status_id, 'remarks' => 'Rejected by Director ('.\Auth::user()->belongstostaff->name.') on '.\Carbon\Carbon::now()->format('j M Y g:i a')]);
      }
      if($sauser->belongstoleaveapprovalflow->supervisor_approval == 1){                              // update on supervisor
        $sal->hasmanyleaveapprovalsupervisor()->update([/*'staff_id' => \Auth::user()->belongstostaff->id,*/ 'leave_status_id' => $request->leave_status_id, 'remarks' => 'Rejected by Director ('.\Auth::user()->belongstostaff->name.') on '.\Carbon\Carbon::now()->format('j M Y g:i a')]);
      }
      if($sauser->belongstoleaveapprovalflow->hod_approval == 1){                                 // update on hod
        $sal->hasmanyleaveapprovalhod()->update([/*'staff_id' => \Auth::user()->belongstostaff->id,*/ 'leave_status_id' => $request->leave_status_id, 'remarks' => 'Rejected by Director ('.\Auth::user()->belongstostaff->name.') on '.\Carbon\Carbon::now()->format('j M Y g:i a')]);
      }
      if($sauser->belongstoleaveapprovalflow->director_approval == 1){                              // update on director
        $sal->hasmanyleaveapprovaldir()->update([/*'staff_id' => \Auth::user()->belongstostaff->id,*/ 'leave_status_id' => $request->leave_status_id, 'remarks' => 'Rejected by Director ('.\Auth::user()->belongstostaff->name.') on '.\Carbon\Carbon::now()->format('j M Y g:i a')]);
      }
      if($sauser->belongstoleaveapprovalflow->hr_approval == 1){                                  // update on hr
        $sal->hasmanyleaveapprovalhr()->update(['staff_id' => \Auth::user()->belongstostaff->id, 'leave_status_id' => $request->leave_status_id, 'remarks' => 'Rejected by Director ('.\Auth::user()->belongstostaff->name.') on '.\Carbon\Carbon::now()->format('j M Y g:i a')]);
      }
      // remove leave_id from attendance
      $z = HRAttendance::where('leave_id', $sal->id)->get();
      foreach ($z as $s) {
        HRAttendance::where('id', $s->id)->update(['leave_id' => null]);
      }
    } elseif($request->leave_status_id == 6) {                // leave waived, so need to put back all leave period.

    }
    // Session::flash('message', 'Successfully make an approval.');
    // return redirect()->back();
    return response()->json([
        'status' => 'success',
        'message' => 'Successfully make an approval.',
      ]);
  }

  public function deactivatestaff(Request $request, Staff $staff): JsonResponse
  {
    // dd($request->all());
    $staff->update(['active' => 0]);
    // $staff->hasmanylogin()->where('active', 1)->update(['active' => 0]);
    return response()->json([
      'status' => 'success',
      'message' => 'Staff '.$staff->name.' successfully deactivated',
    ]);
  }

  public function deletecrossbackup(Request $request, Staff $staff): JsonResponse
  {
    $staff->crossbackupto()->detach($request->id);
    return response()->json([
      'status' => 'success',
      'message' => 'Cross backup for '.$staff->name.' been deactivated.',
    ]);
  }

  public function staffactivate(Request $request, Staff $staff): RedirectResponse
  {
    $staff->update(['active' => 1]);
    $staff->hasmanylogin()->update(['active' => 0]);
    $staff->hasmanylogin()->create([
                      'username' => $request->username,
                      'password' => $request->password,
                      'active' => 1,
                    ]);
    Session::flash('message', 'Successfully activate ex-staff '.$staff->name);
    return redirect()->route('staff.show', $staff->id);
  }

  public function generateannualleave(Request $request)
  {
    // checking to make sure there is no duplicate year for 1 person
    $r = HRLeaveAnnual::where('year', now()->addYear()->format('Y'))->get()->isEmpty();
    if ($r) {
      $s = Staff::where('active', 1)->get();
      foreach ($s as $st) {
        $al = HRLeaveAnnual::where('year', now()->year)->where('staff_id', $st->id)->first();
        $st->hasmanyleaveannual()->create([
                          'year' => now()->addYear()->format('Y'),
                          'annual_leave' => $al?->annual_leave + $al?->annual_leave_adjustment,
                          'annual_leave_adjustment' => 0,
                          'annual_leave_utilize' => 0,
                          'annual_leave_balance' => $al?->annual_leave + $al?->annual_leave_adjustment,
                        ]);
      }
    } else {
      return response()->json([
        'status' => 'error',
        'message' => 'You have generate annual leave for next year. System couldn\'t generate anymore annual leave entitlement for all users in next year ('.now()->addYear()->format('Y').')',
      ]);
    }

    return response()->json([
      'status' => 'success',
      'message' => 'Success generate annual leave for next year',
    ]);
  }

  public function generatemcleave(Request $request)
  {
    // checking to make sure there is no duplicate year for 1 person
    $r = HRLeaveMC::where('year', now()->addYear()->format('Y'))->get()->isEmpty();
    if ($r) {
      $s = Staff::where('active', 1)->get();
      foreach ($s as $st) {
        $al = HRLeaveMC::where('year', now()->year)->where('staff_id', $st->id)->first();
        $st->hasmanyleavemc()->create([
                          'year' => now()->addYear()->format('Y'),
                          'mc_leave' => $al?->mc_leave + $al?->mc_leave_adjustment,
                          'mc_leave_adjustment' => 0,
                          'mc_leave_utilize' => 0,
                          'mc_leave_balance' => $al?->mc_leave + $al?->mc_leave_adjustment,
                        ]);
      }
    } else {
      return response()->json([
        'status' => 'error',
        'message' => 'You have generate medical certificate leave for next year. System couldn\'t generate anymore medical certificate leave entitlement for all users in next year ('.now()->addYear()->format('Y').')',
      ]);
    }

    return response()->json([
      'status' => 'success',
      'message' => 'Success generate medical certificate leave for next year',
    ]);
  }

  public function generatematernityleave(Request $request)
  {
    // checking to make sure there is no duplicate year for 1 person
    $r = HRLeaveMaternity::where('year', now()->addYear()->format('Y'))->get()->isEmpty();
    if ($r) {
      $s = Staff::where('active', 1)->get();
      foreach ($s as $st) {
        $al = HRLeaveMaternity::where('year', now()->year)->where('staff_id', $st->id)->first();
        $st->hasmanyleavematernity()->create([
                          'year' => now()->addYear()->format('Y'),
                          'maternity_leave' => $al?->maternity_leave + $al?->maternity_leave_adjustment,
                          'maternity_leave_adjustment' => 0,
                          'maternity_leave_utilize' => 0,
                          'maternity_leave_balance' => $al?->maternity_leave + $al?->maternity_leave_adjustment,
                        ]);
      }
    } else {
      return response()->json([
        'status' => 'error',
        'message' => 'You have generate medical certificate leave for next year. System couldn\'t generate anymore medical certificate leave entitlement for all users in next year ('.now()->addYear()->format('Y').')',
      ]);
    }

    return response()->json([
      'status' => 'success',
      'message' => 'Success generate medical certificate leave for next year',
    ]);
  }

  public function uploaddoc(Request $request, HRLeave $hrleave)
  {
    // dd($request->all());

    $validated = $request->validate([
        'document' => 'required|file|max:5120|mimes:jpeg,jpg,png,bmp,pdf,doc,docx',
        'amend_note' => 'required',
      ],
      [
        // 'document.required' => 'Please choose supporting document',
        // 'amend_note.required' => 'Please insert :attribute to approve leave, otherwise it wont be necessary for leave application reject',
      ],
      [
        'document' => 'Supporting Document',
        'amend_note' => 'Remarks'
      ]
    );

    if($request->file('document')){
      $file = $request->file('document')->getClientOriginalName();
      $currentDate = Carbon::now()->format('Y-m-d His');
      $fileName = $currentDate . '_' . $file;
      // Store File in Storage Folder
      $request->document->storeAs('public/leaves', $fileName);
      // storage/app/uploads/file.png
      // Store File in Public Folder
      // $request->document->move(public_path('uploads'), $fileName);
      // public/uploads/file.png
      // $data += ['softcopy' => $fileName];
    }
    $t = $hrleave->update(['softcopy' => $fileName]);
    if (!$hrleave->hasmanyleaveamend()->count()) {
      $hrleave->hasmanyleaveamend()->create( Arr::add(Arr::add($request->only(['amend_note']), 'staff_id', \Auth::user()->belongstostaff->id), 'date', now()) );
    } else {
      foreach (HRLeaveAmend::where('leave_id', $hrleave->id)->get() as $v) {
        HRLeaveAmend::find($v->id)->update([
          'amend_note' => ucwords(Str::lower($v->amend_note)).'<br />'.ucwords(Str::lower($request->amend_note)),
          'staff_id' => \Auth::user()->belongstostaff->id,
          'date' => now()
        ]);
      }
    }
    return redirect()->back();
  }

  public function confirmoutstationattendance(Request $request)
  {
    $validated = $request->validate([
        'id' => 'required',
      ],
      [],
      [
        'id' => 'Outstation Attendance',
      ]
    );
    // dd($request->all());
    foreach($request->id as $r) {
      // dd($r);
      $oa = HROutstationAttendance::find($r);
      $oa->update([
        'confirm' => 1,
        'date_confirm' => now()
      ]);
      HRAttendance::where([['staff_id', $oa->staff_id], ['attend_date', $oa->date_attend]])->update([
        'in' => $oa->in,
        'out' => $oa->out,
      ]);
    }
    return redirect()->back();
  }



































































  // used by queue batches
  public function progress(Request $request): JsonResponse
  {
    if (!$request->id) {
      if (session()->exists('lastBatchId')) {
        $bid = session()->get('lastBatchId');
      } elseif (session()->exists('lastBatchIdPay')) {
        $bid = session()->get('lastBatchIdPay');
      } else {
        $bid = 1;
      }
    } else {
      $bid = $request->id;
    }
    $batch = Bus::findBatch($bid);
    return response()->json([
      'processedJobs' => $batch->processedJobs(),
      'totalJobs' => $batch->totalJobs,
      'progress' => $batch->progress()
    ]);
  }
}
