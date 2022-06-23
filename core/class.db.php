<?php
/*
	File = class.db.php
	Date = 18-5-2015
*/

class BC_Db
{
	// define variable
	private $db_host = '';
	private $db_user = '';
	private $db_pass = '';
	private $db_dbname = '';
	private $conn = '';
	
	public function __construct( $host = '', $user = '', $pass = '', $db = '')
	{
		global $bc_core;
		
		$this->db_host = $host;
		$this->db_user = $user;
		$this->db_pass = $pass;
		$this->db_dbname = $db;
		
		if($this->db_host == '' && $this->db_user == '' && $this->db_dbname == '')
			$bc_core->bcGetError("Database configuration not set.");
		
		if($this->conn = mysqli_connect($this->db_host, $this->db_user, $this->db_pass, $this->db_dbname))
			return true;
		else
			$bc_core->bcGetError("Database not connected.");
	}
	
	public function bcQuery( $qry = '' )
	{
		if($qry == '')
			return false;
		
		$res = mysqli_query( $this->conn, $qry ) or die(mysqli_error( $this->conn ));
		return $res;
	}
	
	public function bcFetchArray( $res = '' )
	{
		if($res == '')
			return false;
		
		$data = array();
		$data_cnt = 0;
		
		while( $row = mysqli_fetch_array( $res, MYSQLI_ASSOC ) )
		{
			$data[$data_cnt] = $row;
			$data_cnt++;
		}
		
		return $data;
	}
	
	public function bcFetchObject( $res = '' )
	{
		if($res == '')
			return false;
		
		$data = array();
		$data_cnt = 0;
		
		while( $row = mysqli_fetch_object( $res ) )
		{
			$data[$data_cnt] = $row;
			$data_cnt++;
		}
		
		return $data;
	}
	
	public function bcGetQuery( $qry = '' )
	{
		if($qry == '')
			return false;
		
		$res = $this->bcQuery( $qry );
		$data = $this->bcFetchArray( $res );
		
		return $data;
	}
	
	public function bcGetQueryObj( $qry = '' )
	{
		if($qry == '')
			return false;
		
		$res = $this->bcQuery( $qry );
		$data = $this->bcFetchObject( $res );
		
		return $data;
	}
	
	public function bcGetTable( $tbl = '' )
	{
		if($tbl == '')
			return false;
		
		$qry = "SELECT * FROM `".$tbl."`";
		$res = $this->bcQuery( $qry );
		$data = $this->bcFetchArray( $res );
		
		return $data;
	}
	
	public function bcLastInsert()
	{
		return mysqli_insert_id( $this->conn );
	}
}

$bc_db = new BC_Db(DB_HOST, DB_USER, DB_PASS, DB_DATABASE);
?>