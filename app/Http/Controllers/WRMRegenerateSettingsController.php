<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\WRMRegenerateSettings;

class WRMRegenerateSettingsController extends Controller
{
    public function index(Request $request){

    }
    public function create(Request $request){
        return view('wrm-regenerate-settings.create');
    }

    public function store(Request $request){

        $this->validate($request, [
            'reg_bots_value' => 'required',
        ]);
        WRMRegenerateSettings::create($request->all());

        return redirect()->route('wrm-regenerate-settings.create')
                        ->with('success','No. of bottles to regenerates is save.');

    }
}
