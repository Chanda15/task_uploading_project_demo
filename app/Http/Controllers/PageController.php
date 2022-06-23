<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Validator;
use App\Models\Common;
use Illuminate\Support\Facades\Redirect;
class PageController extends Controller
{
   protected $Common;
    public function __construct(Common $Common)
    {
		$this->Common = $Common;
    }
	function aboutus(){
		$data['meta_title'] =  'About Us | Sports';
		$data['meta_keywords'] =  'About Us | Sports';
		$data['meta_description'] = 'About Us | Sports';
		return view('frontend/pages/aboutUs',$data);
    }
	function events(){
		$data['event_list'] = $this->Common->geEvents();
		//print_r($data['event_list']); exit;
		$data['meta_title'] =  'Events | Sports';
		$data['meta_keywords'] =  'Events | Sports';
		$data['meta_description'] = 'Events | Sports';
		return view('frontend/pages/events',$data);
    }
   function results(){
		$data['meta_title'] =  'Results | Sports';
		$data['meta_keywords'] =  'Results | Sports';
		$data['meta_description'] = 'Results | Sports';
		return view('frontend/pages/results',$data);
    }
	function registration(){
		$data['meta_title'] =  'Registration | Sports';
		$data['meta_keywords'] =  'Registration | Sports';
		$data['meta_description'] = 'Registration | Sports';
		return view('frontend/pages/registration',$data);
    }
	 function save_registration(request $response){
		   $LastRecords = $this->Common->getLastRegistrationNumber();
	   if(empty($LastRecords)){
		   $RegistrationNumber = 1;
	   }else{
		   $RegistrationNumber = $LastRecords->registration_number+1;
	   }
	   $RegistrationNumber = str_pad($RegistrationNumber, 4, '0', STR_PAD_LEFT);
		 $sValidationRules = [
			'FirstName' => 'required|max:255',
			'LastName' => 'required|max:255',
			'email' => 'required|email',
			'FatherFirstName' => 'required|max:255',
			'FatherLastName' => 'required|max:255',
		  ];
		  $validator = Validator::make($response->all(), $sValidationRules);

		  if ($validator->fails()) // on validator found any error 
		  {
			// pass validator object in withErrors method & also withInput it should be null by default
			 return back()->withErrors($validator)->withInput();
		  }
		  $BDate = $response->day.'-'.$response->month.'-'.$response->year;
		  $BirthDate = date('Y-m-d', strtotime($BDate));
		   $data= array(
            'user_name'=>$response->FirstName.' '.$response->LastName,
            'first_name'=>$response->FirstName,
            'last_name'=>$response->LastName,
            'email_id'=>$response->email,
            'mobile_no'=>$response->phone,
            'father_first_name'=>$response->FatherFirstName,
            'father_last_name'=>$response->FatherLastName,
            'age'=>$response->Age,
            'birth_date'=>$BirthDate,
            'registration_number'=>$RegistrationNumber,
            'event_place'=>$response->EventPlace,
            'city'=>$response->City,
            'coach_name'=>$response->CoachNameContact,
            'user_address'=>$response->Address,
            'role_id'=>2,
            'password'=>$response->Password,
            'status'=>1,
        );
        $savedata =$this->Common->saveData('users',$data);
		$data['meta_title'] =  'Registration | Sports';
		$data['meta_keywords'] =  'Registration | Sports';
		$data['meta_description'] = 'Registration | Sports';
		$response->session()->flash('RegistrationMsg', 'Your Registration has been successfully completed.');
		return Redirect::to('/registration');
		//return view('frontend/pages/registration',$data);
    }
    function pagenotfound(){
		$data['meta_title'] = "404 | Sports";
		$data['meta_keywords'] = "404 | Sports";
		$data['meta_description'] = "404 | Sports";
        return view('frontend/pages/404',$data);
   }
   
}
