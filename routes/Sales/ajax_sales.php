<?php
// Continuence from routes/web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sales\AjaxController;

// Ajax Controller : to CRUD data on the DB

Route::middleware('auth')->group(function () {
	Route::controller(AjaxController::class)->group(function () {

	Route::patch('/saleamend/{saleamend}', 'saleamend')->name('saleamend');
	Route::patch('/saleapproved/{saleapproved}', 'saleapproved')->name('saleapproved');
	Route::patch('/salesend/{salesend}', 'salesend')->name('salesend');

		Route::prefix('/sales')->name('sales.')->group(function () {

			Route::get('/getOptSalesType', 'getOptSalesType')->name('getOptSalesType');
			Route::get('/getOptSalesDeliveryType', 'getOptSalesDeliveryType')->name('getOptSalesDeliveryType');

		});

	});
});


// http://localhost:8000/sales/getOptSalesType



// Ajax DB Controller : only to retrieve data from db
// Route::post('/loginuser', [AjaxDBController::class, 'loginuser'])->name('loginuser');

// Route::get('/login/{login}', [
// 	'as' => 'login.edit',
// 	'uses' => 'Profile\LoginController@edit'
// ]);
