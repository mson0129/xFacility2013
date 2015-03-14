<?php
//XFMySQL(XFObject>XFDB>XFMySQL)
//Michael Son(michaelson@nate.com)
//Jul.01.2012.

//Require_once

//Class
class XFMySQL extends XFDB {
	
	//Run
	function XFMySQL($query=NULL) {
		$this->XFDB();
		$this->connect();
		if(!is_null($query))
			$this->runQuery($query);
	}
	
	//Connect, Disconnect
	function connect() {
		$return = mysql_connect($this->server, $this->username, $this->password);
		if($return) {
			//Select database
			mysql_select_db($this->database, $return);
			$this->link = $return;
			return $this->link;
		} else {
			return false;
		}
	}
	function disconnect() {
		//Close a link;
		mysql_close($this->link);
		unset($this->link);
	}
	
	//Run
	function findTable($table) {
		$query = "select 1 from `$table`";
		if($this->link==NULL) {
			$this->connect();
		}
		$result = @mysql_query($this->query, $this->link);
		
		if($result !== FALSE) {
			return true;
		} else {
			return $result;
		}
	}
	function runQuery($query=NULL) {
		unset($this->counter, $this->result);
		if($query!=NULL) {
			$this->query = $query;
		} else if($this->query!=NULL) {
			//$this->query = $this->query;
		} else {
			//Nothing to do
			return false;
		}
		if($this->link==NULL) {
			$this->connect();
		}
		$return = @mysql_query($this->query, $this->link);
		$this->disconnect();
		if(!$return) {
			return false;
		}
		$this->result = $return;
		if(strtolower(substr($this->query, 0, strpos($this->query, " ")))=="select") {
			$this->counter = mysql_num_rows($return);	
		}
		return $this->result;
	}
	function getFieldName($table) {
		unset($this->counter, $this->result);
		$this->query = "SHOW FIELDS FROM `".$table."`";
		$this->runQuery();
		if($this->result) {
			$i=0;
			while ($row = mysql_fetch_array($this->result)) {
				$return[$i] = $row['Field'];
				$i++;
			}
		} else {
			$return = false;
		}
		unset($this->result);
		return $return;
	}
	function parseResult($fields = NULL) {
		/*
		DEVELOPMENT:
			Michael Son(michaelson@nate.com)
			30.May.2010.
		DESCRIPTION:
			Parse a result of query
		CALL:
			parse_result();
		RETURN:
			$array[0]['no'] = 3;
			$array[0]['indicator'] = 1349851283;
			$array[0]['status'] = 1;
			$array[0]['id'] = "root";
			$array[0]['pw'] = "*68F9CD57023F17CBE06EE6365D9B4FEBF3EB3EE4";
			$array[0]['etc'] = "lang=en,ko,jp,ch";
			$array[1]['no'] = 4;
			$array[1]['indicator'] = 1352878344;
			$array[1]['status'] = 1;
			$array[1]['id'] = "administrator";
			$array[1]['pw'] = "*1F7E399139C29C99909A2C7E8C56247043C4FEE1";
			$array[1]['etc'] = "lang=ko,en";
			$array = NULL //Error
		*/
		
		if(!$this->result) {
			return false;
		}
		//If the list of fields are missed,
		if($fields == NULL) {
			$counter = 0;
		} else {
			if(is_array($fields)) {
				$temp = $fields;
				$counter = count($fields) - 1;
			} else {
				//Parse fields by comma
				$temp = split(",", $fields);
				//Estimate times for a loop
				$counter = substr_count($fields, ",");
			}
		}
		for ($i=0; $i<=$counter; $i++) {
			//If the list of fields are missed,
			if($fields == NULL) {
				$field = @mysql_field_name($this->result, $i);
				//If there is no field name,
				if ($field == NULL) {
					//Stop this Loop
					break;
				} else {
					//One more time
					$counter++;	
				}
			} else {
				$field = $temp[$i];
			}
			//Estimate times for a subloop
			$counter2 = mysql_num_rows($this->result);
			for($j=0; $j<$counter2; $j++) {
				$return[$j][$field] = mysql_result($this->result, $j, $field);
			}
		}
		//Return Array
		return $return;
	}
}
?>
