<?php
//XFUser Class
//Michael Son(michaelson@nate.com)
//Jan.19.2013.
	//Require_once
	
	//Class
	class XFUser extends XFObject {
		var $no;
		var $status;
		var $id;
		var $password;
		var $etc;
		
		function signin() {
			if($this->id==NULL&&$this->password==NULL) {
				return false;
			} else {
				$return = $this->matchupID();
				if(!$return) {
					return "{XFUsers_WrongID}";
				} else {
					$return = $this->matchupPassword();
					if(!$return) {
						return "{XFUsers_WrongPassword}";
					} else {
						$return = $this->matchupActivation();
						if(!$return) {
							return "{XFUsers_NotActivated}";
						} else {
							$_SESSION['xf_users'][] = $return[0];
						}
					}
				}
			}
			return $return;
		}
		
		function isSignedIn() {
			$return = false;
			if($this->no==NULL) {
			} else {
				foreach($_SESSION['xf_users'] as $xfUser) {
					if($this->no == $xfUser['no']) {
						$return = true;
						break;
					}
				}
			}
			return $return;
		}
		
		function signup() {
			if($this->status==NULL) {
				$this->status = 0;
			}
			$database = new XFMySQL;
			$query = "INSERT INTO `xf_users` (
				`status` ,
				`id` ,
				`password` ,
				`etc`
			)
			VALUES (
				'".addslashes($this->status)."', '".addslashes($this->id)."', PASSWORD('".addslashes($this->password)."') , '".addslashes($this->etc)."'
			);";
			$database->runQuery($query);
		}
		function signout($id = NULL) {
			if(is_null($id)) {
				unset($_SESSION['xf_users']);
			} else {
				$counter = count($_SESSION['xf_users']);
				for($i=0; $i<$counter; $i++) {
					if($_SESSION['xf_users'][$i]['id'] == $id) {
						unset($_SESSION['xf_users'][$i]);
					}
				}
			}
		}
		
		//Activation, Deactivation
		function activate($code) {
			if(!is_array($code)) {
				$codes[] = $code;
			}
			foreach($codes as $code) {
				$decodes[] = XFUsers::decodeActivationCode($code);
			}
			foreach($decodes as $decode) {
				$database = new XFMySQL;
				$query = "UPDATE `xf_users` SET `status` = '2' WHERE `no` = ".$decode['no']." AND `id` = '".$decode['id']."';";
				$database->runQuery($query);
				$database->runQuery("SELECT * FROM `xf_users` WHERE `no`='".$decode['no']."' AND `id`='".$decode['id']."' AND `status` > 0;");
				if($database->counter != 1) {
					return false;
				}
				return $database->parseResult();
			}
		}
		function deactivate() {
			$database = new XFMySQL;
			$database->runQuery("UPDATE `xf_users` SET `status` = '0' WHERE `no` = ".$this->no); 
		}
		function encodeActivationCode($no = NULL) {
			$database = new XFMySQL;
			if(is_null($no)) {
				$no = $this->no;
			}
			$database->runQuery("SELECT * FROM `xf_users` WHERE `no`='".$no."'");
			if($database->counter != 1) {
				return false;
			}
			$results = $database->parseResult();
			
			$return = base64_encode("<row><column name='no'>".$results[0]['no']."</column><column name='id'>".$results[0]['id']."</column></row>");
			
			return $return;
		}
		function decodeActivationCode($code) {
			$code = base64_decode($code);
			
			$parser = xml_parser_create();
			xml_parse_into_struct($parser, $code, $struct, $index);
			xml_parser_free($parser);
			
			foreach($struct as $key => $value) {
				if($value['tag']=="COLUMN"&&$value['type']=="complete"&&$value['attributes']['NAME']=='no') {
					$return['no'] = $value['value']; 
				} else if($value['tag']=="COLUMN"&&$value['type']=="complete"&&$value['attributes']['NAME']=='id') {
					$return['id'] = $value['value'];
				}
			}
			
			return $return;
		}
		
		//Match Up
		function matchupID() {
			$database = new XFMySQL;
			$database->runQuery("SELECT * FROM `xf_users` WHERE `id`='".$this->id."';");
			if($database->counter != 1) {
				return false;
			}
			return $database->parseResult();
		}
		function matchupPassword() {
			$database = new XFMySQL;
			$database->runQuery("SELECT * FROM `xf_users` WHERE `id`='".$this->id."' AND `password`=password('".$this->password."');");
			if($database->counter != 1) {
				return false;
			}
			return $database->parseResult();
		}
		function matchupActivation() {
			$database = new XFMySQL;
			$database->runQuery("SELECT * FROM `xf_users` WHERE `id`='".$this->id."' AND `password`=password('".$this->password."') AND `status` > 0;");
			if($database->counter != 1) {
				return false;
			}
			return $database->parseResult();
		}
		
		//Format
		function formatDB() {
			$database = new XFMySQL;
			$database->runQuery("DESCRIBE `xf_users`;");
			if($database->result) {
				$database->runQuery("DROP TABLE `xf_users`");
			}
			$query = "CREATE TABLE `xf_users` (
				`no` INT( 255 ) NOT NULL AUTO_INCREMENT ,
				`status` INT( 1 ) NOT NULL DEFAULT '0',
				`id` VARCHAR( 255 ) NOT NULL ,
				`password` VARCHAR( 41 ) NOT NULL ,
				`etc` TEXT NOT NULL ,
				PRIMARY KEY ( `no` )
			) COMMENT = 'Users of xFacility'";
			$database->runQuery($query);
		}
	}
?>