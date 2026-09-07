<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\WRMOriginalBottles;
use Carbon\Carbon;
use DateTime;
use DB;
use Session;

class WRMOriginalBottlesController extends Controller
{
    public function index(Request $request){
        $original_bottles = DB::table('w_r_m_original_bottles')
        ->orderBy('w_r_m_original_bottles.date', 'ASC')
        ->paginate(10);
        
        return view('wrm-original-bottles.index', compact('original_bottles'))
        ->with('i', ($request->input('page', 1) - 1) * 10);
        //$original_bottles = '';
        //return view('wrm-original-bottles.index', compact('original_bottles'));
    }
    public function create(Request $request){
        return view('wrm-original-bottles.create');
    }

    public function store(Request $request){
        $this->validate($request, [
            'orig_bottles' => 'required',
            'date' => 'required',
        ]);
        WRMOriginalBottles::create($request->all());

        return redirect()->route('wrm-original-bottles.create')
            ->with('success','No. of original bottles are successfully added.');
    }

    public function show($id)
	{
		//$water_refillings = DB::table('water_refillings')->where('entry_no', $id)->orderBy('entry_no', 'DESC')->first();
		//$last2 = DB::table('items')->orderBy('id', 'DESC')->first();
		//return view('water-refilling-monitoring.create',compact('water_refillings'));
		//return view('product.show',compact('product'));
	}
    
    public function edit($id){
        $original_bottles = WRMOriginalBottles::findOrFail($id);

        return view('wrm-original-bottles.edit', compact('original_bottles'));
    }
    public function update($id, Request $request){
        $original_bottles = WRMOriginalBottles::findOrFail($id);
        $original_bottles->update($request->all());

        return redirect('wrm-original-bottles/track-original-bottles')
            ->with('success','Original bottles updated.');
    }
    public function getRecordDestroy($id){
        WRMOriginalBottles::destroy($id);
        return redirect('wrm-original-bottles/track-original-bottles')
            ->with('success','Original bottles deleted.');
    }

    public function reportDetails(Request $request){
        $original_bottles = DB::table('w_r_m_original_bottles')
        ->orderBy('w_r_m_original_bottles.date', 'ASC')
        ->paginate(10);
        
        return view('wrm-original-bottles.track-original-bottles', compact('original_bottles'))
        ->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function dateByRange(Request $request){

        $start = $request->date_from;
        $end = $request->date_to;

        $original_bottles = DB::table('w_r_m_original_bottles')
        ->whereBetween('date', array($start, $end))
        ->orderBy('date', 'ASC')
        ->paginate(10);

        return view('wrm-original-bottles.track-original-bottles', compact('original_bottles'))
        ->with('i', ($request->input('page', 1) - 1) * 10);
    }
}
