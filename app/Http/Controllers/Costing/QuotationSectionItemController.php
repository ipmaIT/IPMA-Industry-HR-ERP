<?php

namespace App\Http\Controllers\Costing;

use App\Http\Controllers\Controller;

// load model
use App\Models\QuotQuotationSectionItem;

use Illuminate\Http\Request;

use Session;

class QuotationSectionItemController extends Controller
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

	public function show(QuotQuotationSectionItem $quotSectionItem)
	{
	//
	}

	public function edit(QuotQuotationSectionItem $quotSectionItem)
	{
	}

	public function update(Request $request, QuotQuotationSectionItem $quotSectionItem)
	{
	//
	}

	public function destroy(QuotQuotationSectionItem $quotSectionItem)
	{
		$quotSectionItem->hasmanyquotsectionitemattrib()->delete();
		// $quotSectionItem->destroy();
		QuotQuotationSectionItem::destroy($quotSectionItem->id);
		return response()->json([
			'message' => 'Data deleted',
			'status' => 'success'
		]);
	}
}

