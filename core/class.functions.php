<?php
/*
	File = class.function.php
	Date = 18-5-2015
*/

class BC_Core
{
	// define variable
	public $def_set = array();
	public $def_lang = array();
	
	public function __construct()
	{
		
	}
	
	public function bcGetCurrentURL()
	{

		///$_SERVER['HTTP_HOST']=='localhost';
		$pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
		if ($_SERVER["SERVER_PORT"] != "80")
		{
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} 
		else 
		{
			$pageURL .= 'localhost'.$_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
	public function bcGetValue($tbl, $fld, $whr, $val)
	{
		if($tbl == '' && $fld == '' && $whr == '' && $val == '')
			return false;
		
		global $bc_db;
		
		$qry = "SELECT $fld FROM $tbl WHERE $whr='".$val."'";
		$row = $bc_db->bcGetQuery($qry);
		
		return $row[0][$fld];
	}
	
	public function bcGetLatLng($address)
	{
		$prepAddr = str_replace(' ','+',$address);
		$geocode=file_get_contents('https://maps.google.com/maps/api/geocode/json?address='.$prepAddr.'&sensor=false');
		$output= json_decode($geocode);
		$data[] = $output->results[0]->geometry->location->lat;
		$data[] = $output->results[0]->geometry->location->lng;
		
		return $data;
	}
	
	public function bcGetValueWhr($tbl, $fld, $whr)
	{
		if($tbl == '' && $fld == '' && $whr == '')
			return false;
		
		global $bc_db;
		
		$qry = "SELECT $fld FROM $tbl WHERE ".$whr;
		$row = $bc_db->bcGetQuery($qry);
		
		return $row[0][$fld];
	}
	
	public function bcCheckDuplicate($tbl='', $fld='', $val='')
	{
		global $bc_db;
		
		$qry = "SELECT ".$fld." FROM ".$tbl." WHERE ".$fld."='".$val."'";
		$row = $bc_db->bcGetQuery($qry);
		
		if(count($row) > 0)
			return true;
		else
			return false;
	}
	
	public function bcCheckEditDuplicate($tbl='', $fld='', $val='', $chk='')
	{
		global $bc_db;
		
		$qry = "SELECT ".$fld." FROM ".$tbl." WHERE ".$fld."='".$val."' AND id!='".$chk."'";
		$row = $bc_db->bcGetQuery($qry);
		
		if(count($row) > 0)
			return true;
		else
			return false;
	}
	
	public function bcGetCount( $tbl = '', $fld = '', $whr = '1=1' )
	{
		if($tbl=='' && $fld=='')
			return '0';
		
		global $bc_db;
		
		$qry = "SELECT COUNT(".$fld.") AS cnt_fld FROM ".$tbl." WHERE ".$whr;
		$row = $bc_db->bcGetQuery($qry);
		
		return $row[0]['cnt_fld'];
	}
	
	public function bcIncrementCounter( $tbl, $fld, $whr, $val, $counter = 1 )
	{
		if($tbl == '' && $fld == '' && $whr == '' && $val == '')
			return false;
		
		global $bc_db;
		
		$qry = "UPDATE $tbl SET $fld=$fld+$counter WHERE $whr='".$val."'";
		$row = $bc_db->bcQuery($qry);
		
		return $row;
	}
	
	public function bcGet404()
	{
		
	}
	
	public function bcGetString( $val = '' )
	{
		if($val=='')
			return '';
		
		$val = stripslashes($val);
		return $val;
	}
	
	public function bcGetError( $msg = '' )
	{
		echo '<pre class="error_msg">'.$msg.'</pre>'; exit;
	}
	
	public function bcGoPage( $url = '' )
	{
		echo '<script>window.location="'.$url.'";</script>'; exit;
	}
	
	public function bcGetDate( $dt = '', $fmt = 'm/d/Y' )
	{
		if($dt == '')
			return '';
		
		return date($fmt, strtotime($dt));
	}
	
	public function bcGetDefault( $tbl = '' )
	{
		if($tbl == '')
			return false;
		
		global $bc_db;
		
		$def_arr = $bc_db->bcGetTable( $tbl );
		foreach( $def_arr as $def_data)
		{
			$this->def_set[$def_data['slug']] = $def_data['value'];
		}
	}
	
	public function bcUpload( $img_path, $fld = '', $ftype = 'image', $old_img = '' )
	{
		if($img_path['error']!=0)
			return $old_img;
		
		$file_nm = rand(1000,9999).'_'.$img_path['name'];
		move_uploaded_file($img_path['tmp_name'], $fld.$file_nm);
		
		if($old_img != '')
			unlink($fld.$old_img);
		
		return $file_nm;
	}
	
	public function bcDelete( $old_img = '' )
	{
		if($old_img != '')
			unlink($old_img);
	}
	
	public function bcGetAgo( $tp_date = '' )
	{
		if($tp_date=='')
			return '';
		
		$curr_time = strtotime(date('d-m-Y H:i:s'));
		$tp_time = strtotime($tp_date);
		$time_diff = $curr_time - $tp_time;
		
		if($time_diff < 0)
			return '';
		
		$tp_time_date = $this->bcTimeToDate( $time_diff );
		
		if( $tp_time_date['day'] > 0 )
			$tp_msg = $tp_time_date['day'].' days ago';
		elseif( $tp_time_date['hour'] > 0 )
			$tp_msg = $tp_time_date['hour'].' hours ago';
		elseif( $tp_time_date['minute'] > 0 )
			$tp_msg = $tp_time_date['minute'].' minutes ago';
		else
			$tp_msg = $tp_time_date['second'].' seconds ago';
			
		return $tp_msg;
	}
	
	public function bcTimeToDate( $tp_time = '' )
	{
		if($tp_time=='')
			return '';
		
		$dt_arr = array();
		
		$d = floor($tp_time/86400);
		$dt_arr['day'] = ($d < 10 ? '0' : '').$d;
		
		$h = floor(($tp_time-$d*86400)/3600);
		$dt_arr['hour'] = ($h < 10 ? '0' : '').$h;
		
		$m = floor(($tp_time-($d*86400+$h*3600))/60);
		$dt_arr['minute'] = ($m < 10 ? '0' : '').$m;
		
		$s = $tp_time-($d*86400+$h*3600+$m*60);
		$dt_arr['second'] = ($s < 10 ? '0' : '').$s;
		
		return $dt_arr;
	}
	
	public function bcCheckLogin()
	{
		if(!(isset($_SESSION['uid']) && $_SESSION['uid']!=''))
			$this->bcGoPage("index.php");
	}
	
	public function bcCheckPageLevel($fileName)
	{
		if((isset($_SESSION['u_level']) && $_SESSION['u_level']!=''))
		{
			$pages['1'] = array(
				'dashboard.php',
				'manage_profile.php',
				'manage_application.php', 
				'manage_area.php', 
				'manage_category.php',
				'manage_city.php',
				'manage_point_setting.php',
				'manage_setting.php',
				'manage_state.php',
				'manage_user.php',
				'manage_user_meta.php',
				'manage_zipcode.php',
				'report_application.php',
				'report_publisher.php'
			);
			$pages['2'] = array(
				'dashboard.php',
				'manage_profile.php',
				'manage_application.php', 
				'manage_area.php', 
				'manage_category.php',
				'manage_city.php',
				'manage_point_setting.php',
				'manage_state.php',
				'manage_user.php',
				'manage_user_meta.php',
				'manage_zipcode.php',
				'report_application.php',
				'report_publisher.php'
			);
			$pages['3'] = array(
				'dashboard.php',
				'manage_profile.php',
				'report_application.php',
				'report_publisher.php'
			);
			
			if(!in_array($fileName, $pages[$_SESSION['u_level']]))
			{
				$this->bcGoPage("dashboard.php");
			}
			
		}	
	}
	
	public function bcUpdateSetting( $slug = '', $val = '' )
	{
		if($slug == '')
			return false;
		
		global $bc_db;
		
		$qry = "UPDATE ".DB_PREFIX."setting SET meta_value='".$val."' WHERE meta_key='".$slug."'";
		$res = $bc_db->bcQuery($qry);
		
		return true;
	}
	
	public function bcSlug( $name = '' )
	{
		if($name == '')
			return '';
		
		$name = str_replace(' ', '-', strtolower(trim($name)));
		$name = preg_replace('/[^A-Za-z0-9\-]/', '', $name);
		$name = preg_replace('/-+/', '-', $name);
		
		return $name;
	}
	
	public function bcCheckKey( $key = '' )
	{
		$salt_status = array();
		
		if($key=='')
		{
			$salt_status['error_code'] = 1001;
			$salt_status['error_msg'] = 'salt_key_is_null.';
		}
		
		if(SALT_KEY == $key)
		{
			$salt_status['error_code'] = 1000;
			$salt_status['error_msg'] = 'salt_key_is_success.';
		}
		else
		{
			$salt_status['error_code'] = 1002;
			$salt_status['error_msg'] = 'salt_key_is_failure.';
		}
		
		return $salt_status;
	}
	
	public function bcGetLang( $lang = 'en' )
	{
		if($lang=='')
			return 'en';
		
		foreach( $this->lang_arr as $key => $val)
		{
			if($key==$lang)
				return $lang;
		}
		
		return 'en';
	}
	
	public function bgGetPassword()
	{
		$seed = str_split('abcdefghijklmnopqrstuvwxyz'
						 .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
						 .'0123456789'); // and any other characters
		shuffle($seed); // probably optional since array_is randomized; this may be redundant
		$rand = '';
		foreach (array_rand($seed, 6) as $k) $rand .= $seed[$k];
		
		return $rand;
	}
	
	public function bcSendNotification( $registatoin_ids, $message )
	{
		// Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';

        $fields = array(
            'registration_ids' => $registatoin_ids,
            'data' => $message,
        );

        $headers = array(
            'Authorization: key='.GOOGLE_API_KEY,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }

        // Close connection
        curl_close($ch);
        echo $result;
	}
	
	public function bcSendHtmlEmail($from, $to, $sub, $body)
	{
		if($_SERVER['HTTP_HOST']=='localhost')
		{
			return '';
		}
		else
		{	
			$headers = "From: sales@reportsanddata.com\r\n";
			//$headers .= "Reply-To: ". strip_tags($_POST['req-email']) . "\r\n";
			//$headers .= "CC: susan@example.com\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
			mail($to, $sub, $body, $headers);
			return true;
		}
	}
	
	public function bcTranslate($text, $from_lan = 'en', $to_lan = 'en')
	{
		$from_lan = 'en';
		if(isset($_SESSION['curr_lang']) && $_SESSION['curr_lang']!='')
			$to_lan = $_SESSION['curr_lang'];
		else
			$to_lan = 'en';
		
		if($to_lan=='en')
		{
			$translated_text = $text;
		}
		else
		{
			$json = json_decode(file_get_contents('https://ajax.googleapis.com/ajax/services/language/translate?v=1.0&q=' . urlencode($text) . '&langpair=' . $from_lan . '|' . $to_lan));
			//print_r($json);
			$translated_text = $json->responseData->translatedText;
		}
		return $translated_text;
	}
	
	public function getTranslation($txt)
	{
		if($this->def_lang[$txt]!='')
		{
			return $this->def_lang[$txt];
		}
		else
		{
			return $txt;
		}
	}
	
	public function bcUpdateGCM( $tbl, $field, $val, $whr)
	{
		global $bc_db;
		
		$qry2 = "UPDATE ".$tbl." SET ".$field."='".$val."' WHERE ".$whr;
		$res2 = $bc_db->bcQuery($qry2);
	}
	
	public function bcDaysAgo($datetime, $full = false)
	{
		$now = new DateTime;
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);
	
		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;
	
		$string = array(
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		);
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			} else {
				unset($string[$k]);
			}
		}
	
