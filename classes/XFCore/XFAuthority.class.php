<?php
//XFAuthority
//Michael Son(Studio2b)
//2012.Jul.18.
	//Require_once
	require_once ($_SERVER['DOCUMENT_ROOT'].'/xfacility/classes/XFIO/XFDB.class.php');

	//Class
	class XFAuthority extends XFLibrary {
		var $xfUser;
		var $usersNo, $how, $application, $table, $itemNo;
		
		function XFAuthority($how) {
			//$userNo
			if(is_null($_SESSION['xf']['xfcore']['users'])) {
				//Guest
				$this->usersNo = 0;
			} else {
				/*
				$_SESSION['xf']['xfcore']['users'][0]['no'] = 123;
				$_SESSION['xf']['xfcore']['users'][1]['no'] = 234;
				*/
				
				$this->usersNo = $_SESSION['xf']['xfcore']['users'];
			}
			
			//$how
			if($how=="create"||$how=="modify"||$how=="delete"||$how=="browse") {
				//OK
				$this->how = $how;
			} else {
				//Error
				$this->how = false;
			}
		}
		
		function check() {
			$db = new XFMySQL();
			$db->query = "";
			
			//return true||false
			return $return;
		}
		function prohibit() {
			return $return;
		}
		function permit() {
			return $return;
		}
		
		function formatDB() {
			$database = new XFMySQL;
			$database->runQuery("DESCRIBE `xf_authority`;");
			if($database->result) {
				$database->runQuery("DROP TABLE `xf_authority`");
			}
			/*
			$query = "CREATE TABLE `xf_authority` (
			`no` INT( 255 ) NOT NULL AUTO_INCREMENT ,
			`status` INT( 1 ) NOT NULL DEFAULT '0',
			`id` VARCHAR( 255 ) NOT NULL ,
			`password` VARCHAR( 41 ) NOT NULL ,
			`etc` TEXT NOT NULL ,
			PRIMARY KEY ( `no` )
			) COMMENT = 'Users of xFacility'";
			*/
			$database->runQuery($query);
		}
	}
?>
