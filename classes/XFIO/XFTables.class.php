<?php
//XFTables(XFObject>XFLibrary)
//Michael Son(michaelson@nate.com)
//Dec.24.2012. Made this new class
//May.10.2013. Adopt a new XFArray Specification.
//May.13.2013. Adopt a new XFacility(SQL Table Tree) Specification.
//May.18.2013. Renamed this class.(XFLibrary -> XFTable)
//May.21.2013. Added a method - browseRows
//May.22.2013. Updated browseRows method.
	//Require_once
	require_once ($_SERVER['DOCUMENT_ROOT'].'/xfacility/classes/XFIO/XFDB.class.php');
	
	//Class
	class XFTables extends XFObject {
	var $no;
	var $application;
	var $xfArray; //Not Class, Just Array
	
	function XFTables($application) {
		$this->application = $application;
	}
	
	function renameTable($table) {
		$string = new XFString($table);
		$db = new XFMySQL();
		//May.13.2013.
		//$return = "xf".$string->makeCapital(0, 1); //xfLibrary
		$return = $db->prefix."_".$this->application."_".$string->lower(); //xf_AppName_Table
		return $return;
	}
	
	//Tables
	//Columns
	
	//Rows
		//Automatically create, modify, delete rows.
		function doRows() {
			$temp = $this->xfArray;
			
			if(!is_null($temp)) {
				foreach($temp as $table => $rows) {
					unset($this->xfArray);
					foreach ($rows as $row => $columns) {
						$this->xfArray[$table][0] = $columns;
						if(is_null($columns['no'])&&count($columns)>0) {
							$this->createRows();
						} else if(!is_null($columns['no'])&&count($columns)==1) {
							$this->deleteRows();
						} else {
							$this->modifyRows();
						}
					}
				}
				$return = false;
			}
			
			$this->xfArray = $temp;
			
			return $return;
		}
		
		function createRows() {
			$db = new XFMySQL;
			
			//May.10.2013.
			if(is_null($this->xfArray)) {
				$return = true;
			} else {
				foreach($this->xfArray as $table => $rows) {
					//Table
					//Select Table Directly
					$fields = $db->getFieldName($table); //Dangerous for security.
					if(!is_array($fields)) {
						
						$renamedTable = $this->renameTable($table);
						$fields = $db->getFieldName($renamedTable);
					}
					if(!is_array($fields)) {
						//There is no table which has a given name.
						echo "There is no table: ".$table;
						continue;
					} else if(!is_null($renamedTable)) {
						$table = $renamedTable;
					}
					
					foreach($rows as $row => $columns) {
						//Meta Data(non-row)
						if(!is_numeric($row))
							continue;
						
						//Unset
						unset($values);
						unset($fieldName);
						unset($fieldValue);
						
						//Parse data in columns
						foreach($columns as $column => $value) {
							foreach($fields as $field) {
								//Input Data
								if($column=="etc") {
									if(substr(trim($value), -1) != ";") {
										$value = trim($value).";";
									}
									$values['etc'] = trim(trim($value)."\n".$values['etc']);
									break;
								} else if($field==$column) {
									$values[$field] = $value;
									break;
								} else if($field=="etc") {
									$values['etc'] .= $column.":".$value.";\n";
									trim($values['etc']);
								}
							}
							unset($field);
						}
						
						//Write & Run a Query
						foreach($fields as $field) {
							if(!is_null($values[$field])) {
								$fieldName .= "`".$field."`,";
								$fieldValue .= "'".$values[$field]."',";
							}
						}
						
						$db->query = "INSERT INTO `".$table."` (".substr($fieldName, 0, -1).") VALUES (".substr($fieldValue, 0, -1).");\n";
						$db->runQuery();
					}
				}
				$return = false;
			}
			
			/*Dec.24.2012.
			//table
			if(!is_null($this->xfArray['table'])) {
				$table = $this->xfArray['table'];
			} else {
				$table = $this->table;
			}
			
			$fields = $db->getFieldName($table);
			
			if(is_null($this->xfArray)) {
				$return = true;
			} else {
				
				foreach($this->xfArray as $key => $row) {
					if(!is_numeric($key))
						continue;
					unset($values);
					unset($fieldName);
					unset($fieldValue);
					foreach($row as $column => $value) {
						foreach($fields as $field) {
							if($column=="etc") {
								if(substr(trim($value), -1) != ";") {
									$value = trim($value).";";
								}
								$values['etc'] = trim(trim($value)."\n".$values['etc']);
								break;
							} else if($field==$column) {
								$values[$field] = $value;
								break;
							} else if($field=="etc") {
								$values['etc'] .= $column.":".$value.";\n";
								trim($values['etc']);
							}
						}
						unset($field);
					}
					foreach($fields as $field) {
						if(!is_null($values[$field])) {
							$fieldName .= "`".$field."`,";
							$fieldValue .= "'".$values[$field]."',";
						}
					}
					$db->query = "INSERT INTO `".$table."` (".substr($fieldName, 0, -1).") VALUES (".substr($fieldValue, 0, -1).");\n";
					$db->runQuery();
				}
				$return = false;
			}
			*/
			
			return $return;
		}
		
		function modifyRows() {
			$db = new XFMySQL;
			
			//Apr.10.2013.
			if(is_null($this->xfArray)) {
				$return = true;
			} else {
				foreach($this->xfArray as $table => $rows) {
					//Table
					//Select Table Directly
					$fields = $db->getFieldName($table); //Dangerous for security.
					if(!is_array($fields)) {
						
						$renamedTable = $this->renameTable($table);
						$fields = $db->getFieldName($renamedTable);
					}
					if(!is_array($fields)) {
						//There is no table which has a given name.
						echo "There is no table: ".$table;
						continue;
					} else if(!is_null($renamedTable)) {
						$table = $renamedTable;
					}
					
					//foreach($this->xfArray as $key => $row) {
					foreach($rows as $row => $columns) {
						if(!is_numeric($row))
							continue;
						unset($column, $set, $where, $values);
						
						if(!is_null($columns['no'])) {
							//Get Etc
							$where = "`no` = '".$columns['no']."'";
							$db->query = "SELECT * FROM `".$table."` WHERE ".$where." LIMIT 1;\n";
							$db->runQuery();
							$xfArray[$table] = $db->parseResult();
							$tempEtc = trim($xfArray[$table][0]['etc']);
							if(substr($tempEtc, -1) == ";") {
								$tempEtc = substr($tempEtc, 0, -1);
							}
							$etcs = split(";", $tempEtc);
							foreach($etcs as $etc) {
								list($etcKey, $etcValue) = explode(':', $etc);
								$values['etc'][trim($etcKey)] = trim($etcValue);
							}
							
							//Parsing
							foreach($columns as $column => $value) {
								foreach($fields as $field) {
									if($column == "etc") {
										$tempEtc = trim($value);
										if(substr($tempEtc, -1) == ";") {
											$tempEtc = substr($tempEtc, 0, -1);
										}
										$etcs = split(";", $tempEtc);
										foreach($etcs as $etc) {
											list($etcKey, $etcValue) = explode(':', $etc);
											$after[trim($etcKey)] = trim($etcValue);
										}
										$values['etc'] = array_merge($values['etc'], $after); 
										break;
									} else if($column == $field) {
										$values[$column] = $value;
										break;
									} else if($field == "etc") {
										echo "CUSTOM:<br />\n";
										$values['etc'][trim($column)] = trim($value);
										print_r($values['etc']);
										echo "<br />\n";
									}
								}
							}
							unset($temp, $column, $value);
							
							//
							foreach($values as $field => $value) {
								if($field == "etc") {
									unset($temp, $value);
									foreach($values['etc'] as $column => $temp) {
										$value .= $column.":".$temp.";\n";
									}
									trim($value);
								}
								
								if(is_null($set)) {
									$set = "`".$field."` = '".$value."'";
								} else {
									$set .= ", `".$field."` = '".$value."'";
								}
							}
							
							$db->query = "UPDATE `".$table."` SET ".$set." WHERE ".$where." LIMIT 1;\n";
							echo $db->query;
							$db->runQuery();
						}
					}
				}
				$return = false;
			}
			
			/*
			//Dec.24.2012.
			//table
			if(!is_null($this->xfArray['table'])) {
				$table = $this->xfArray['table'];
			} else {
				$table = $this->table;
			}
			
			$fields = $db->getFieldName($table);
			
			if(is_null($this->xfArray)) {
				$return = true;
			} else {
				foreach($this->xfArray as $key => $row) {
					if(!is_numeric($key))
						continue;
					unset($columns, $set, $where);
					if(!is_null($row['no'])) {
						//set
						foreach($row as $columnName => $columnValue) {
							foreach($fields as $field) {
								if($columnName == "etc") {
									if(substr(trim($columnValue), -1) != ";") {
										$columnValue = trim($columnValue).";";
									}
									$columns['etc'] = trim(trim($columnValue)."\n".$columns['etc']);
									break;
								} else if($columnName == $field) {
									$columns[$columnName] = $columnValue;
									break;
								} else if($field == "etc") {
									$columns['etc'] .= $columnName.":".$columnValue.";\n";
									trim($columns['etc']);
								}
							}
						}
						
						foreach($columns as $name => $value) {
							if(is_null($set)) {
								$set = "`".$name."` = '".$value."'";
							} else {
								$set .= ", `".$name."` = '".$value."'";
							} 
						}
					
						//where
						$where = "`no` = '".$row['no']."'";
						
						$db->query = "UPDATE `".$table."` SET ".$set." WHERE ".$where." LIMIT 1;\n";
						
						$db->runQuery();
						
						
					}
				}
				$return = false;
			}
			*/
			
			return $return;
		}
		
		function deleteRows($ranges = NULL) {
			$db = new XFMySQL;
			
			//range
			if(!is_null($ranges)) {
				$xfRanges = new XFRanges($ranges);
				$xfArray = $xfRanges->rangesToXFArray();
			} else {
				$xfArray = $this->xfArray;
			}
			
			if(is_null($xfArray)) {
				$return = true;
			} else {
				foreach($xfArray as $table => $rows) {
					//Table
					//Select Table Directly
					$fields = $db->getFieldName($table); //Dangerous for security.
					if(!is_array($fields)) {
						$renamedTable = $this->renameTable($table);
						$fields = $db->getFieldName($renamedTable);
					}
					if(!is_array($fields)) {
						//There is no table which has a given name.
						echo "There is no table: ".$table;
						continue;
					} else if(!is_null($renamedTable)) {
						$table = $renamedTable;
					}
					
					unset($where);
					foreach($rows as $row => $columns) {
						if(!is_null($columns['no'])) {
							if(is_null($where)) {
								$where = "`no` = '".$columns['no']."'";
							} else {
								$where .= " OR `no` = '".$columns['no']."'";
							}
						}
					}
					$db->query = "DELETE FROM `".$table."` WHERE ".$where.";\n";
					$db->runQuery();
				}
				
				$return = false;
			}
			return $return;
		}
		
		function browseRows($ranges = NULL) {
			$db = new XFMySQL();
			
			//Range
			if(!is_null($ranges)) {
				$xfRanges = new XFRanges($ranges);
				$xfArray = $xfRanges->rangesToXFArray();
			} else {
				$xfArray = $this->xfArray;
			}
			
			foreach($xfArray as $table => $rows) {
				//Table
				//Select Table Directly
				$fields = $db->getFieldName($table); //Dangerous for security.
				if(!is_array($fields)) {
					$renamedTable = $this->renameTable($table);
					$fields = $db->getFieldName($renamedTable);
				}
				if(!is_array($fields)) {
					//There is no table which has a given name.
					echo "There is no table: ".$table;
					continue;
				} else if(!is_null($renamedTable)) {
					$table = $renamedTable;
				}
				
				unset($where);
				for($i=0; $i<count($rows); $i++) {
					//foreach
					$row = $i;
					$columns = $rows[$i];
					if(!is_array($columns))
						continue;
					
					unset($subWhere);
					
					//where
					if(!is_null($where))
						$where .= "\n OR ";
					$where .= "(";
					foreach($columns as $column => $value) {
						foreach ($fields as $field) {
							if($column == "etc") {
								if(substr(trim($value), -1) == ";") {
									$value = substr(trim($value), 0, -1);
								} else {
									$value = trim($value);
								}
								$temps = explode(";", $value);
								
								foreach($temps as $temp) {
									list($tempColumn, $tempValue) = explode(":", $temp);
									if(!is_null($subWhere))
										$subWhere .= " AND ";
									$subWhere .= "`etc` LIKE '%".$tempColumn.":".$tempValue."%'";
								}
								break;
							} else if($column == $field) {
								//condition
								if($rows[$column]=="like") {
									$condition = "LIKE";
									$value = "'%".$value."%'";
								} else if($rows[$column]=="password") {
									$condition = "=";
									$value = "password('".$value."')";
								} else {
									$condition = "=";
									$value = "'".$value."'";
								}
								
								if(!is_null($subWhere))
									$subWhere .= " AND ";
								$subWhere .= "`".$column."` ".$condition." ".$value."";
								break;
							} else if($field == "etc") {
								if(!is_null($subWhere))
									$subWhere .= " AND ";
								$subWhere .= "`etc` LIKE '%".$column.":".$value."%'";
							}
						}
					}
					$where .= $subWhere.")";
				}
				
				$db->query = "SELECT * FROM `".$table."` WHERE ".$where.";\n";
				//echo $db->query;
				$db->runQuery();
				$return[$table] = $db->parseResult();
			}
			return $return;
		}
}
?>