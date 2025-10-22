<?php

namespace App\Http\Controllers\Costing;

use App\Http\Controllers\Controller;

// load model
use App\Models\QuotBank;

use Illuminate\Http\Request;

use Session;

class QuotationBankController extends Controller
{
	function __construct()
	{
		$this->middleware('auth');
		$this->middleware('highMgmtAccess:1|2|4|5,NULL'/*, ['only' => ['show', 'edit', 'update']]*/);
		$this->middleware('highMgmtAccessLevel1:1|5,14', ['only' => ['create', 'show', 'edit', 'update']]);
	}

	public function index()
	{
		return view('quotation.bank.index');
	}

	public function create()
	{
		return view('quotation.bank.create');
	}

	public function store(Request $request)
	{
		QuotBank::create($request->only('bank'));
		Session::flash('message', 'Data successfully stored!');
		return redirect(route('quotBank.index'));
	}

	public function show(QuotBank $quotBank)
	{
	//
	}

	public function edit(QuotBank $quotBank)
	{
		return view('quotation.bank.edit', compact(['quotBank']));
	}

	public function update(Request $request, QuotBank $quotBank)
	{
		$quotBank->updated($request->only('bank'));
		Session::flash('message', 'Data successfully updated!');
		return redirect(route('quotBank.index'));
	}

	public function destroy(QuotBank $quotBank)
	{
		// $quotBank->destroy();
		QuotBank::destroy($quotBank->id);
		return response()->json([
			'message' => 'Data deleted',
			'status' => 'success'
		]);
	}
}

