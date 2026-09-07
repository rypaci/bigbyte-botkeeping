<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\WRMChangesapfiltercounteralkaline;

class WRMChangeSAPfilterAlkalineController extends Controller
{
    public function index(Request $request){

    }
    public function create(Request $request){
        return view('wrm-change-sap-alkaline-filter.create');
    }

    public function store(Request $request){

        $this->validate($request, [
            'bots_no' => 'required',
        ]);
        WRMChangesapfiltercounteralkaline::create($request->all());

        return redirect()->route('wrm-change-sap-alkaline-filter.create')
                        ->with('success','No. of bottles to change SAP Akaline filter is save.');

    }
}
