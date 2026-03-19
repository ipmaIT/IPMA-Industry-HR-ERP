<?php
use Illuminate\Support\Facades\Route;

/* auth */
use App\Http\Controllers\Auth\{
	EmailVerificationNotificationController,
	EmailVerificationPromptController,
	AuthenticatedSessionController,
	ConfirmablePasswordController,
	VerifyEmailController,
	PasswordController,
};

/* authenticate user */
Route::middleware('auth')->group(function(){
	Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
	Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
	Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware('throttle:6,1')->name('verification.send');
	Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
	Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);	Route::put('password', [PasswordController::class, 'update'])->name('password.update');
	Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

	/* audit section ONLY for admin */
	Route::middleware('adminAccess')->group(function () {
		Route::controller(\App\Http\Controllers\System\ActivityLogController::class)->group(function () {
			Route::get('/activity-logs/getActivityLogs', 'getActivityLogs')->name('getActivityLogs');
			Route::prefix('/activity-logs')->name('activity-logs.')->group(function () {
				Route::get('/', 'index')->name('index');
				Route::get('/{log}', 'show')->name('show');
				Route::delete('{log}', 'destroy')->name('destroy');
			});

		});
	});

	/* verified email user */
	Route::middleware('verified')->group(function(){
		Route::get('/dashboard', function () {
			return view('welcome');
		})->name('dashboard');

	});









});
