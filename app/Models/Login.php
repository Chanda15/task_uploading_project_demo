<?php

namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;

class Login extends Model
{
	function checkLoginDetails($TableName, $ArrData){
        $Res= DB::table($TableName);
		$Res->where($ArrData);
        $Res = $Res->get();
        return $Res;
    }
	public function user_list($search_term='',$startpoint='', $per_page='')
    {
		$Users= DB::table('users as u')
                ->select('u.*', 'u.id as user_id', 'r.name as role_name')
                ->join('roles as r', 'u.role_id', '=', 'r.id');
				$Users = $Users->where('u.role_id','<>',1);
				$Users = $Users->where('u.is_deleted','=',0);
				if($search_term['value']!=''){
					$Leads->where(function ($Leads)  use ( $search_term ) {
					    $Users = $Users->where('u.user_name', 'like', '%' . $search_term['value'] . '%')
						->orWhere('u.email_id','LIKE','%'.$search_term['value'].'%')
						->orWhere('r.name','LIKE','%'.$search_term['value'].'%');
				    });
					
				}

				if($startpoint!='')
					$Users = $Users->offset($startpoint)->limit($per_page);
				$Users=$Users->orderBy('u.id','DESC');	
				$Users=$Users->get();	
        return $Users;
	}
}
