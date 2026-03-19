<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AjaxSupportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth:sanctum', 'auth'])->group(function () {
// Route::middleware('auth')->group(function () {
  Route::controller(AjaxSupportController::class)->group(function () {

    /* add ur api route below */
    Route::post('/loginuser', 'loginuser')->name('loginuser');
    Route::post('/icuser', 'icuser')->name('icuser');
    Route::post('/emailuser', 'emailuser')->name('emailuser');
    Route::post('/leaveType', 'leaveType')->name('leaveType.leaveType');
    Route::post('/backupperson', 'backupperson')->name('backupperson');
    Route::post('/unavailabledate', 'unavailabledate')->name('leavedate.unavailabledate');
    Route::post('/timeleave', 'timeleave')->name('leavedate.timeleave');
    Route::post('/leavestatus', 'leavestatus')->name('leavestatus.leavestatus');

    Route::post('/authorise', 'authorise')->name('authorise.authorise');
    Route::post('/branch', 'branch')->name('branch.branch');
    Route::post('/customer', 'customer')->name('customer.customer');
    Route::post('/uom', 'uom')->name('uom.uom');
    Route::get('/machine', 'machine')->name('machine.machine');
    Route::get('/machineaccessories', 'machineaccessories')->name('machineaccessories.machineaccessories');
    // Route::post('/jdescgetitem', 'jdescgetitem')->name('jdescgetitem.jdescgetitem');
    Route::post('/category', 'category')->name('category.category');
    Route::post('/country', 'country')->name('country.country');
    Route::post('/department', 'department')->name('department.department');
    Route::post('/division', 'division')->name('division.division');
    Route::post('/educationlevel', 'educationlevel')->name('educationlevel.educationlevel');
    Route::post('/gender', 'gender')->name('gender.gender');
    Route::post('/healthstatus', 'healthstatus')->name('healthstatus.healthstatus');
    Route::post('/maritalstatus', 'maritalstatus')->name('maritalstatus.maritalstatus');
    Route::post('/religion', 'religion')->name('religion.religion');
    Route::post('/race', 'race')->name('race.race');
    Route::post('/taxexemptionpercentage', 'taxexemptionpercentage')->name('taxexemptionpercentage.taxexemptionpercentage');
    Route::post('/relationship', 'relationship')->name('relationship.relationship');
    Route::post('/status', 'status')->name('status.status');
    Route::post('/department', 'department')->name('department.department');
    Route::post('/restdaygroup', 'restdaygroup')->name('restdaygroup.restdaygroup');
    Route::post('/staffcrossbackup', 'staffcrossbackup')->name('staffcrossbackup.staffcrossbackup');
    Route::post('/unblockhalfdayleave', 'unblockhalfdayleave')->name('unblockhalfdayleave.unblockhalfdayleave');
    Route::post('/leaveevents', 'leaveevents')->name('leaveevents');
    Route::post('/division', 'division')->name('division');
    Route::post('/staffattendance', 'staffattendance')->name('staffattendance');
    Route::post('/staffattendancelist', 'staffattendancelist')->name('staffattendancelist');
    Route::post('/staffpercentage', 'staffpercentage')->name('staffpercentage');
    Route::post('/yearworkinghourstart', 'yearworkinghourstart')->name('yearworkinghourstart');
    Route::post('/yearworkinghourend', 'yearworkinghourend')->name('yearworkinghourend');
    Route::post('/hcaldstart', 'hcaldstart')->name('hcaldstart');
    Route::post('/hcaldend', 'hcaldend')->name('hcaldend');
    Route::post('/staffdaily', 'staffdaily')->name('staffdaily');
    Route::post('/samelocationstaff', 'samelocationstaff')->name('samelocationstaff');
    Route::post('/overtimerange', 'overtimerange')->name('overtimerange');
    Route::post('/branchattendancelist', 'branchattendancelist')->name('branchattendancelist');
    Route::post('/outstationattendancestaff', 'outstationattendancestaff')->name('outstationattendancestaff');
    Route::post('/outstationattendancelocation', 'outstationattendancelocation')->name('outstationattendancelocation');
    Route::post('/staffoutstationduration', 'staffoutstationduration')->name('staffoutstationduration');
    Route::post('/attendanceabsentindicator', 'attendanceabsentindicator')->name('attendanceabsentindicator');
    Route::post('/week_dates', 'week_dates')->name('week_dates');
    Route::get('/sales/getOptSalesGetItem', 'getOptSalesGetItem')->name('getOptSalesGetItem');


    /* ajax_hr */
/*    Route::patch('/leavecancel/{hrleave}', 'leavecancel')->name('leavecancel.leavecancel');
    Route::patch('/uploaddoc/{hrleave}', 'uploaddoc')->name('uploaddoc');
    Route::patch('/leaverapprove/{hrleaveapprovalbackup}', 'leaverapprove')->name('leaverapprove.leaverapprove');
    Route::patch('/supervisorstatus', 'supervisorstatus')->name('leavestatus.supervisorstatus');
    Route::patch('/hodstatus', 'hodstatus')->name('leavestatus.hodstatus');
    Route::patch('/dirstatus', 'dirstatus')->name('leavestatus.dirstatus');
    Route::patch('/hrstatus', 'hrstatus')->name('leavestatus.hrstatus');
    Route::patch('/deactivatestaff/{staff}', 'deactivatestaff')->name('deactivatestaff');
    Route::delete('/deletecrossbackup/{staff}', 'deletecrossbackup')->name('deletecrossbackup');
    Route::patch('/staffactivate/{staff}', 'staffactivate')->name('staff.activate');
    Route::post('/generateannualleave', 'generateannualleave')->name('generateannualleave');
    Route::post('/generatemcleave', 'generatemcleave')->name('generatemcleave');
    Route::post('/generatematernityleave', 'generatematernityleave')->name('generatematernityleave');
    Route::post('/confirmoutstationattendance', 'confirmoutstationattendance')->name('confirmoutstationattendance');
*/



















    // progress for excel generate
    Route::get('/progress','progress')->name('progress');

  });
});
