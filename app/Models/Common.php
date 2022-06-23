<?php

namespace App\Models;
use DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Helper;
class Common extends Model
{
	function getRelatedTags($RelatedTagIds){
		//DB::enableQueryLog();
        $Categories= DB::table('mr_tags')
                ->select('mr_tags.id','mr_tags.tag_name','mr_tags.slug');
				$Categories = $Categories->where('mr_tags.status','=','Active');
				$Categories = $Categories->whereIn('mr_tags.id',$RelatedTagIds);
				$Categories = $Categories->get();
			   //dd(DB::getQueryLog());
        return $Categories;
    }
	function getRelatedTagsCount($Table, $RelatedTagIds){
		//DB::enableQueryLog();
        $Categories= DB::table($Table);
				$Categories=$Categories->where('related_tags', 'like', '%' . $RelatedTagIds . '%');
				//$Categories = $Categories->whereIn('related_tags',$RelatedTagIds);
				$Categories = $Categories->get()->count();
			   //dd(DB::getQueryLog());
        return $Categories;
    }
	function check_url($TableName, $ColumnId='', $ColumnValue='', $Id=''){
        $Res= DB::table($TableName);
		if($ColumnValue!='')
			$Res->where($ColumnId,$ColumnValue);
		if($Id!='')
			$Res->whereNotIn('id',[$Id]);
                $Res = $Res->get();
        return $Res;
    }
    function saveData($TableName, $data){
       $saveenquiry =  DB::table($TableName)->insertGetId($data);
       return $saveenquiry;
    }
	function saveMultipleData($TableName, $data = array()){
       $saveenquiry =  DB::table($TableName)->insert($data);
       return $saveenquiry;
    }
	function saveUpdateMultipleData($TableName, $data = array()){
       $saveenquiry =  DB::table($TableName)->update($data, 'email_id');
       return $saveenquiry;
    }
	function updateData($TableName, $ColumnId='', $ColumnValue='', $data){
		//DB::enableQueryLog();
       DB::table($TableName)->where($ColumnId, $ColumnValue)->update($data);
	   //dd(DB::getQueryLog());
       //return $saveenquiry;
    }
	function deleteData($TableName, $ColumnId='', $ColumnValue=''){
       DB::table($TableName)->where($ColumnId, $ColumnValue)->delete();
    }
	function deleteDataWhr($TableName, $ArrData){
       DB::table($TableName)->where($ArrData)->delete();
    }
	function search_data($search_term){
		/*$ReportDetail= DB::table('mr_blog')
			->select('id', 'blog_type',  \DB::raw('SUBSTRING(title,1,200) as ntitle'), 'slug', 'publish_date', \DB::raw('SUBSTRING(description,1,200) as ndesc'))
			->where('status','active')
			->where('title', 'like', '%' . $search_term . '%')
			->orderBy('id','desc')
			->limit(30)
			->get();
			return $ReportDetail;*/
	}
	function getRoleList($TableName){
		$Res= DB::table($TableName);
			$Res->where('name','<>','Admin');
                $Res = $Res->get();
        return $Res;
	}
	function getList($TableName, $ColumnId='', $ColumnValue=''){
        $Res= DB::table($TableName);
		if($ColumnValue!='')
			$Res->where($ColumnId,$ColumnValue);
                $Res = $Res->get();
        return $Res;
    }
	public function get_permission_list($CompanyId, $search_term='',$startpoint='', $per_page='')
    {
		$Users= DB::table('menu as m')
                ->select('m.*', 'u.user_name')
                ->join('users as u', 'u.id', '=', 'm.created_by');
				if($search_term['value']!='')
					$Users = $Users->where('m.menu_name', 'like', '%' . $search_term['value'] . '%');
				if($startpoint!='')
					$Users = $Users->offset($startpoint)->limit($per_page);
				$Users=$Users->orderBy('m.id','desc');
				$Users=$Users->get();	
        return $Users;
	}
	public function getSelectionUserByRegion($RegionCode, $CompanyId)
    {
		$Users= DB::table('users')
                ->select('id', 'user_name')
				->where('status',1)
				->where('company_id',$CompanyId)
				->where('region_code', 'like', '%' . $RegionCode . '%');
				$Users=$Users->get();	
        return $Users;
	}
	function getTableData($TableName){
		//DB::enableQueryLog();
        $Res= DB::table($TableName);
        $Res = $Res->get();
		// dd(DB::getQueryLog());
        return $Res;
    }
	function getData($TableName, $ArrData){
		//DB::enableQueryLog();
        $Res= DB::table($TableName);
		$Res->where($ArrData);
        $Res = $Res->get();
		// dd(DB::getQueryLog());
        return $Res;
    }
	function getDataBySelect($TableName, $ArrData, $SelectData=''){
		//DB::enableQueryLog();
        $Res= DB::table($TableName);
		if($SelectData!='')
		$Res->select($SelectData);
		$Res->where($ArrData);
        $Res = $Res->get();
		 //dd(DB::getQueryLog()); exit;
        return $Res;
    }
	public function get_role_list($CompanyId, $search_term='',$startpoint='', $per_page='')
    {
		$Users= DB::table('roles as m')
                ->select('m.*');
				if($search_term['value']!='')
					$Users = $Users->where('m.name', 'like', '%' . $search_term['value'] . '%');
				if($startpoint!='')
					$Users = $Users->offset($startpoint)->limit($per_page);
				$Users=$Users->orderBy('m.id','desc');
				$Users=$Users->get();
        return $Users;
	}
	public function get_menu_list()
    {
		$Users= DB::table('menu as m')
                ->select('m.*')
				->where('status',1);
				$Users=$Users->orderBy('m.sort_order','asc');
				$Users=$Users->get();
        return $Users;
	}
	public function getPermissionMenuWise($SubPermissionArr)
    {
		$Users= DB::table('permissions as m')
                ->select('m.*')
				->where($SubPermissionArr);
				$Users=$Users->orderBy('m.sort_order','asc');
				$Users=$Users->get();
        return $Users;
	}
	function updateCategoryData($TableName, $CompanyId='', $MainCatId='', $data){
		//DB::enableQueryLog();
       DB::table($TableName)->where('company_id', $CompanyId)->where('main_cat_id', $MainCatId)->update($data);
	   //dd(DB::getQueryLog());
       //return $saveenquiry;
    }
	/******** Start Helper function***************/
	public static function getHelperList($TableName, $ColumnId='', $ColumnValue=''){
        $Res= DB::table($TableName);
		if($ColumnValue!='')
			$Res->where($ColumnId,$ColumnValue);
                $Res = $Res->get();
        return $Res;
    }
	public static function getRolePermission($Id)
    {
		$Users= DB::table('permission_role as m')
                ->select('u.name')
                ->join('permissions as u', 'u.id', '=', 'm.permission_id')
				->where('role_id',$Id)
				->get();	
        return $Users;
	}
	public static function getUserPermission($Id)
    {
		$Users= DB::table('permission_user as m')
                ->select('u.name')
                ->join('permissions as u', 'u.id', '=', 'm.permission_id')
				->where('user_id',$Id)
				->get();	
        return $Users;
	}
	public static function getListValue($TableName, $ColumnId='', $ColumnValue=''){
        $Res= DB::table($TableName);
		if($ColumnValue!='')
			$Res->where($ColumnId,$ColumnValue);
                $Res = $Res->get();
        return $Res;
    }
	public static function getDataHelperFun($TableName, $ArrData){
		//DB::enableQueryLog();
        $Res= DB::table($TableName);
		$Res->where($ArrData);
        $Res = $Res->get();
		// dd(DB::getQueryLog());
        return $Res;
    }
	/******** End Helper function***************/
	public function emplyee_autosearch($Table_name, $UserName)
    {
		$UserData = Helper::getUserData();
		$CompanySessionId = Helper::getCompanySession();
		$Users= DB::table($Table_name)
                ->select('id', 'user_name')
				->where('user_name', 'like', '%' . $UserName . '%')
				->where('region_code', 'like', '%' . $UserData->region_code . '%')
				->where('company_id',$CompanySessionId)
				->get();
        return $Users;
	}
	function checkdatabse($params){
		config(['database.connections.mysql_crm' => [
            'driver' => $params['driver'],
            'host' => $params['host'],
            'database' => $params['dbname'],
            'username' => $params['username'],
            'password' => $params['password'],
			'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ]]);

        return DB::connection('mysql_crm');
		/*$config = Config::get('connections.mysql_new');
		$config['driver'] = $params['driver'];
		$config['host'] = $params['host'];
		$config['database'] = $params['dbname'];
		$config['username'] = $params['username'];
		$config['password'] = $params['password'];
		config()->set('connections.mysql_new', $config);
		return DB::connection('mysql_new');*/
	}
	function getData_newconnection($Connection, $TableName, $ArrData){
		//DB::enableQueryLog();
        $Res= $Connection->table($TableName);
		$Res->where($ArrData);
        $Res = $Res->get();
		// dd(DB::getQueryLog());
        return $Res;
    }
	function getList_newconnection($Connection, $TableName, $ColumnId='', $ColumnValue=''){
        $Res= $Connection->table($TableName);
		if($ColumnValue!='')
			$Res->where($ColumnId,$ColumnValue);
                $Res = $Res->get();
        return $Res;
    }
	public function getSelectionUserByRegion_newconnection($Connection, $RegionCode, $CompanyId)
    {
		$Users= $Connection->table('users')
                ->select('id', 'user_name')
				->where('status',1)
				->where('company_id',$CompanyId)
				->where('region_code', 'like', '%' . $RegionCode . '%');
				$Users=$Users->get();	
        return $Users;
	}
	function saveData_newconnection($Connection, $TableName, $data){
		//$Connection->enableQueryLog();
       $saveenquiry =  $Connection->table($TableName)->insertGetId($data);
	   //dd($Connection->getQueryLog());
       return $saveenquiry;
    }
	function getCountries($isocode=''){
        $Res= DB::table('mr_countries');
		if($isocode!='')
			$Res->where('countries_iso_code',$isocode);
                $Res = $Res->get();
        return $Res;
    }
	/******** Start Helper function for header***************/
	 public static function getParentCategoryMenu($categoryId=0, $orderby=''){
        $Categories= DB::table('mr_category')
                            ->select('id', 'name', 'slug', 'image' ,'category_icon')
                            ->where('parent_id',$categoryId)
                            ->where('status','active');
							if($orderby!='')
								$Categories=$Categories->orderBy('home_page_research_sort','asc');
                            $Categories = $Categories->get();
        return $Categories;
    }
	public static function getLatestReportsBycategoryIdforMenu($limit='', $CategoryId=''){
		//DB::enableQueryLog();
        $Categories= DB::table('mr_report')
                ->select('mr_report.id','mr_report.title','mr_report.url','mr_report.single_price','mr_report.publish_date','mr_report.no_of_pages','mr_category.name', 'mr_report.publish_type')
                ->join('mr_category', function ($join) {
					$join->on('mr_category.id', '=', 'mr_report.cat_id');
						 //->orOn('mr_category.id', '=', 'mr_report.sub_cat_id');
				});
                $Categories = $Categories ->where('mr_category.id',$CategoryId);
                $Categories = $Categories ->orderBy(DB::raw('RAND()'));
               
                if($limit !='')
                  $Categories = $Categories->limit($limit);
				$Categories = $Categories->get();
			   //dd(DB::getQueryLog());
        return $Categories;
    }
	/******** End Helper function***************/
	function getTableDataByLimit($TableName, $id1, $id2){
		//DB::enableQueryLog();
        $Res= DB::table($TableName);
		$Res->whereBetween('id', [$id1, $id2]);
        $Res = $Res->get();
		// dd(DB::getQueryLog());
        return $Res;
    }
	public function get_event_list($search_term = '', $startpoint = '', $per_page = '')
	{
		$Users = DB::table('events')
			->select('title', 'id', 'short_keyword');
		if ($search_term['value'] != '') {
			$Users->where(function ($Users)  use ($search_term) {
				$Users = $Users->where('title', 'like', '%' . $search_term['value'] . '%');
			});
		}
		if ($startpoint != '')
			$Users = $Users->offset($startpoint)->limit($per_page);
		$Users = $Users->orderBy('id', 'desc');
		$Users = $Users->get();
		return $Users;
	}
	public function get_assign_event_list($Data, $search_term = '', $startpoint = '', $per_page = '')
	{
		$Users = DB::table('assign_events')
			->select('assign_events.id', 'assign_events.assign_date', 'u.user_name as user_name', 'e.title as event_name', 'assign_events.registration_number', 'assign_events.result')
			->join('users as u', 'u.id', '=', 'assign_events.user_id')
			->join('events as e', 'e.id', '=', 'assign_events.event_id');
		if(isset($Data['certificate_request'])){
			$Users = $Users ->where('assign_events.order_request',1);
		}
		if(isset($Data['UserId'])){
			$Users = $Users ->where('assign_events.user_id',$Data['UserId']);
		}
		if ($search_term['value'] != '') {
			$Users->where(function ($Users)  use ($search_term) {
				$Users = $Users->where('u.user_name', 'like', '%' . $search_term['value'] . '%')
				->orwhere('e.title', 'like', '%' . $search_term['value'] . '%');
			});
		}
		if ($startpoint != '')
			$Users = $Users->offset($startpoint)->limit($per_page);
		$Users = $Users->orderBy('id', 'desc');
		$Users = $Users->get();
		return $Users;
	}
	public function getLastRegistrationNumber(){
		return $res =  DB::table('users')->select('registration_number')->where('role_id', 2)->latest('id')->first();
	}
	public function getLastAssignEventRegNo(){
		return $res =  DB::table('assign_events')->select('registration_number')->latest('id')->first();
	}
	public function getAssignEventList($limit='', $Data)
	{
		$UserId = $Data['user_id'];
		$EventId = $Data['event_id'];
		$Users = DB::table('assign_events')
			->select('assign_events.id', 'assign_events.assign_date', 'u.user_name as user_name', 'e.title as event_name', 'assign_events.result', 'assign_events.registration_number')
			->join('users as u', 'u.id', '=', 'assign_events.user_id')
			->join('events as e', 'e.id', '=', 'assign_events.event_id');
			if($EventId!='')
				$Users = $Users ->where('assign_events.event_id',$EventId);
			if($UserId!='')
				$Users = $Users ->where('assign_events.user_id',$UserId);
		$Users = $Users->orderBy('assign_events.id', 'desc');
		$Users= $Users->paginate($limit);
		return $Users;
	}
	public function getResultByRegId($RegistrationNumber='')
	{
		$Users = DB::table('assign_events')
			->select('assign_events.*', 'u.*', 'e.title as event_name','assign_events.registration_number as reg_no','e.short_keyword as event_short_keyword')
			->join('users as u', 'u.id', '=', 'assign_events.user_id')
			->join('events as e', 'e.id', '=', 'assign_events.event_id');
			if($RegistrationNumber!='')
				$Users = $Users ->where('assign_events.registration_number',$RegistrationNumber);
		$Users = $Users->get();
		return $Users;
	}
	public function geEvents()
    {
		$query_day = DB::table('events')
			->whereDate('event_date', '>=', Carbon::today());
			$query_day = $query_day->get();
		return  $query_day;
    }
}
