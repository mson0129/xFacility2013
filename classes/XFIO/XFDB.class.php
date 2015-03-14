<?php
//XFDB(XFObject>XFDB)
//Michael Son(michaelson@nate.com)
//1.0.0.
//Dec.24.2012.

//Require_once
require_once ($_SERVER['DOCUMENT_ROOT'].'/xfacility/classes/XFIO/XFMySQL.class.php');

//Class
class XFDB extends XFObject {
	//Values
	var $link;
	var $query;
	//Result
	var $result;
	var $counter;
	
	//Settings
	var $kind; //MySQL, MSSQL, ETC...
	var $server;
	var $database;
	var $username;
	var $pw;
	var $prefix;
	
	function XFDB() {
		require ($_SERVER['DOCUMENT_ROOT'].'/xfacility/configs/database.php');
		$this->kind = $xf_db['kind'];
		$this->server = $xf_db['server'];
		$this->database = $xf_db['database'];
		$this->username = $xf_db['username'];
		$this->password = $xf_db['password'];
		$this->prefix = $xf_db['prefix'];
	}
}
?>