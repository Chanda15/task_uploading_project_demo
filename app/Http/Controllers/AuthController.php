<?php
namespace App\Http\Controllers\Auth;
namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\Common;
use App\Models\Login;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Helper;
use PHPMailer\PHPMailer\PHPMailer;
class AuthController extends Controller
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
	public function index()
    {
		if (Session::has('user')){
			return Redirect::to('/dashboard');
		  // do some thing if the key is exist
		}else{
			
		  //the key does not exist in the session
		}
		$data['title']= 'Login';
		return view('admin/auth/login',$data);
    }
	public function register()
    {
		$data['Departmentlist']=$this->Common->getList('department');
		//$data['CategoryList']=$this->Common->getList('category', 'parent_id',0);
		/*$ArrData=array(
		'parent_id'=>0
		);
		$data['CategoryList']=$this->Common->getData('category', $ArrData);*/
		$data['CompanyList']=$this->Common->getList('companies');
		$data['RoleList']=$this->Common->getRoleList('roles');
		$data['RegionList']=$this->Common->getList('regions');
		$data['title']= 'Register';
		return view('admin/auth/register',$data);
    }
	public function getSelectionData(request $response){
		$TableName = $response->TableName;
		$SelectionValue = $response->SelectionValue;
		$DBColumnId = $response->DBColumnId;
		$ShowDBColumnName = $response->ShowDBColumnName;
		$ShowDBColumnId = $response->ShowDBColumnId;
		$AppendToId = $response->AppendToId;
		$List = $this->Common->getList($TableName, $DBColumnId, $SelectionValue);
		$option='<option value="">Select '.$AppendToId.'</option>';
		foreach($List as $key => $val){
			$option .='<option value="'.$val->$ShowDBColumnId.'">'.$val->$ShowDBColumnName.'</option>';
		}
		echo $option;
	}
	/***************** Use this function after login ********************/
	public function getSelectionUserByRegion(request $response){ 
		$data['User'] = $response->session()->get('user');
		$TableName = $response->TableName;
		$SelectionValue = $response->SelectionValue;
		$DBColumnId = $response->DBColumnId;
		$ShowDBColumnName = $response->ShowDBColumnName;
		$ShowDBColumnId = $response->ShowDBColumnId;
		$AppendToId = $response->AppendToId;
		$ArrData=array(
			$DBColumnId=>$SelectionValue,
			'company_id'=>$response->session()->get('company_session_id'),
			'status'=>1
		);
		//$List = $this->Common->getData($TableName, $ArrData);
		$List = $this->Common->getSelectionUserByRegion($SelectionValue, $response->session()->get('company_session_id'));
		//$Adminlist=$this->Common->getList('users', 'role_id', $data['User']->role_id);
		$option='<option value="">Select '.$AppendToId.'</option>';
		//foreach($Adminlist as $key => $val){
		//	$option .='<option value="'.$val->id.'">'.$val->user_name.'</option>';
		//}
		$option .='<option value="1">Admin</option>';
		foreach($List as $key => $val){
			$option .='<option value="'.$val->$ShowDBColumnId.'">'.$val->$ShowDBColumnName.'</option>';
		}
		echo $option;
	}
	/***************** Use this function Before login ********************/
	public function getSelectionCategoryByCompany(request $response){ 
		$TableName = $response->TableName;
		$SelectionValue = $response->SelectionValue;
		$DBColumnId = $response->DBColumnId;
		$ShowDBColumnName = $response->ShowDBColumnName;
		$ShowDBColumnId = $response->ShowDBColumnId;
		$AppendToId = $response->AppendToId;
		$ArrData=array(
			'parent_id'=>0,
			$DBColumnId=>$SelectionValue,
			'status'=>'active',
		);
		$List = $this->Common->getData($TableName, $ArrData);
		$option='<option value="">Select '.$AppendToId.'</option>';
		foreach($List as $key => $val){
			$option .='<option value="'.$val->$ShowDBColumnId.'">'.$val->$ShowDBColumnName.'</option>';
		}
		echo $option;
	}
	public function checkEmail(request $response){
		$Email = $response->Email;
		
		$List = $this->Common->getList('users', 'email_id', $Email);
		//print_r($List);
		if($List->count() > 0)
			echo 1;
		else
			echo 0;
	}
	public function submit_forgot_data(request $response){
		$Email = $response->Email;
		$dataLogin= array(
				'email_id'=>$Email,
			);
		$List = $this->Login->checkLoginDetails('users', $dataLogin);
		//print_r($List); exit;
		if($List->count() > 0){
			$RandomCode = $this->getRandomCode();
			/********************** Remain Email Functionality ***********************/
			$dataArr['forgot_code']=$RandomCode;
			$this->Common->updateData('users', 'email_id', $Email, $dataArr);
			/*
				$subject ='Reset Password Link';
				$bodySales = '<div style="font-family:arial; font-size:14px;">
					<div class="row" >
				
					<div style="margin:30px 0px; padding:10px 15px 0px 15px; clear:both;">
					<p>Hi <span style="font-weight: bold;">Dear</span>,</p>
					<p style="margin-bottom:10px;">To reset password, please click on below link <br><a href="https://www.reportsanddata.com/reset_password/'.$RandomCode.'">Reset Password</a><br></p><p><b>Regards,</b><br>Business Development Team,<br>Emergrn Research.</p>
					</div>
					</div>
					</div>';
			
			
				$headers = "From: sales@reportsanddata.com\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
				
				$mailNew = new PHPMailer;
				$mailNew->Host = 'smtp.qlc.co.in';
				$mailNew->SMTPAuth = true;
				//$mailNew->SMTPSecure = 'tls';			
				//$mailNew->Username = 'websmtp@reportsanddata.com';
				//$mailNew->Password = 'mark$#@321';
				$mailNew->Username = 'sales@emergenresearch.com';
                $mailNew->Password = '#ld546t4#b';
				$mailNew->Port = 465;
				$mailNew->setFrom('sales@reportsanddata.com', 'Sales');
				$mailNew->addAddress('chanda.gurnule@reportsanddata.com', 'Agent');
				//$mailNew->addAddress('tushar.rajput@reportsanddata.com', 'Tushar');
				$mailNew->isHTML(true);
				$mailNew->Subject  = $subject;
				$mailNew->Body     = $bodySales;
				if(!$mailNew->send()){
					//echo $mailNew->ErrorInfo; exit;
				  mail($from, $subject, $bodySales, $headers);
				}
			*/
			$response->session()->flash('ForgotEmail', trans('auth.ForgotEmail'));
			return Redirect::to('/forgot-password');
			
		}else{
			$response->session()->flash('InvalidEmail', trans('auth.InvalidEmail'));
			return Redirect::to('/forgot-password');
		}	
	}
	public function getRandomCode(){
		$string = '';
		$random_string_length=4;
		$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		for ($i = 0; $i < $random_string_length; $i++) {
		  $string .= $characters[rand(0, strlen($characters) - 1)];
		}
		$string = $string.date('dmyhis');
		return $string;
	}
	public function login_data(request $response){
		$Email = $response->Email;
		$Password = $response->Password;
		$dataLogin= array(
				'email_id'=>$Email,
				'password'=>$response->Password,
			);
		$List = $this->Login->checkLoginDetails('users', $dataLogin);
		//print_r($List); exit;
		if($List->count() > 0){
			if($List[0]->is_deleted==1){
				$response->session()->flash('AccountDeleted', trans('auth.AccountDeleted'));
				return Redirect::to('/login');
			}
			if($List[0]->status==0){
				$response->session()->flash('DeActiveAccount', trans('auth.AccountDeleted'));
				return Redirect::to('/login');
			}
			$SessionData = $List[0];
			//print_r($SessionData); 
			//echo $SessionData->company_id;
			//exit;
			$response->session()->put('user', $SessionData);
			
			return Redirect::to('/dashboard');
		}else{
			$response->session()->flash('InvalidLoginDetails', trans('auth.InvalidLoginDetails'));
			return Redirect::to('/login');
		}
	}
	public function set_company_id(request $response){
		$CompanyId = $response->DefaultCompanyForAdmin;
		$response->session()->put('company_session_id', $CompanyId);
		if($CompanyId==4){
			return Redirect::to('/dashboard_email_marketing');
		}else{
			return Redirect::to('/dashboard');
		}
		
		//return back()->withInput();
	}
	public function update_profile(request $response){
		$Email = $response->Email;
		$RegionIds = '';
		if(!empty($response->Region))
		$RegionIds = implode(",",$response->Region);
		$Active=0;
		if($response->Status)
			$Active=1;
		$dataArr= array(
			'user_name'=>$response->Username,
			'role_id'=>$response->Role,
			'mobile_no'=>$response->MobileNo,
			'user_address'=>$response->UserAddress,
			'status'=>$Active,
		);
		$this->Common->updateData('users', 'email_id', $Email, $dataArr);
		$List = $this->Common->getList('users', 'email_id', $Email);
		$SessionData = $List[0];
		$response->session()->put('user', $SessionData);
		$response->session()->flash('UpdateProfile', trans('auth.UpdateProfile'));
		return Redirect::to('/profile');
	}
	public function register_data(request $response){
		$Email = $response->Email;
		$List = $this->Common->getList('users', 'email_id', $Email);
		if($List->count() > 0){
			$response->session()->flash('EmailExistMsg', trans('auth.VaildEmailError'));
			return Redirect::to('/register');
		}else{
			$RegionIds = '';
			if(!empty($response->Region))
			$RegionIds = implode(",",$response->Region);
			$data= array(
				'user_name'=>$response->Username,
				'email_id'=>$Email,
				'password'=>$response->Password,
				'department_id'=>$response->Department,
				'designation_id'=>$response->Designation,
				'category_id'=>$response->Category,
				'role_id'=>$response->Role,
				'region_code'=>$RegionIds,
				'country_code'=>$response->Country,
				'company_id'=>$response->Company,
				'mobile_no'=>$response->MobileNo,
				'user_address'=>$response->UserAddress,
			);
			$savedata =$this->Common->saveData('users', $data);
			$response->session()->flash('RegistrationSucceessMsg', trans('auth.RegistrationSucceessMsg'));
			return Redirect::to('/register');
		}
		
	}
	public function forgot_password()
    {
		$data['title']= 'Forgot Password';
		return view('admin/auth/forgot_password',$data);
    }
	public function submit_reset_password(request $response){
		$code = $response->Code;
		$Email = $response->Email;
		$dataArr['password'] = $response->Password;
		$List = $this->Common->getList('users', 'forgot_code', $code);
		if($List->count() > 0){
			$MainEmail = $List[0]->email_id;
			if($List[0]->is_reset_password==1){
				$response->session()->flash('ExpirePasswordLink', trans('auth.ExpirePasswordLink'));
				return Redirect::to('/reset_password');
			}else{
				if($Email==$MainEmail){
					$this->Common->updateData('users', 'email_id', $Email, $dataArr);
					$response->session()->flash('ResetPassword', trans('auth.ResetPassword'));
					return Redirect::to('/login');
				}else{
					$response->session()->flash('CorrectEmail', trans('auth.CorrectEmail'));
					return Redirect::to('/reset_password');
				}
			}
			
			
		}else{
			$response->session()->flash('InvalidData', trans('auth.InvalidData'));
			return Redirect::to('/reset_password');
		}
		
	}
	public function reset_password(request $response)
    {
		$code = $data['Code'] = $response->code;
		$List = $this->Common->getList('users', 'forgot_code', $code);
		if($List->count() == 0){
			$response->session()->flash('InvalidData', trans('auth.InvalidData'));
			return Redirect::to('/login');
		}
		$data['title']= 'Reset Password';
		return view('admin/auth/reset_password',$data);
    }
	public function profile(request $response){
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$data['Departmentlist']=$this->Common->getList('department');
		$data['User'] = $response->session()->get('user');
		$data['RoleList']=$this->Common->getRoleList('roles');
		$data['title']= 'Reset Password';
		return view('admin/auth/profile',$data);
	}
	public function submit_change_profile(request $response){
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$Email = $response->Email;
		$dataArr= array(
			'password'=>$response->Password,
		);
		$this->Common->updateData('users', 'email_id', $Email, $dataArr);
		$List = $this->Common->getList('users', 'email_id', $Email);
		$SessionData = $List[0];
		$response->session()->put('user', $SessionData);
		$response->session()->flash('ChangePassword', trans('auth.ChangePassword'));
		return Redirect::to('/change_password');
	}
	public function changePassword(request $response){
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$data['User'] = $response->session()->get('user');
		$data['title']= 'Change Password';
		return view('admin/auth/change_password',$data);
	}
	public function user_management(request $response){
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$data['User'] = $response->session()->get('user');
		$data['title']= 'User Managemnt';
		if($response->session()->get('company_session_id')==4){
			return view('admin/access_management/email_marketing_user_management',$data);
		}else{
			return view('admin/access_management/user_management',$data);
		}
		
	}
	
	public function user_list(request $response) {
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
			$LoginUser = $response->session()->get('user');
			$ResultCount = $this->Login->user_list(($response->search)?$response->search:"");
			$total = $ResultCount->count();
			$data = array();
			$Result = $this->Login->user_list(($response->search)?$response->search:"", $response->start, $response->length );
			
			if ($Result->count() > 0) {
				foreach ($Result as $row) {
					$Id = $row->user_id;
					$row_data['status'] = 'De-active';
					if($row->status==1)
						$row_data['status'] = 'Active';
					$row_data['user_id'] = $Id;
					$row_data['user_name'] = $row->user_name;
					$row_data['email_id'] = $row->email_id.'/'. $row->mobile_no;
					$row_data['created_date'] = date('d-m-Y',strtotime($row->created_date));
					$row_data['role_name'] = $row->role_name;
					$row_data['Address'] = "Father's Name: ".$row->father_first_name.' '.$row->father_last_name.'<br> Address: '.$row->user_address;
					$row_data['details'] = 'Age: '.$row->age.' <br> DOB: '.date('d-m-Y',strtotime($row->birth_date)).'<br> Event Palce: '.$row->event_place.'<br>City: '.$row->city.' <br> Coach Name: '.$row->coach_name;
					//$url = 'http://localhost/marketysers_crm/edit_user/'.$Id;
					$url = url('edit_user/'.$Id);
					$Edit = '';
					if(Helper::allow_permission('edit-user-management')==1)
						$Edit = '<a href="'.$url.'" class="EditRoom "  title="Edit"><i class="fa fa-edit"></i></a> ';
					
					$Delete = '';
					//if(Helper::allow_permission('delete-user-management')==1)
					//	$Delete = '- <a href="javascript:void(0);" class="DeleteRoom " onclick="return deleteData('.$Id.');" title="Delete"><i class="fa fa-trash"></i></a> ';
					$row_data['action'] = $Edit.$Delete;
					$data[] = $row_data;
				}
				
			}
			//$datas = array_map('array_values', $data);
			$DataArray = array('draw'=>$_REQUEST['draw'],'recordsTotal'=>$total ,'recordsFiltered'=>$total, 'data'=> $data);
			echo json_encode($DataArray);
	}
	public function create_email_marketing_user(request $response){
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$Id = $response->id;
		$data['Departmentlist']=$this->Common->getList('department');
		$data['CompanyList']=$this->Common->getList('companies');
		$data['RoleList']=$this->Common->getRoleList('roles');
		$data['RegionList']=$this->Common->getList('regions');
		$data['User'] = $LoginUser = $response->session()->get('user');
		//print_r($data['User']); echo $data['User'][0]->department_id; exit;
		$data['Countrylist']=$this->Common->getList('countries', 'continent_code', $data['User']->region_code);
		$ArrData=array(
		'parent_id'=>0,
		'status'=>'active',
		'company_id'=>$response->session()->get('company_session_id'),
		);
		$data['CategoryList']=$this->Common->getData('category', $ArrData);
		$List = $this->Common->get_menu_list();
		$Permissions = array();
		foreach($List as $key => $val){
			$SubPermissionArr = array(
				'status'=>1,
				'menu_id'=>$val->id,
			);
			$ArrSub = array();
			$SubPermission = $this->Common->getPermissionMenuWise($SubPermissionArr);
			if(!empty($SubPermission)){
				foreach($SubPermission as $keys => $vals){
					$SubPermissionStatus=0;
					
					$ArrSub[] = array(
						'display_name'=>$vals->display_name,
						'id'=>$vals->id,
						'status'=>$SubPermissionStatus
					);
				}
			}
			
			$Permissions[] = array(
				'menu_name'=>$val->menu_name,
				'id'=>$val->id,
				'sub_permissions'=>$ArrSub
			);
			
		}
		//echo '<pre>'; print_r($Permissions); exit;
		$data['Permissions']=$Permissions;
		$data['title']= 'Create User';
		return view('admin/access_management/create_email_marketing_user',$data);
	}
	public function submit_email_marketing_user(request $response){
		//print_r($response->Region); exit;
		//$RegionIds = implode(",",$response->Region);
		$Email = $response->Email;
		$List = $this->Common->getList('users', 'email_id', $Email);
		if($List->count() > 0){
			$response->session()->flash('EmailExistMsg', trans('auth.VaildEmailError'));
			return Redirect::to('/user-management');
		}else{
			$Active=0;
			if($response->Status)
				$Active=1;
			$data= array(
				'user_name'=>$response->Username,
				'email_id'=>$Email,
				'password'=>$response->Password,
				'mobile_no'=>$response->MobileNo,
				'user_address'=>$response->UserAddress,
				//'department_id'=>$response->Department,
				//'designation_id'=>$response->Designation,
				//'category_id'=>$response->Category,
				'role_id'=>$response->Role,
				//'region_code'=>$RegionIds,
				//'country_code'=>$response->Country,
				'company_id'=>$response->CompanyId,
				'status'=>$Active,
			);
			$savedata =$this->Common->saveData('users', $data);
			$response->session()->flash('UserSucceessMsg', trans('auth.UserSucceessMsg'));
			return Redirect::to('/user-management');
		}
		
	}
	public function create_user(request $response){
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$Id = $response->id;
		$data['RoleList']=$this->Common->getRoleList('roles');
		$data['User'] = $LoginUser = $response->session()->get('user');
		$List = $this->Common->get_menu_list();
		$Permissions = array();
		foreach($List as $key => $val){
			$SubPermissionArr = array(
				'status'=>1,
				'menu_id'=>$val->id,
			);
			$ArrSub = array();
			$SubPermission = $this->Common->getPermissionMenuWise($SubPermissionArr);
			if(!empty($SubPermission)){
				foreach($SubPermission as $keys => $vals){
					$SubPermissionStatus=0;
					
					$ArrSub[] = array(
						'display_name'=>$vals->display_name,
						'key_name'=>$vals->name,
						'id'=>$vals->id,
						'status'=>$SubPermissionStatus
					);
				}
			}
			
			$Permissions[] = array(
				'menu_name'=>$val->menu_name,
				'id'=>$val->id,
				'sub_permissions'=>$ArrSub
			);
			
		}
		//echo '<pre>'; print_r($Permissions); exit;
		$data['Permissions']=$Permissions;
		$data['title']= 'Create User';
		return view('admin/access_management/create_user',$data);
	}
	public function edit_user(request $response){
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$Id = $response->id;
		
		$data['RoleList']=$this->Common->getRoleList('roles');
		$List = $this->Common->getList('users', 'id', $Id);
		$data['User'] = $List[0];
		
		$List = $this->Common->get_menu_list();
		$Permissions = array();
		foreach($List as $key => $val){
			$SubPermissionArr = array(
				'status'=>1,
				'menu_id'=>$val->id,
			);
			$ArrSub = array();
			$SubPermission = $this->Common->getPermissionMenuWise($SubPermissionArr);
			if(!empty($SubPermission)){
				foreach($SubPermission as $keys => $vals){
					$SubPermissionStatus=0;
					$List = $this->Common->getList('permission_user', 'user_id', $Id);
					if($List->count()==0){
						$PRoleArr = array(
							'role_id'=>$data['User']->role_id,
							'permission_id'=>$vals->id,
						);
						$PRoleRes = $this->Common->getData('permission_role', $PRoleArr);
						if($PRoleRes->count()>0){
							$SubPermissionStatus=1;
						}
					}
					
					$PUserArr = array(
						'user_id'=>$Id,
						'permission_id'=>$vals->id,
					);
					$PUserRes = $this->Common->getData('permission_user', $PUserArr);
					if($PUserRes->count()>0){
						$SubPermissionStatus=1;
					}
					$ArrSub[] = array(
						'display_name'=>$vals->display_name,
						'key_name'=>$vals->name,
						'id'=>$vals->id,
						'status'=>$SubPermissionStatus
					);
				}
			}
			
			$Permissions[] = array(
				'menu_name'=>$val->menu_name,
				'id'=>$val->id,
				'sub_permissions'=>$ArrSub
			);
			
		}
		//echo '<pre>'; print_r($Permissions); exit;
		$data['Permissions']=$Permissions;
		$data['title']= 'Edit User';
		return view('admin/access_management/edit_user',$data);
	}
	public function deleteData(request $response){
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$id = $response->id;
		$table = $response->table;
		$dataArr= array(
			'is_deleted'=>1,
		);
		$this->Common->updateData('users', 'id', $id, $dataArr);
	}
	public function update_user(request $response){
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$hid_id = $response->hid_id;
		$Active=0;
		if($response->Status)
			$Active=1;
		$dataArr= array(
			'user_name'=>$response->Username,
			'role_id'=>$response->Role,
			'mobile_no'=>$response->MobileNo,
			'user_address'=>$response->UserAddress,
			'status'=>$Active,
		);
		$this->Common->updateData('users', 'id', $hid_id, $dataArr);
		$SubPermission = $response->SubPermission;
		$sub_permission_change = $response->sub_permission_change;
		if($sub_permission_change==1){
			if(!empty($SubPermission)){
				$this->Common->deleteData('permission_user', 'user_id', $hid_id);
				foreach($SubPermission as $val){
					$ArrOptional = array(
						'permission_id'=>$val,
						'user_id'=>$hid_id,
					);
					$this->Common->saveData('permission_user', $ArrOptional);
				}
			}	
		}
		//exit;
		$response->session()->flash('UserUpdate', trans('auth.UserUpdate'));
		return Redirect::to('/user-management');
	}
	public function submit_user(request $response){
		//print_r($response->Region); exit;
		$Email = $response->Email;
		$List = $this->Common->getList('users', 'email_id', $Email);
		if($List->count() > 0){
			$response->session()->flash('EmailExistMsg', trans('auth.VaildEmailError'));
			return Redirect::to('/user-management');
		}else{
			$Active=0;
			if($response->Status)
				$Active=1;
			$data= array(
				'user_name'=>$response->Username,
				'email_id'=>$Email,
				'mobile_no'=>$response->MobileNo,
				'user_address'=>$response->UserAddress,
				'password'=>$response->Password,
				'role_id'=>$response->Role,
				'status'=>$Active,
			);
			$savedata =$this->Common->saveData('users', $data);
			$SubPermission = $response->SubPermission;
			//print_r($SubPermission); exit;
			if(!empty($SubPermission)){
				foreach($SubPermission as $val){
					$ArrOptional = array(
						'permission_id'=>$val,
						'user_id'=>$savedata,
					);
					$this->Common->saveData('permission_user', $ArrOptional);
				}
			}	
			$response->session()->flash('UserSucceessMsg', trans('auth.UserSucceessMsg'));
			return Redirect::to('/user-management');
		}
		
	}
	/********************Permission**********************/
	public function permission(){
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$data['title']= 'Permision';
		return view('admin/permissions/permission',$data);
	}
	public function create_permission(){
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$data['title']= 'Create Permision';
		return view('admin/permissions/create_permission',$data);
	}
	public function update_permissions(request $response){
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$data['id']=$response->id;
		$List = $this->Common->getList('menu', 'id', $response->id);
		$data['Permission'] = $List[0];
		$data['SubPermission'] = $this->Common->getList('permissions', 'menu_id', $response->id);
		$data['title']= 'Update Permision';
		return view('admin/permissions/update_permissions',$data);
	}
	public function edit_permission(request $response){
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$data['id']=$response->id;
		$List = $this->Common->getList('menu', 'id', $response->id);
		$data['Permission'] = $List[0];
		$MenuLower = strtolower($List[0]->menu_name);
		$data['MenuKeyword'] = str_replace(" ","-",$MenuLower);
		$DataDefaultPermissionWhere = array(
			'optional_permission'=>0,
			'menu_id'=>$response->id,
		);
		$DefaultPermissionArr = $this->Common->getData('permissions', $DataDefaultPermissionWhere);
		$DefaultPermission=array();
		foreach($DefaultPermissionArr as $key => $val){
			$DefaultPermission[$val->name] = $val->status;
		}
		$data['DefaultPermission']=$DefaultPermission;
		
		$DataOptionalPermissionWhere = array(
			'optional_permission'=>1,
			'menu_id'=>$response->id,
		);
		$OptionalPerArr = $this->Common->getData('permissions', $DataOptionalPermissionWhere);
		$OptionalPermission='';
		foreach($OptionalPerArr as $key => $val){
			$OptionalPermission .=$val->display_name.',';
		}
		$data['OptionalPermission'] = rtrim($OptionalPermission,',');
		$data['title']= 'Edit Permision';
		return view('admin/permissions/create_permission',$data);
	}
	public function get_permission_list(request $response) {
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
			$LoginUser = $response->session()->get('user');
			$ResultCount = $this->Common->get_permission_list($response->session()->get('company_session_id'), ($response->search)?$response->search:"");
			$total = $ResultCount->count();
			$data = array();
			$Result = $this->Common->get_permission_list($response->session()->get('company_session_id'), ($response->search)?$response->search:"", ($response->start)?$response->start:"", ($response->length)?$response->length:"" );
			
			if ($Result->count() > 0) {
				foreach ($Result as $row) {
					$Status='De-active';
					if($row->status==1)
						$Status='Active';
					$Id = $row->id;
					$row_data['id'] = $Id;
					$row_data['menu'] = $row->menu_name;
					$row_data['status'] = $Status;
					$row_data['created_date'] = date('d-m-Y',strtotime($row->created_date));
					$row_data['sort_order'] = $row->sort_order;
					$row_data['created_by'] = $row->user_name;
					//$url = 'http://localhost/marketysers_crm/edit_permission/'.$Id;
					$url = url('edit_permission/'.$Id);
					//$url_sub_permission = 'http://localhost/marketysers_crm/update_permissions/'.$Id;
					$url_sub_permission = url('update_permissions/'.$Id);
					$row_data['action'] = '<a href="'.$url.'" class="EditRoom "  title="Edit"><i class="fa fa-edit"></i></a> - <a href="javascript:void(0);" class="DeleteRoom " onclick="return delete_permission('.$Id.');" title="Delete"><i class="fa fa-trash"></i></a> - <a href="'.$url_sub_permission.'" title="Update Status & Sort Order Permission"><i class="fa fa-refresh"></i></a> ';
					$data[] = $row_data;
				}
				
			}
			//$datas = array_map('array_values', $data);
			$DataArray = array('draw'=>$_REQUEST['draw'],'recordsTotal'=>$total ,'recordsFiltered'=>$total, 'data'=> $data);
			echo json_encode($DataArray);
	}
	public function delete_permission(request $response){
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$Id = $response->Id;
		$this->Common->deleteData('menu', 'id', $Id);
		$this->Common->deleteData('permissions', 'menu_id', $Id);
		
	}
	public function update_permission_data(request $response){
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$Id = $response->Id;
		$Status = $response->Status;
		$SortOrder = $response->SortOrder;
		 $Arr = array(
			'status'=>$Status,
			'sort_order'=>$SortOrder,
		 );
		$this->Common->updateData('permissions', 'id', $Id, $Arr);
	}
	public function submit_permission(request $response){
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$LoginUser = $response->session()->get('user');
		$hid_id = $response->hid_id;
		$Menu = $response->Menu;
		$MenuLower = strtolower($Menu);
		$MenuKeyword = str_replace(" ","-",$MenuLower);
		$Active=0;
		if($response->Status)
			$Active=1;
		if($hid_id==''){
			$List = $this->Common->getList('menu', 'menu_name', $response->Menu);
			if($List->count() > 0){
				$response->session()->flash('PermissionAlreadyExist', 'Permission already exist. Please add new permssion name.');
				return Redirect::to('/permission');
			}
			$dataArr= array(
				'menu_name'=>$response->Menu,
				'sort_order'=>($response->SortOrder)?$response->SortOrder:0,
				'created_by'=>$LoginUser->id,
				'status'=>$Active,
			);
			$MenuId=0;
			$MenuId =$this->Common->saveData('menu', $dataArr);
			$Create = ($response->Create)? 1: 0;
			$Edit = ($response->Edit)? 1: 0;
			$DeleteP = ($response->DeleteP)? 1: 0;
			$ViewList = ($response->ViewList)? 1: 0;
			$ViewMenu = ($response->ViewMenu)? 1: 0;
			$ViewDetails = ($response->ViewDetails)? 1: 0;
			
			$ArrCreate = array(
				'status'=>$Create,
				'name'=>'create-'.$MenuKeyword,
				'display_name'=>'Create '.$Menu,
				'menu_id'=>$MenuId,
				'created_by'=>$LoginUser->id,
			);
			$this->Common->saveData('permissions', $ArrCreate);
			$ArrEdit = array(
				'status'=>$Edit,
				'name'=>'edit-'.$MenuKeyword,
				'display_name'=>'Edit '.$Menu,
				'menu_id'=>$MenuId,
				'created_by'=>$LoginUser->id,
			);
			$this->Common->saveData('permissions', $ArrEdit);
			$ArrDelete = array(
				'status'=>$DeleteP,
				'name'=>'delete-'.$MenuKeyword,
				'display_name'=>'Delete '.$Menu,
				'menu_id'=>$MenuId,
				'created_by'=>$LoginUser->id,
			);
			$this->Common->saveData('permissions', $ArrDelete);
			$ArrViewList = array(
				'status'=>$ViewList,
				'name'=>'view-'.$MenuKeyword.'-list',
				'display_name'=>'View '.$Menu.' List',
				'menu_id'=>$MenuId,
				'created_by'=>$LoginUser->id,
			);
			$this->Common->saveData('permissions', $ArrViewList);
			$ArrViewMenu = array(
				'status'=>$ViewMenu,
				'name'=>'view-'.$MenuKeyword.'-menu',
				'display_name'=>'View '.$Menu.' Menu',
				'menu_id'=>$MenuId,
				'created_by'=>$LoginUser->id,
			);
			$this->Common->saveData('permissions', $ArrViewMenu);
			$ArrViewDetails = array(
				'status'=>$ViewDetails,
				'name'=>'view-'.$MenuKeyword.'-details',
				'display_name'=>'View '.$Menu.' Details',
				'menu_id'=>$MenuId,
				'created_by'=>$LoginUser->id,
			);
			$this->Common->saveData('permissions', $ArrViewDetails);
			$OptionalPermission = $response->OptionalPermission;
			if($OptionalPermission!=''){
				$OptionalPerArr = explode(",",$OptionalPermission);
				if(!empty($OptionalPerArr)){
					foreach($OptionalPerArr as $val){
						$ValKeyword = str_replace(" ","-",$val);
						$ArrOptional = array(
							'optional_permission'=>1,
							'status'=>1,
							'name'=>strtolower($ValKeyword),
							'display_name'=>$val,
							'menu_id'=>$MenuId,
							'created_by'=>$LoginUser->id,
						);
						$this->Common->saveData('permissions', $ArrOptional);
					}
				}
			}
			$response->session()->flash('PermissionAdded', 'Permission Created');
			return Redirect::to('/permission');
		}else{
			$MenuId = $hid_id;
			$dataArr= array(
				'sort_order'=>($response->SortOrder)?$response->SortOrder:0,
				'status'=>$Active,
			);
			$this->Common->updateData('menu', 'id', $hid_id, $dataArr);
			
			
			$Create = ($response->Create)? 1: 0;
			$Edit = ($response->Edit)? 1: 0;
			$DeleteP = ($response->DeleteP)? 1: 0;
			$ViewList = ($response->ViewList)? 1: 0;
			$ViewMenu = ($response->ViewMenu)? 1: 0;
			$ViewDetails = ($response->ViewDetails)? 1: 0;
			
			$List = $this->Common->getList('permissions', 'name', 'create-'.$MenuKeyword);
			if($List->count() > 0){
				$ArrCreate = array(
					'status'=>$Create,
				);
				$this->Common->updateData('permissions', 'name', 'create-'.$MenuKeyword, $ArrCreate);
			}
			
			$List = $this->Common->getList('permissions', 'name', 'edit-'.$MenuKeyword);
			if($List->count() > 0){
				$ArrEdit = array(
					'status'=>$Edit,
				);
				$this->Common->updateData('permissions', 'name', 'edit-'.$MenuKeyword, $ArrEdit);
			}
			
			$List = $this->Common->getList('permissions', 'name', 'delete-'.$MenuKeyword);
			if($List->count() > 0){
				$ArrDelete = array(
					'status'=>$DeleteP,
				);
				$this->Common->updateData('permissions', 'name', 'delete-'.$MenuKeyword, $ArrDelete);
			}
			
			$List = $this->Common->getList('permissions', 'name', 'view-'.$MenuKeyword.'-list');
			if($List->count() > 0){
				$ArrViewList = array(
					'status'=>$ViewList,
				);
				$this->Common->updateData('permissions', 'name', 'view-'.$MenuKeyword.'-list', $ArrViewList);
			}
			$List = $this->Common->getList('permissions', 'name', 'view-'.$MenuKeyword.'-menu');
			if($List->count() > 0){
				$ArrViewMenu = array(
					'status'=>$ViewMenu,
				);
				$this->Common->updateData('permissions', 'name', 'view-'.$MenuKeyword.'-menu', $ArrViewMenu);
			}
			$List = $this->Common->getList('permissions', 'name', 'view-'.$MenuKeyword.'-details');
			if($List->count() > 0){
				$ArrViewDetails = array(
					'status'=>$ViewDetails,
				);
				$this->Common->updateData('permissions', 'name', 'view-'.$MenuKeyword.'-details', $ArrViewDetails);
			}
			
			$OptionalPermission = $response->OptionalPermission;
			if($OptionalPermission!=''){
				$OptionalPerArr = explode(",",$OptionalPermission);
				if(!empty($OptionalPerArr)){
					foreach($OptionalPerArr as $val){
						$ValKeyword = str_replace(" ","-",$val);
						$Keyword = strtolower($ValKeyword);
						$List = $this->Common->getList('permissions', 'name', $Keyword);
						if($List->count() == 0){
							$ArrOptional = array(
								'optional_permission'=>1,
								'status'=>1,
								'name'=>$Keyword,
								'display_name'=>$val,
								'menu_id'=>$MenuId,
								'created_by'=>$LoginUser->id,
							);
							$this->Common->saveData('permissions', $ArrOptional);
						}
						
					}
				}
			}
			
			$response->session()->flash('PermissionUpdated', 'Permission Updated');
			return Redirect::to('/permission');
		}
		//echo $Active; exit;
		
	}
	/********************End Permission**********************/
	/********************Role Permission**********************/
	public function roles(){
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$data['title']= 'Role';
		return view('admin/permissions/roles',$data);
	}
	public function create_role(){
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$data['title']= 'Create Role';
		$List = $this->Common->get_menu_list();
		$Permissions = array();
		foreach($List as $key => $val){
			$SubPermissionArr = array(
				'status'=>1,
				'menu_id'=>$val->id,
			);
			$ArrSub = array();
			$SubPermission = $this->Common->getPermissionMenuWise($SubPermissionArr);
			if(!empty($SubPermission)){
				foreach($SubPermission as $keys => $vals){
					$ArrSub[] = array(
						'display_name'=>$vals->display_name,
						'id'=>$vals->id,
						'status'=>0
					);
				}
			}
			$Permissions[] = array(
				'menu_name'=>$val->menu_name,
				'id'=>$val->id,
				'sub_permissions'=>$ArrSub,
				
			);
			
		}
		$data['Permissions']=$Permissions;
		return view('admin/permissions/create_role',$data);
	}
	public function edit_role(request $response){
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$data['id']=$response->id;
		$List = $this->Common->getList('roles', 'id', $response->id);
		$data['Role'] = $List[0];
		$data['title']= 'Edit Role';
		$List = $this->Common->get_menu_list();
		$Permissions = array();
		foreach($List as $key => $val){
			$SubPermissionArr = array(
				'status'=>1,
				'menu_id'=>$val->id,
			);
			$ArrSub = array();
			$SubPermission = $this->Common->getPermissionMenuWise($SubPermissionArr);
			if(!empty($SubPermission)){
				foreach($SubPermission as $keys => $vals){
					$SubPermissionStatus=0;
					$PRoleArr = array(
						'role_id'=>$response->id,
						'permission_id'=>$vals->id,
					);
					$PRoleArr = $this->Common->getData('permission_role', $PRoleArr);
					if($PRoleArr->count()>0){
						$SubPermissionStatus=1;
					}
					$ArrSub[] = array(
						'display_name'=>$vals->display_name,
						'id'=>$vals->id,
						'status'=>$SubPermissionStatus
					);
				}
			}
			
			$Permissions[] = array(
				'menu_name'=>$val->menu_name,
				'id'=>$val->id,
				'sub_permissions'=>$ArrSub
			);
			
		}
		//echo '<pre>'; print_r($Permissions); exit;
		$data['Permissions']=$Permissions;
		return view('admin/permissions/create_role',$data);
	}
	public function get_role_list(request $response) {
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
			$LoginUser = $response->session()->get('user');
			$ResultCount = $this->Common->get_role_list($response->session()->get('company_session_id'), ($response->search)?$response->search:"");
			$total = $ResultCount->count();
			$data = array();
			$Result = $this->Common->get_role_list($response->session()->get('company_session_id'), ($response->search)?$response->search:"", ($response->start)?$response->start:"", ($response->length)?$response->length:"" );
			
			if ($Result->count() > 0) {
				foreach ($Result as $row) {
					$Status='De-active';
					if($row->status==1)
						$Status='Active';
					$Id = $row->id;
					$row_data['id'] = $Id;
					$row_data['name'] = $row->name;
					$row_data['status'] = $Status;
					$row_data['created_date'] = date('d-m-Y',strtotime($row->created_at));
					$row_data['sort_order'] = $row->sort;
					//$url = 'http://localhost/marketysers_crm/edit_role/'.$Id;
					$url = url('edit_role/'.$Id);
					$Edit = '';
					if(Helper::allow_permission('edit-role-management')==1)
						$Edit = '<a href="'.$url.'" class="EditRoom "  title="Edit"><i class="fa fa-edit"></i></a> ';
					$row_data['action'] = $Edit;
					$data[] = $row_data;
				}
				
			}
			//$datas = array_map('array_values', $data);
			$DataArray = array('draw'=>$_REQUEST['draw'],'recordsTotal'=>$total ,'recordsFiltered'=>$total, 'data'=> $data);
			echo json_encode($DataArray);
	}
	public function submit_role(request $response){
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$LoginUser = $response->session()->get('user');
		$hid_id = $response->hid_id;
		$previous_associated_permission = $response->previous_associated_permission;
		$Role = $response->Role;
		$SubPermission = $response->SubPermission;
		
		$Active=0;
		if($response->Status)
			$Active=1;
		if($hid_id==''){
			$List = $this->Common->getList('roles', 'name', $response->Role);
			if($List->count() > 0){
				$response->session()->flash('RoleAlreadyExist', 'Role already exist. Please add new role name.');
				return Redirect::to('/roles');
			}
			$dataArr= array(
				'name'=>$response->Role,
				'all'=>$response->AssociatedPermission,
				'sort'=>($response->SortOrder)?$response->SortOrder:0,
				'created_by'=>$LoginUser->id,
				'status'=>$Active,
			);
			
			$RoleId =$this->Common->saveData('roles', $dataArr);
			
			if($response->AssociatedPermission==0){
				if(!empty($SubPermission)){
					foreach($SubPermission as $val){
						$ArrOptional = array(
							'permission_id'=>$val,
							'role_id'=>$RoleId,
						);
						$this->Common->saveData('permission_role', $ArrOptional);
					}
				}
			}
			
			$response->session()->flash('RoleAdded', 'Added Permissions to a role.');
			return Redirect::to('/roles');
		}else{
			$RoleId = $hid_id;
			$dataArr= array(
				'name'=>$response->Role,
				'all'=>$response->AssociatedPermission,
				'sort'=>($response->SortOrder)?$response->SortOrder:0,
				'status'=>$Active,
			);
			$this->Common->updateData('roles', 'id', $hid_id, $dataArr);
			if($previous_associated_permission!=$response->AssociatedPermission){
				if($previous_associated_permission==0){
					$this->Common->deleteData('permission_role', 'role_id', $RoleId);
				}
			}
			if($response->AssociatedPermission==0 && $response->sub_permission_change==1){
				$this->Common->deleteData('permission_role', 'role_id', $RoleId);
				if(!empty($SubPermission)){
					foreach($SubPermission as $val){
						$ArrOptional = array(
							'permission_id'=>$val,
							'role_id'=>$RoleId,
						);
						$this->Common->saveData('permission_role', $ArrOptional);
					}
				}
			}
			
			$response->session()->flash('RoleUpdated', 'Updated Permissions to a role.');
			return Redirect::to('/roles');
		}
		//echo $Active; exit;
		
	}
	
	/********************End Role Permission**********************/
	/********************Start Employee autocomplete**********************/
	public function search_employee(){
		$data=array();
		if(isset($_GET['name_startsWith']))
		{
			$user_name = trim($_GET['name_startsWith']);
			$rows = $this->Common->emplyee_autosearch('users',$user_name);
			foreach($rows as $row){
				$id= $row->id;
				$rowTitle = $row->user_name;
				array_push($data, array("label"=>"$rowTitle" ,"id"=>"$id"));
			}
			echo json_encode($data);
		}
	}
	/********************End Employee autocomplete**********************/
}
