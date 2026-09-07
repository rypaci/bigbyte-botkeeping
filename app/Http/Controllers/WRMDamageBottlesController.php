<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\WRMDamageBottles;
use Carbon\Carbon;
use DateTime;
use DB;
use Session;

class WRMDamageBottlesController extends Controller
{
    public function index(Request $request){
        $damage_bottles = DB::table('w_r_m_damage_bottles')
        ->orderBy('w_r_m_damage_bottles.date', 'ASC')
        ->paginate(10);
        
        return view('wrm-damage-bottles.index', compact('damage_bottles'))
        ->with('i', ($request->input('page', 1) - 1) * 10);
    }
    public function create(Request $request){
        return view('wrm-damage-bottles.create');
    }

    public function store(Request $request){
        $this->validate($request, [
            'dmg_bottles' => 'required',
            'date' => 'required',
        ]);
        WRMDamageBottles::create($request->all());

        return redirect()->route('wrm-damage-bottles.create')
            ->with('success','No. of damage bottles are successfully added.');
    }
    
    public function edit($id){
        $damage_bottles = WRMDamageBottles::findOrFail($id);

        return view('wrm-damage-bottles.edit', compact('damage_bottles'));
    }

    public function update($id, Request $request){
        $damage_bottles = WRMDamageBottles::findOrFail($id);
        $damage_bottles->update($request->all());

        return redirect('wrm-damage-bottles/track-damage-bottles')
            ->with('success','Damage bottles updated.');
    }
    
    public function getRecordDestroy($id){
        WRMDamageBottles::destroy($id);
        return redirect('wrm-damage-bottles/track-damage-bottles')
            ->with('success','Damage bottles deleted.');
    }

    public function reportDetails(Request $request){
        $damage_bottles = DB::table('w_r_m_damage_bottles')
        ->orderBy('w_r_m_damage_bottles.date', 'ASC')
        ->paginate(10);
        
        return view('wrm-damage-bottles.track-damage-bottles', compact('damage_bottles'))
        ->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function dateByRange(Request $request){

        $start = $request->date_from;
        $end = $request->date_to;

        $damage_bottles = DB::table('w_r_m_damage_bottles')
        ->whereBetween('date', array($start, $end))
        ->orderBy('date', 'ASC')
        ->paginate(10);

        return view('wrm-damage-bottles.track-damage-bottles', compact('damage_bottles'))
        ->with('i', ($request->input('page', 1) - 1) * 10);
    }
}
