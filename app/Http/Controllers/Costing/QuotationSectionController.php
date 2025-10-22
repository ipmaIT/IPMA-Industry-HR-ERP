<?php

namespace App\Http\Controllers\Costing;

use App\Http\Controllers\Controller;

// load model
use App\Models\QuotQuotationSection;

use Illuminate\Http\Request;

use Session;

class QuotationSectionController extends Controller
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

	public function show(QuotQuotationSection $quotSection)
	{
	//
	}

	public function edit(QuotQuotationSection $quotSection)
	{
	}

	public function update(Request $request, QuotQuotationSection $quotSection)
	{
	//
	}

	public function destroy(QuotQuotationSection $quotSection)
	{
		foreach($quotSection->hasmanyquotsectionitem()->get() as $sec) {
			$sec->hasmanyquotsectionitemattrib()->delete();
		}
		$quotSection->hasmanyquotsectionitem()->delete();
		// $quotSection->destroy();
		QuotQuotationSection::destroy($quotSection->id);
		return response()->json([
			'message' => 'Data deleted',
			'status' => 'success'
		]);
	}
}
