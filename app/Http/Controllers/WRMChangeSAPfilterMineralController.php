<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\WRMChangesapfiltercountermineral;

class WRMChangeSAPfilterMineralController extends Controller
{
    public function index(Request $request){

    }
    public function create(Request $request){
        return view('wrm-change-sap-mineral-filter.create');
    }

    public function store(Request $request){

        $this->validate($request, [
            'bots_no' => 'required',
        ]);
        WRMChangesapfiltercountermineral::create($request->all());

        return redirect()->route('wrm-change-sap-mineral-filter.create')
                        ->with('success','No. of bottles to change SAP Mineral filter is save.');

    }
}
