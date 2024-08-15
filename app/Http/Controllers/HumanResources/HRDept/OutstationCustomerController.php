<?php

namespace App\Http\Controllers\HumanResources\HRDept;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// load models
use App\Models\Customer;

// for controller output
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

// load array helper
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

// load Carbon
use \Carbon\Carbon;
use \Carbon\CarbonPeriod;
use \Carbon\CarbonInterval;

use Session;

class OutstationCustomerController extends Controller
{
  function __construct()
  {
    $this->middleware(['auth']);
    $this->middleware('highMgmtAccess:1|2|5,6|14', ['only' => ['create', 'store', 'index', 'show', 'edit', 'update']]);                                  // all high management
    $this->middleware('highMgmtAccessLevel1:1|5,14', ['only' => ['destroy']]);       // only hod and asst hod HR can access
  }

  /**
   * Display a listing of the resource.
   */
  public function index(): View
  {
    return view('humanresources.hrdept.outstation.outstationcustomer.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create(): View
  {
    return view('humanresources.hrdept.outstation.outstationcustomer.create');
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request): RedirectResponse
  {
    // dd($request->all());
    Customer::create([
      'customer' => $request->customer,
      'contact' => $request->contact,
      'phone' => $request->phone,
      'fax' => $request->fax,
      'address' => $request->address,
      'latitude' => $request->latitude,
      'longitude' => $request->longitude,
    ]);
    Session::flash('flash_message', 'Successfully add customer');
    return redirect()->route('outstationcustomer.index');
  }

  /**
   * Display the specified resource.
   */
  public function show(): View
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Customer $outstationcustomer): View
{
    // Return the view with the Customer instance
    return view('humanresources.hrdept.outstation.outstationcustomer.edit', ['outstationcustomer' => $outstationcustomer]);
}


  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Customer $outstationcustomer): RedirectResponse
  {
    $outstationcustomer->update([
      'customer' => $request->customer,
      'contact' => $request->contact,
      'phone' => $request->phone,
      'fax' => $request->fax,
      'address' => $request->address,
      'latitude' => $request->latitude,
      'longitude' => $request->longitude,
    ]);

    return redirect()->route('outstationcustomer.index')->with('flash_message', 'Successfully edit customer');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Customer $outstationcustomer): JsonResponse
  {
    // DELETE FROM TABLE ATTENDANCE
    Customer::destroy([
      'id' => $outstationcustomer['id']
    ]);

    // RETURN MESSAGE
    return response()->json([
    	'message' => 'Customer deleted',
    	'status' => 'success'
    ]);
  }
}
