<?php
/*
	File = class.core.php
	Date = 18-5-2015
*/
// include require files
require_once("class.functions.php");
require_once("class.db.php");
// basic function call
if( $is_admin == 1 )
	$bc_core->bcCheckLogin();
$bc_core->bcGetDefault( DB_PREFIX.'setting' );
?>