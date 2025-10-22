<?php

namespace App\Http\Controllers\Costing;

use App\Http\Controllers\Controller;

// load model
use App\Models\QuotItemAttribute;

use Illuminate\Http\Request;

use Session;

class QuotationItemAttributeController extends Controller
{
	function __construct()
	{
		$this->middleware('auth');
		$this->middleware('highMgmtAccess:1|2|4|5,NULL'/*, ['only' => ['show', 'edit', 'update']]*/);
		$this->middleware('highMgmtAccessLevel1:1|5,14', ['only' => ['create', 'show', 'edit', 'update']]);
	}

	public function index()
	{
		return view('quotation.attrib.index');
	}

	public function create()
	{
		return view('quotation.attrib.create');
	}

	public function store(Request $request)
	{
		QuotItemAttribute::create($request->only('attribute'));
		Session::flash('message', 'Data successfully stored!');
		return redirect(route('quotItemAttrib.index'));
	}

	public function show(QuotItemAttribute $quotItemAttrib)
	{
	//
	}

	public function edit(QuotItemAttribute $quotItemAttrib)
	{
		return view('quotation.attrib.edit', compact('quotItemAttrib'));
	}

	public function update(Request $request, QuotItemAttribute $quotItemAttrib)
	{
		$quotItemAttrib->update( $request->only('attribute') );
		Session::flash('message', 'Data successfully updated!');
		return redirect(route('quotItemAttrib.index'));
	}

	public function destroy(QuotItemAttribute $quotItemAttrib)
	{
		// $quotItemAttrib->destroy();
		QuotItemAttribute::destroy($quotItemAttrib->id);
		return response()->json([
			'message' => 'Data deleted',
			'status' => 'success'
		]);
	}
}

