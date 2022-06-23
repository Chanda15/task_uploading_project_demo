<?php
/*
	File = class.defaults.php
	Date = 18-5-2015
*/

class BC_Default
{
	// define variable
	public $def_set = array('test');
	
	function bcGetDefault( $tbl = '' )
	{
		if($tbl == '')
			return false;
		
		$this->def_set = $bc_db->bcGetTable( $tbl );
	}
}

$bc_func = new BC_Default();
?>