		if (!$full) $string = array_slice($string, 0, 1);
		return $string ? implode(', ', $string) . ' ago' : 'just now';
	}
	
	public function bcGetPaging($qry, $paged=1, $per_page = 10, $url = '')
	{
		global $bc_db;
		$paged = ($paged!='')?$paged:1;
		if(strpos($url,'?')===false)
			$url = $url.'?';
		else
			$url = $url.'&';
		
		$qry3 = $qry.' LIMIT '.($per_page*($paged-1)).', '.$per_page;
		$res3 = $bc_db->bcGetQuery($qry3);
		
		$res2 = $bc_db->bcGetQuery($qry);
		
		$total_rec = count($res2);
		$total_page = ceil($total_rec/$per_page);
		
		$data = '<ul class="pagination pagination-sm justify-content-end">
		<li class="page-item"><a class="page-link pg-link" href="'.$url.'paged=1"><i class="uk-icon-angle-double-left"></i></a></li>';
		
		for($i=1;$i<=$total_page;$i++)
		{
			$data .= '<li class="page-item ';
			$data .= ($paged==$i)?'active':'';
			$data .= '"><a class="page-link pg-link" href="'.$url.'paged='.$i.'">'.$i.'</a></li>';
		}
		
		$data .= '<li class="page-item"><a class="page-link pg-link" href="'.$url.'paged='.$total_page.'"><i class="uk-icon-angle-double-right"></i></a></li>';
		
		return array($res3, $data);
	}
	
	public function bcGetPaging2($qry, $paged=1, $per_page = 10, $url = '')
	{
		global $bc_db;
		$paged = ($paged!='')?$paged:1;
		$current_page = ($paged!='')?$paged:1;
		if(strpos($url,'?')===false)
			$url = $url.'?';
		else
			$url = $url.'&';
		
		$qry3 = $qry.' LIMIT '.($per_page*($paged-1)).', '.$per_page;
		$res3 = $bc_db->bcGetQuery($qry3);
		
		$res2 = $bc_db->bcGetQuery($qry);
		
		$total_rec = count($res2);
		$total_pages = ceil($total_rec/$per_page);


		$data = '<ul class="pagination pagination-sm justify-content-end">
		<li class="page-item"><a class="page-link pg-link" href="'.$url.'paged=1"><i class="fa fa-angle-double-left"></i></a></li>';
    if ($total_pages >= 1 && $current_page <= $total_pages) {
        //$links .= "<a href=''>1</a>";
        $i = max(2, $current_page - 5);
        if ($i > 2)
            $data .= " ... ";
        for ($i=$current_page; $i < min($current_page + 6, $total_pages); $i++) {
            //$links .= "<a href=''>".$i."</a>";

            	$data .= '<li class="page-item ';
				$data .= ($current_page==$i)?'active':'';
				$data .= '"><a class="page-link pg-link" href="'.$url.'paged='.$i.'">'.$i.'</a></li>';
        }
        if ($i != $total_pages)
            $data .= " ... ";
       // $links .= "<a href=''>".$total_pages."</a>";

         $data .= '<li class="page-item"><a class="page-link pg-link" href="'.$url.'paged='.$total_pages.'"><i class="fa fa-angle-double-right"></i></a></li>';
    }







	// 	$limit=5; // May be what you are looking for

 //    if ($total_page >=1 && $paged <= $total_page)
 //    {
 //        $counter = 1;
 //        $data = '';
 //        if ($paged > ($limit/2))
 //           { 
 //           	$link .= '<ul class="pagination pagination-sm justify-content-end">
	// 	<li class="page-item"><a class="page-link pg-link" href="'.$url.'paged=1"><i class="fa fa-angle-double-left"></i></a></li>...';
	// }
 //        for ($x=$paged; $x<=$total_page;$x++)
 //        {

 //            if($counter < $limit)
 //               	$data .= '<li class="page-item" ';
	// 		$data .= ($paged==$i)?'active':'';
	// 		$data .= '><a class="page-link pg-link" href="'.$url.'paged='.$x.'">'.$x.'</a></li>';

 //            $counter++;
 //        }
 //        if ($paged < $total_page - ($limit/2))
 //         { $data .= '..<li class="page-item"><a class="page-link pg-link" href="'.$url.'paged='.$total_page.'"><i class="fa fa-angle-double-right"></i></a></li>';}
 //    }




		
		// $data = '<ul class="pagination pagination-sm justify-content-end">
		// <li class="page-item"><a class="page-link pg-link" href="'.$url.'paged=1"><i class="fa fa-angle-double-left"></i></a></li>';
		
		// for($i=1;$i<=$total_page;$i++)
		// {
		// 	$data .= '<li class="page-item ';
		// 	$data .= ($paged==$i)?'active':'';
		// 	$data .= '"><a class="page-link pg-link" href="'.$url.'paged='.$i.'">'.$i.'</a></li>';
		// }
		
		// $data .= '<li class="page-item"><a class="page-link pg-link" href="'.$url.'paged='.$total_page.'"><i class="fa fa-angle-double-right"></i></a></li>';
		
		return array($res3, $data);
	}
}

$bc_core = new BC_Core();
?>
