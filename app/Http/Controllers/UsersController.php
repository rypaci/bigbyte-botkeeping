<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;
use Redirect;
use Auth;
use Image;

use App\Services\EmailService;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{

	public function __construct()
	{
		$this->middleware('auth');
		// $this->middleware('auth', ['only' => 'create']);
	}

    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index(Request $request)
    {
        // if ($request->user()->cannot('create-user')) {
           // abort(403, 'Unauthorized action.');
        // }

        $users = User::paginate(15);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create(Request $request)
    {
        if ($request->user()->cannot('create-user')) {
           abort(403, 'Unauthorized action.');
        }

        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return void
     */
    public function store(Request $request)
    {
        if ($request->user()->cannot('create-user')) {
           abort(403, 'Unauthorized action.');
        }

        $user = User::whereEmail($request->email)->first();

        if ($user) {
            return Redirect::back()->withErrors( 'User with the same email already exist.' );
        }



        $data = $request->all();
        $data['password'] = bcrypt($this->getToken(8, Carbon::today()->timestamp));

        $user = User::create($data);

        $data = [
            'user'      => $user,
            'admin'     => \Auth::user(),
            'password'  => $this->getToken(8, Carbon::today()->timestamp)
        ];
		return redirect('users');

        EmailService::sendWelcomeEmail($data, $user);

        return redirect('users');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function show($id)
    {
        // if ($request->user()->cannot('create-user')) {
           // abort(403, 'Unauthorized action.');
        // }

        $user = User::findOrFail($id);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function update($id, Request $request)
    {
		// print_r( $request->all() );
		// die;
        $user = User::findOrFail($id);
        $user->update(['name'=>$request->name, 'access_token'=>$request->access_token]);

        return redirect('users');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function destroy($id)
    {
        User::destroy($id);

        Session::flash('flash_message_success', 'User deleted!');

        return redirect('users');
    }

    private function getToken($length, $seed)
	{
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "0123456789";

        mt_srand($seed);

        for($i=0;$i<$length;$i++){
            $token .= $codeAlphabet[mt_rand(0,strlen($codeAlphabet)-1)];
        }
        return $token;
    }

	public function getAccessToken(Request $request)
	{
		return $this->getToken(15, time());
	}
	
	public function getProfile()
	{
		$user = Auth::user();
        return view('users.profile', compact('user'));
	}
	
	public function postProfile(Request $request)
	{
		$user = Auth::user();
		
		$validator = Validator::make($request->all(), [
			'avatar' => 'max:3500',
		]);
		
		if ($validator->fails()){
			return view('users.profile', compact('user'))
				->withErrors($validator);
		}
		
		/* File */
		if($request->file('avatar')){
			ini_set('memory_limit','-1');
			
			$avatar = $request->file('avatar');
			
			$filename = strtolower( $avatar->getClientOriginalName() );
			$filename = time() . '-' . $filename;
			
			Image::make($avatar)->resize(150, 150)->save( public_path("uploads/images/{$filename}") );
			
			$user->avatar = $filename;
		}
		
		$user->name = $request->name;
		$user->save();
		
		Session::flash('flash_message_success', 'Profile updated!');
		
		return view('users.profile', compact('user'));
	}
}
