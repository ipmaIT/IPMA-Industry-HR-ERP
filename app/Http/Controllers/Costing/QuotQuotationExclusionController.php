<?php

namespace App\Http\Controllers\Costing;

use App\Http\Controllers\Controller;

// load model
use App\Models\QuotQuotationExclusion;

use Illuminate\Http\Request;

use Session;

class QuotQuotationExclusionController extends Controller
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

	public function show(QuotQuotationExclusion $quotExclusion)
	{
	//
	}

	public function edit(QuotQuotationExclusion $quotExclusion)
	{
	}

	public function update(Request $request, QuotQuotationExclusion $quotExclusion)
	{
	//
	}

	public function destroy(QuotQuotationExclusion $quotExclusion)
	{
		// $quotExclusion->destroy();
		QuotQuotationExclusion::destroy($quotExclusion->id);
		return response()->json([
			'message' => 'Data deleted',
			'status' => 'success'
		]);
	}
}

