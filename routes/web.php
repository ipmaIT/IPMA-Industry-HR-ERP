<?php
use Illuminate\Support\Facades\Route;

/* guest */
use App\Http\Controllers\Auth\{
	NewPasswordController,
	PasswordResetLinkController,
	AuthenticatedSessionController,
	RegisteredUserController,
};


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/openai', function() {
	$response = Http::withToken(config('services.openai.secret'))
				->post("https://api.openai.com/v1/chat/completions", [
							"model"=> "gpt-3.5-turbo",
							"messages"=> [
								[
									"role"=> "system",
									"content"=> "You are a poetic assistant, skilled in explaining complex programming concepts with creative flair."
								],
								[
									"role"=> "user",
									"content"=> "Compose a poem that explains the concept of recursion in programming."
								],
							]
				])->json();
				dd($response);
});

Route::middleware('guest')->group(function(){

	Route::get('/', function () {
		return view('welcome');
	});

	Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
	Route::post('register', [RegisteredUserController::class, 'store']);
	Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
	Route::post('login', [AuthenticatedSessionController::class, 'store']);
	Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
	Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
	Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
	Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

require __DIR__.'/auth.php';

#############################################################################################
// ipma erp general resources controller
// require __DIR__.'/General/ajax.php';

#############################################################################################
// ipma erp human resources controller
require __DIR__.'/HumanResources/hr.php';
require __DIR__.'/HumanResources/ajax_hr.php';

#############################################################################################
// ipma erp cps (sales department) controller
require __DIR__.'/Sales/sales.php';
require __DIR__.'/Sales/ajax_sales.php';

#############################################################################################
// ipma erp cps (costing department) controller
require __DIR__.'/Costing/costing.php';
require __DIR__.'/Costing/ajax_costing.php';

