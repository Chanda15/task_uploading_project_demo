<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Validator;
use App\Models\Common;
use App\Models\Login;
use Helper;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class EventController extends Controller
{
	
	protected $Common;
	protected $Login;
    public function __construct(Common $Common,Login $Login)
    {
		$this->Common = $Common;
		$this->Login = $Login;
		
    }
	/********************************************************
	Backend
	*********************************************************/
	public function event_listing(request $response)
    {
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$data['title']= 'Event';
		return view('admin/event/index',$data);
    }
	public function create_event(request $response)
    {
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$data['title']= 'Create Event';
		return view('admin/event/create_event',$data);
    }

	public function submit_event(request $response){
		$data['User'] = $response->session()->get('user');
		$EventId= $response->EventId;
		$title= $response->title;
		$ReportStatus = 0;
		if($response->Status)
			$ReportStatus = 1;
		
		$dataArr= array(
			'title'=>$title,
			'event_date'=>date('Y-m-d', strtotime($response->event_date)),
			'short_keyword'=>$response->ShortCode,
			'description'=>$response->desc,
			'status'=>$ReportStatus,
			'meta_title'=>$response->metatitle,
			'meta_keywords'=>$response->metakeywords,
			'meta_description'=>$response->metadesc,
		);
		if ($response->hasFile('Image')) {
			$image = $response->file('Image');
			$name = $image->getClientOriginalName();
			$destinationPath = 'assets/event';
			$image->move($destinationPath, $name);
			$dataArr['image']=$name;
			if($response->HiddenImage!=''){
				$filename = 'assets/event/'.$response->HiddenImage;
				if (file_exists($filename)){
				   @unlink($filename);
				}
			}
		}
		
		
		if($EventId==''){
			
			$Result = $this->Common->check_url('events', 'title', $title);
			if(count($Result)>0){
				$response->session()->flash('ReportMessage', 'Event is already exist');
				return Redirect::to('/event_listing');
			}
			$dataArr['created_by']=$data['User']->id;
			$dataArr['updated_by']=$data['User']->id;
			$EventId = $this->Common->saveData('events', $dataArr);
			
			$response->session()->flash('ReportMessage', 'Event Added');
				return Redirect::to('/event_listing');
		}else{
			
			$Result = $this->Common->check_url('events', 'title', $title, $EventId);
			if(count($Result)>0){
				$response->session()->flash('ReportMessage', 'Event is already exist');
				return Redirect::to('/event_listing');
			}
			$dataArr['updated_by']=$data['User']->id;
			$this->Common->updateData('events', 'id', $EventId, $dataArr);
			$response->session()->flash('ReportMessage', 'Event Updated');
			return Redirect::to('/event_listing');
		}
	}
	function seoUrl($str, $replace=array(), $delimiter='-') 
	{	
		if(strlen($str) > 225) {
			$str2 = trim(substr($str,-10));
			$str = trim(substr($str,0,215));			
			$str = $str.'-'.$str2;			
		} else {			
			$str = trim(substr($str,0,225));
		}
		
		if( !empty($replace) ){	$str = str_replace((array)$replace, ' ', $str);	}
		$clean = $str;
		$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
		$clean = strtolower(trim($clean, '-'));
		$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
		return $clean;
	}
	public function event_list(request $response) {
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$LoginUser = $response->session()->get('user');
		$ResultCount = $this->Common->get_event_list(($response->search)?$response->search:"");
		$total = $ResultCount->count();
		$data = array();
		$Result = $this->Common->get_event_list(($response->search)?$response->search:"", $response->start, $response->length );
		$EditLink = 'edit_event/';
		
		
		if ($Result->count() > 0) {
			foreach ($Result as $row) {
				$Id = $row->id;
				$row_data['id'] = $Id;
				$row_data['title'] = $row->title;
				$row_data['short_keyword'] = $row->short_keyword;
				$edit_url = url($EditLink.$Id);
				//- <a target="new" href="'.$view_url.'"class="ViewData " title="View"><i class="fa fa-eye"></i></a>
				//$view_url = url('blog-category/'.$row->slug);
				$row_data['action'] = '<a href="'.$edit_url.'" class="EditData "  title="Edit"><i class="fa fa-edit"></i></a> ';
				$data[] = $row_data;
			}
			
		}
		//$datas = array_map('array_values', $data);
		$DataArray = array('draw'=>$_REQUEST['draw'],'recordsTotal'=>$total ,'recordsFiltered'=>$total, 'data'=> $data);
		echo json_encode($DataArray);
	}

	public function edit_event(request $response){
		$Id = $response->id;
		$ReportData = $this->Common->getList('events', 'id', $Id);
		$data['report_data'] = $ReportData[0];
		$data['title']= 'Edit Event';
		return view('admin/event/create_event',$data);
	}
	public function delete_data(request $response){
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/');
		  //the key does not exist in the session
		}
		$this->Common->deleteData($response->table_name, 'id', $response->Id);
		echo 'deleted';
	}
	public function assign_event(request $response)
    {
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$WhrData = array(
			'status'=>1,
			'role_id'=>2,
		);
		$data['UserList']=$this->Common->getData('users', $WhrData);
		$WhrData = array(
			'status'=>1,
		);
		$data['EventList']=$this->Common->getData('events', $WhrData);
		$data['title']= 'Event';
		return view('admin/event/assign_event',$data);
    }
	public function edit_assign_event(request $response){
		$Id = $response->Id;
		$ReportData = $this->Common->getList('assign_events', 'id', $Id);
		$data['report_data'] = $ReportData[0];
		echo json_encode($data['report_data']);
	}
	public function get_assign_event_list(request $response) {
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$Data = array();
		if(isset($response->certificate_request))
			$Data['certificate_request'] = $response->certificate_request;
		$LoginUser = $response->session()->get('user');
		$ResultCount = $this->Common->get_assign_event_list($Data, ($response->search)?$response->search:"");
		$total = $ResultCount->count();
		$data = array();
		$Result = $this->Common->get_assign_event_list($Data, ($response->search)?$response->search:"", $response->start, $response->length );
		$EditLink = 'edit_assign_event/';
		
		
		if ($Result->count() > 0) {
			foreach ($Result as $row) {
				$Id = $row->id;
				$row_data['id'] = $Id;
				$row_data['registration_number'] = $row->registration_number;
				$row_data['event_name'] = $row->event_name;
				$row_data['user_name'] = $row->user_name;
				$row_data['result'] = $row->result;
				$row_data['assign_date'] = date('d-m-Y', strtotime($row->assign_date));
				$edit_url = url($EditLink.$Id);
				//- <a target="new" href="'.$view_url.'"class="ViewData " title="View"><i class="fa fa-eye"></i></a>
				//$view_url = url('blog-category/'.$row->slug);
				$row_data['action'] = '<a href="javascript:void(0)" onclick="return edit_assign_event('.$Id.');" class="EditData "  title="Edit"><i class="fa fa-edit"></i></a> ';
				$data[] = $row_data;
			}
			
		}
		//$datas = array_map('array_values', $data);
		$DataArray = array('draw'=>$_REQUEST['draw'],'recordsTotal'=>$total ,'recordsFiltered'=>$total, 'data'=> $data);
		echo json_encode($DataArray);
	}
	public function submit_assign_event(request $response){
		$data['User'] = $response->session()->get('user');
		$AssignEventId= $response->AssignEventId;
		
		$dataArr= array(
			'assign_date'=>date('Y-m-d', strtotime($response->assign_date)),
			'event_id'=>$response->event_id,
			'user_id'=>$response->user_id,
		);
		
		if($AssignEventId==''){
			$LastRecords = $this->Common->getLastAssignEventRegNo();
			if(empty($LastRecords)){
			   $RegistrationNumber = 1;
			}else{
			   $RegistrationNumber = $LastRecords->registration_number+1;
			}
			$dataArr['registration_number'] = str_pad($RegistrationNumber, 4, '0', STR_PAD_LEFT);
			$dataArr['created_by']=$data['User']->id;
			$dataArr['updated_by']=$data['User']->id;
			$AssignEventId = $this->Common->saveData('assign_events', $dataArr);
			
			$response->session()->flash('ReportMessage', 'Event Added');
				return Redirect::to('/assign_event');
		}else{
			$dataArr['updated_by']=$data['User']->id;
			$this->Common->updateData('assign_events', 'id', $AssignEventId, $dataArr);
			$response->session()->flash('ReportMessage', 'Event Updated');
			return Redirect::to('/assign_event');
		}
	}
	public function get_order_request(request $response){
		$RegistrationNumber = $response->RegistrationNumber;
		$dataArr['order_request']=1;
		$this->Common->updateData('assign_events', 'registration_number', $RegistrationNumber, $dataArr);
	}
	public function get_result(request $response){
		$RegistrationNumber = $response->RegistrationNumber;
		$ResultDetails=$this->Common->getResultByRegId($RegistrationNumber);
		if($ResultDetails->count()>0){
		$res = $ResultDetails[0];
		$res->msg = 'success';
		//print_r($res);
		echo json_encode($res);
	}else{
		$res = (object) ['msg' => 'error'];
		echo json_encode($res);
	}
	}
	public function submit_result(request $response){
		$result_array = json_decode($response->result_array);
		//print_r($result_array);
		foreach($result_array as $key => $val){
			$dataArr['result']=$val;
			$this->Common->updateData('assign_events', 'id', $key, $dataArr);
		}
	}
	public function add_event_result(request $response)
    {
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$WhrData = array(
			'status'=>1,
			'role_id'=>2,
		);
		$data['UserList']=$this->Common->getData('users', $WhrData);
		$WhrData = array(
			'status'=>1,
		);
		$data['EventList']=$this->Common->getData('events', $WhrData);
		$data['AssignEventList'] = array();
		if($response->submit_result){
			$data['EventId']=$EventId = $response->Event;
			$data['UserId']=$UserId = $response->User;
			$WhrData = array(
				'event_id'=>$EventId,
				'user_id'=>$UserId,
			);
			$data['AssignEventList']=$this->Common->getAssignEventList(10, $WhrData);
			//print_r($data['AssignEventList']); exit;
		}
		
		$data['title']= 'Event';
		return view('admin/event/add_event_result',$data);
    }
	public function request_certificate(request $response)
    {
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$data['title']= 'Order Certificate';
		return view('admin/event/order_certificate',$data);
    }
	public function user_event(request $response)
    {
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$data['title']= 'User Event';
		return view('admin/event/user_event',$data);
    }
	public function get_user_event_list(request $response) {
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$Data = array();
		if(isset($response->certificate_request))
			$Data['certificate_request'] = $response->certificate_request;
		$LoginUser = $response->session()->get('user');
		$Data['UserId']=$LoginUser->id;
		$ResultCount = $this->Common->get_assign_event_list($Data, ($response->search)?$response->search:"");
		$total = $ResultCount->count();
		$data = array();
		$Result = $this->Common->get_assign_event_list($Data, ($response->search)?$response->search:"", $response->start, $response->length );
		$EditLink = 'edit_assign_event/';
		
		
		if ($Result->count() > 0) {
			foreach ($Result as $row) {
				$Id = $row->id;
				$row_data['id'] = $Id;
				$row_data['registration_number'] = $row->registration_number;
				$row_data['event_name'] = $row->event_name;
				$row_data['user_name'] = $row->user_name;
				$row_data['result'] = $row->result;
				$row_data['assign_date'] = date('d-m-Y', strtotime($row->assign_date));
				$edit_url = url($EditLink.$Id);
				//- <a target="new" href="'.$view_url.'"class="ViewData " title="View"><i class="fa fa-eye"></i></a>
				//$view_url = url('blog-category/'.$row->slug);
				//$row_data['action'] = '<a href="javascript:void(0)" onclick="return edit_assign_event('.$Id.');" class="EditData "  title="Edit"><i class="fa fa-edit"></i></a> ';
				$data[] = $row_data;
			}
			
		}
		//$datas = array_map('array_values', $data);
		$DataArray = array('draw'=>$_REQUEST['draw'],'recordsTotal'=>$total ,'recordsFiltered'=>$total, 'data'=> $data);
		echo json_encode($DataArray);
	}
}
