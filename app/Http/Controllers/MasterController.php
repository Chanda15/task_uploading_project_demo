<?php

namespace App\Http\Controllers\Auth;

namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\Common;
use App\Models\Login;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Models\Master;
use Helper;
class MasterController extends Controller
{
    protected $Common;
    protected $Login;
	protected $Master;
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
    public function __construct(Common $Common,Login $Login,Master $Master)
    {
		$this->Common = $Common;
		$this->Login = $Login;
		$this->Master = $Master;
        //$this->middleware('guest')->except('logout');
    }
	
	public function department(request $response)
    {
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$data['title']= 'Departments';
		return view('admin/master/department',$data);
    }
	public function submit_department(request $response){
		$DepartmentId= $response->DepartmentId;
		$dataArr= array(
			'name'=>$response->DepartmentName,
			'status'=>1,
			
		);
		if($DepartmentId==''){
			$Resut = $this->Common->getList('department', 'name', $response->DepartmentName);
			if(empty($Resut[0])){
				$this->Common->saveData('department', $dataArr);
				echo 'Added';
			}else{
				echo 'exist';
			}
			
		}else{
			$this->Common->updateData('department', 'id', $DepartmentId, $dataArr);
		}
	}
	public function get_department_list(request $response) {
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
			$LoginUser = $response->session()->get('user');
			$ResultCount = $this->Master->get_department_list(($response->search)?$response->search:"");
			$total = $ResultCount->count();
			$data = array();
			$Result = $this->Master->get_department_list(($response->search)?$response->search:"", ($response->start)?$response->start:"", ($response->length)?$response->length:"", );
			
			if ($Result->count() > 0) {
				foreach ($Result as $row) {
					$Id = $row->id;
					$row_data['id'] = $Id;
					$row_data['department_name'] = $row->name;
					$url = url('edit_department/'.$Id);
					$row_data['action'] = '<a href="javascript:void(0);" onclick="return edit_department('.$Id.');" class="EditRoom "  title="Edit"><i class="fa fa-edit"></i></a> - <a href="javascript:void(0);" class="DeleteRoom " onclick="return delete_department('.$Id.');" title="Delete"><i class="fa fa-trash"></i></a> ';
					$data[] = $row_data;
				}
				
			}
			//$datas = array_map('array_values', $data);
			$DataArray = array('draw'=>$_REQUEST['draw'],'recordsTotal'=>$total ,'recordsFiltered'=>$total, 'data'=> $data);
			echo json_encode($DataArray);
	}
	public function edit_department(request $response){
		$Id = $response->Id;
		$Resut = $this->Common->getList('department', 'id', $response->Id);
		echo json_encode($Resut[0]);
	}
	public function delete_data(request $response){
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$this->Common->deleteData($response->table_name, 'id', $response->Id);
		echo 'deleted';
	}
	public function importCategories(request $request){
		$CompanyId = $request->id;
		$client = new \GuzzleHttp\Client();
		if($CompanyId==1)
			$response = $client->request('GET', 'https://www.reportsanddata.com/import_categories', ['verify' => false]);
		if($CompanyId==2)
			$response = $client->request('GET', 'https://www.marketexpertz.com/import_categories', ['verify' => false]);
		if($CompanyId==3)
			$response = $client->request('GET', 'https://www.emergenresearch.com/import_categories', ['verify' => false]);
		
		$DecodeResponse = json_decode($response->getBody());
		//echo '<pre>';
		//print_r($DecodeResponse); exit;
		$input = array();
		foreach($DecodeResponse as $key => $val){
			foreach($val as $val_main){
				$ArrData = array(
					'company_id'=>$CompanyId,
					'main_cat_id'=>$val_main->id,
				);
				$input_array=array(
					//'id'=>$val_main->id,
					'name'=>$val_main->name,
					'slug'=>$val_main->slug,
					'image_path'=>$val_main->image_path,
					'category_icon'=>$val_main->category_icon,
					'cat_type'=>$val_main->cat_type,
					'parent_id'=>$val_main->parent_id,
					'parentnew'=>$val_main->parentnew,
					'is_popular'=>$val_main->is_popular,
					'title'=>$val_main->title,
					'metdakeyword'=>$val_main->metdakeyword,
					'metadata'=>$val_main->metadata,
					'status'=>$val_main->status,
					'catdesc'=>$val_main->catdesc,
					'company_id'=>$CompanyId,
					'main_cat_id'=>$val_main->id,
					'image'=>$val_main->image,
				);
				$ResultCat = $this->Common->getData('category', $ArrData);
				if($ResultCat->count()>0){
					$this->Common->updateCategoryData('category', $CompanyId, $val_main->id, $input_array);
					if(!empty($val_main->subcat)){
						$SubCategories = $val_main->subcat;
						foreach($SubCategories as $key_child => $val_child){
							//echo $val_main->name;
							//print_r($val_child);
							$ParentCatName = $val_child->parent_cat_name;
							$WhrChildData = array(
								'company_id'=>$CompanyId,
								'name'=>$ParentCatName,
							);
							$Resut = $this->Common->getData('category', $WhrChildData);
							$ParentId =0;
							foreach($Resut as $va_pcat){
								$ParentId = $va_pcat->id;
							}
							$input_shild_array=array(
								//'id'=>$val_child->id,
								'name'=>$val_child->name,
								'slug'=>$val_child->slug,
								'image_path'=>$val_child->image_path,
								'category_icon'=>$val_child->category_icon,
								'cat_type'=>$val_child->cat_type,
								'parent_id'=>$ParentId,
								'parentnew'=>$val_child->parentnew,
								'is_popular'=>$val_child->is_popular,
								'title'=>$val_child->title,
								'metdakeyword'=>$val_child->metdakeyword,
								'metadata'=>$val_child->metadata,
								'status'=>$val_child->status,
								'catdesc'=>$val_child->catdesc,
								'company_id'=>$CompanyId,
								'main_cat_id'=>$val_child->id,
								'image'=>$val_child->image,
							);
							$WhrChildExist = array(
								'company_id'=>$CompanyId,
								'main_cat_id'=>$val_child->id,
							);
							$ResultChildCat = $this->Common->getData('category', $WhrChildExist);
							if($ResultChildCat->count()>0){
								$this->Common->updateCategoryData('category', $CompanyId, $val_child->id, $input_shild_array);
							}else{
								$this->Common->saveData('category', $input_shild_array);
							}
						}
					}
				}else{
					$this->Common->saveData('category', $input_array);
					if(!empty($val_main->subcat)){
						$SubCategories = $val_main->subcat;
						foreach($SubCategories as $key_child => $val_child){
							//echo $val_main->name;
							//print_r($val_child);
							$ParentCatName = $val_child->parent_cat_name;
							$WhrChildData = array(
								'company_id'=>$CompanyId,
								'name'=>$ParentCatName,
							);
							$Resut = $this->Common->getData('category', $WhrChildData);
							$ParentId =0;
							foreach($Resut as $va_pcat){
								$ParentId = $va_pcat->id;
							}
							$input_shild_array=array(
								//'id'=>$val_child->id,
								'name'=>$val_child->name,
								'slug'=>$val_child->slug,
								'image_path'=>$val_child->image_path,
								'category_icon'=>$val_child->category_icon,
								'cat_type'=>$val_child->cat_type,
								'parent_id'=>$ParentId,
								'parentnew'=>$val_child->parentnew,
								'is_popular'=>$val_child->is_popular,
								'title'=>$val_child->title,
								'metdakeyword'=>$val_child->metdakeyword,
								'metadata'=>$val_child->metadata,
								'status'=>$val_child->status,
								'catdesc'=>$val_child->catdesc,
								'company_id'=>$CompanyId,
								'main_cat_id'=>$val_child->id,
								'image'=>$val_child->image,
							);
							$WhrChildExist = array(
								'company_id'=>$CompanyId,
								'main_cat_id'=>$val_child->id,
							);
							$ResultChildCat = $this->Common->getData('category', $WhrChildExist);
							if($ResultChildCat->count()>0){
								$this->Common->updateCategoryData('category', $CompanyId, $val_child->id, $input_shild_array);
							}else{
								$this->Common->saveData('category', $input_shild_array);
							}
						}
					}
				}
				
			}
		}
		echo '<h1>Category has been imported</h1>';
	}
	public function masterCategories(request $response)
    {
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$data['title']= 'Departments';
		return view('admin/master/import_categories',$data);
    }
	public function tags(request $response)
    {
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$data['title']= 'Tags';
		return view('admin/master/tags',$data);
    }
	public function get_tag_list(request $response) {
		if (Session::has('user')){
		  // do some thing if the key is exist
		}else{
			return Redirect::to('/login');
		  //the key does not exist in the session
		}
		$LoginUser = $response->session()->get('user');
		$ResultCount = $this->Master->get_tag_list(($response->search)?$response->search:"");
		$total = $ResultCount->count();
		$data = array();
		$Result = $this->Master->get_tag_list(($response->search)?$response->search:"", ($response->start)?$response->start:"", ($response->length)?$response->length:"", );
		
		if ($Result->count() > 0) {
			foreach ($Result as $row) {
				$Id = $row->id;
				$row_data['id'] = $Id;
				$row_data['tag_name'] = $row->tag_name;
				$row_data['status'] = $row->status;
				$url = url('edit_tag/'.$Id);
				$row_data['action'] = '<a href="javascript:void(0);" onclick="return edit_tag('.$Id.');" class="EditRoom "  title="Edit"><i class="fa fa-edit"></i></a> - <a href="javascript:void(0);" class="DeleteRoom " onclick="return delete_tag('.$Id.');" title="Delete"><i class="fa fa-trash"></i></a> ';
				$data[] = $row_data;
			}
			
		}
		//$datas = array_map('array_values', $data);
		$DataArray = array('draw'=>$_REQUEST['draw'],'recordsTotal'=>$total ,'recordsFiltered'=>$total, 'data'=> $data);
		echo json_encode($DataArray);
	}
	public function edit_tag(request $response){
		$Id = $response->Id;
		$Resut = $this->Common->getList('mr_tags', 'id', $response->Id);
		echo json_encode($Resut[0]);
	}
	public function submit_tag(request $response){
		$TagId= $response->TagId;
		$TagName= trim($response->TagName);
		$TagSlug = $this->seoUrl($TagName,'&');
		$LoginUser = $response->session()->get('user');
		$dataArr= array(
			'tag_name'=>$response->TagName,
			'slug'=>$TagSlug,
			'status'=>$response->Tagstatus,
			
		);
		
		if($TagId==''){
			$dataArr['created_by']=$LoginUser->id;
			$Resut = $this->Common->getList('mr_tags', 'tag_name', $response->TagName);
			if(empty($Resut[0])){
				$this->Common->saveData('mr_tags', $dataArr);
				echo 'Added';
			}else{
				echo 'exist';
			}
			
		}else{
			$Result = $this->Common->check_url('mr_tags', 'tag_name', $response->TagName, $TagId);
			if(count($Result)>0){
				echo 'exist'; exit;
			}
			$dataArr['updated_by']=$LoginUser->id;
			$this->Common->updateData('mr_tags', 'id', $TagId, $dataArr);
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
	public function getTagjson(request $response){
		$WhrTag = array(
			'status'=>'Active'
		);
		$SelectTagData = ['id','tag_name'];
		$TagLists = $this->Common->getDataBySelect('mr_tags', $WhrTag,$SelectTagData);
		$TagArr = array();
		foreach($TagLists as $TagData){
			$TagArr[] = $TagData->tag_name;
		}
		echo json_encode($TagArr);
	}
	public function submit_import_tags(request $response){
		$LoginUser = $response->session()->get('user');
		if ($response->hasFile('DataFile')) {
			$DataSectionFile = $response->file('DataFile');
			$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
			$spreadsheet = $reader->load($response->file('DataFile'));
			$sheetData = $spreadsheet->getActiveSheet()->toArray();
			//echo '<pre>'; print_r($sheetData); exit;
			if (!empty($sheetData)) {
				$TagArray = array();
				for ($i=1; $i<count($sheetData); $i++) {
					$TagName = trim($sheetData[$i][0]);
					if($TagName!=''){
						$Resut = $this->Common->getList('mr_tags', 'tag_name', $TagName);
						if($Resut->count()>0){
							//$TagId = $Resut[0]->id;
							//$dataArr['tag_name']=$TagName;
							//$this->Common->updateData('mr_tags', 'id', $TagId, $dataArr);
						}else{
							$TagSlug = $this->seoUrl($TagName,'&');
							$TagArray[] = array(
								'tag_name'=>$TagName,
								'slug'=>$TagSlug,
								'status'=>'Active',
								'created_by'=>$LoginUser->id,
							);
						}
					}
				}
				if(!empty($TagArray)){
					$this->Common->saveMultipleData('mr_tags', $TagArray);
				}
			}
			//exit;
			$response->session()->flash('ReportMessage', 'Tags Imported');
			return Redirect::to('/tags');	 
		}else{
			$response->session()->flash('ReportMessage', 'Invalid file');
			return Redirect::to('/tags');
		}
	}
}
