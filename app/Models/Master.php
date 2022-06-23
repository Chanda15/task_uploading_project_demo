<?php

namespace App\Models;
use DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Helper;
use Config;
class Master extends Model
{
	
	function checkDetails($TableName, $ArrData){
        $Res= DB::table($TableName);
		$Res->where($ArrData);
        $Res = $Res->get();
        return $Res;
    }
	public function get_department_list($search_term='',$startpoint='', $per_page='')
    {
		$Users= DB::table('department as m')
                ->select('m.*');
				if($search_term['value']!='')
					$Users = $Users->where('m.name', 'like', '%' . $search_term['value'] . '%');
				if($startpoint!='')
					$Users = $Users->offset($startpoint)->limit($per_page);
				$Users=$Users->orderBy('m.id','desc');
				$Users=$Users->get();	
        return $Users;
	}
	
	public function create_masters(array $input, $table_name)
    {
		DB::table($table_name)->insert($input);
    }
	
	function getList($TableName, $ColumnId='', $ColumnValue=''){
        $Res= DB::table($TableName);
		if($ColumnValue!='')
			$Res->where($ColumnId,$ColumnValue);
                $Res = $Res->get();
        return $Res;
    }
	function getData($TableName, $ArrData){
        $Res= DB::table($TableName);
		$Res->where($ArrData);
        $Res = $Res->get();
        return $Res;
    }
	public function get_tag_list($search_term='',$startpoint='', $per_page='')
    {
		$Users= DB::table('mr_tags as m')
                ->select('m.*');
				if($search_term['value']!='')
					$Users = $Users->where('m.tag_name', 'like', '%' . $search_term['value'] . '%');
				if($startpoint!='')
					$Users = $Users->offset($startpoint)->limit($per_page);
				$Users=$Users->orderBy('m.id','desc');
				$Users=$Users->get();	
        return $Users;
	}
}
