<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Option;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;

class OptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function postIndex(Request $request)
    {
        foreach( $request->all() as $option_key => $option_value ) {
            if($option_key == '_token') continue;
            
            $option = Option::firstOrNew(['name' => $option_key]);
            $option->value = $option_value;
            $option->save();
        }
        
        Session::flash('flash_message', 'Options saved.');
        
        // return redirect('option');
        return redirect('tax-setting');
    }
    
    public function getIndex()
    {
        $options = $this->getAllOptions();
        
        return view('option.index', compact('options'));
    }
    
    function getAllOptions()
    {
        $alloptions_db = Option::all();
        
        // dd($alloptions_db->toArray()); 
        // echo (is_a($alloptions_db, "Illuminate\Database\Eloquent\Collection")) ? 'yes' : 'no'; echo '<br>';
        // echo (is_callable([$alloptions_db, 'toArray'])) ? 'yes' : 'no'; echo '<br>';
        
        $alloptions = [];
        foreach( $alloptions_db as $o ) {
            $alloptions[$o->name] = $o->value;
        }
        
        return $alloptions;
    }
}
