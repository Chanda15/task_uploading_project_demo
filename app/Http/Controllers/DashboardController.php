<?php

namespace App\Http\Controllers\Auth;

namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\Common;
use App\Models\Login;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
class DashboardController extends Controller
{
    protected $Common;
    protected $Login;
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    //protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Common $Common,Login $Login)
    {
		$this->Common = $Common;
		$this->Login = $Login;
		
        //$this->middleware('guest')->except('logout');
    }
	
	public function index(request $request)
    {
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		/***************Departwise dashboard Display****************/
		$LoginUser = $request->session()->get('user');
		$RoleId = $LoginUser->role_id;
		$data['title']= 'Dashboard';
		$data['toggle_menu']= 'xp-toggle-menu';
		return view('admin/dashboard/index',$data);
    }
	
}
