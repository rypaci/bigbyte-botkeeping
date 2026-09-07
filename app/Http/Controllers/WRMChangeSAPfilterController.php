<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\WRMChangesapfilter;

class WRMChangeSAPfilterController extends Controller
{
    public function index(Request $request){

    }
    public function create(Request $request){
        return view('wrm-change-sap-filter.create');
    }

    public function store(Request $request){

        $this->validate($request, [
            'bots_no' => 'required',
        ]);
        WRMChangesapfilter::create($request->all());

        return redirect()->route('wrm-change-sap-filter.create')
                        ->with('success','No. of bottles to change 2 SAP filter is save.');

    }
}
