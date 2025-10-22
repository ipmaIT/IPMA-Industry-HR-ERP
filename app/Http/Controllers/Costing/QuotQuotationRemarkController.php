<?php

namespace App\Http\Controllers\Costing;

use App\Http\Controllers\Controller;

// load model
use App\Models\QuotQuotationRemark;

use Illuminate\Http\Request;

use Session;

class QuotQuotationRemarkController extends Controller
{
	function __construct()
	{
		$this->middleware('auth');
		$this->middleware('highMgmtAccess:1|2|4|5,NULL'/*, ['only' => ['show', 'edit', 'update']]*/);
		$this->middleware('highMgmtAccessLevel1:1|5,14', ['only' => ['create', 'show', 'edit', 'update']]);
	}

	public function index()
	{
	}

	public function create()
	{
	}

	public function store(Request $request)
	{
	}

	public function show(QuotQuotationRemark $quotRemark)
	{
	//
	}

	public function edit(QuotQuotationRemark $quotRemark)
	{
	}

	public function update(Request $request, QuotQuotationRemark $quotRemark)
	{
	//
	}

	public function destroy(QuotQuotationRemark $quotRemark)
	{
		// $quotRemark->destroy();
		QuotQuotationRemark::destroy($quotRemark->id);
		return response()->json([
			'message' => 'Data deleted',
			'status' => 'success'
		]);
	}
}

