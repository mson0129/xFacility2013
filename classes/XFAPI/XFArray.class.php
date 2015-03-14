<?php
//XFArray Class
//Michael Son(michaelson@nate.com)
//Jan.27.2013.
//May.18.2013. - Adopt a new xFArray Specification.
	//Require_once
	
	//Class
	class XFArray {
		/*
		$xfArray['tableName']['rowNumber']['columnName'] = $value;
		
		EXAMPLE:
		$xfArray['xfUsers'][0]['id'] = "kenya";
		$xfArray['xfUsers'][0]['passowrd'] = "dutchCoffee";
		*/
		var $table, $row, $column;
		var $address, $xfArray;
		
		function XFArray($xfArray = NULL) {
			if(!is_null($xfArray)||is_array($xfArray)) {
				$this->xfArray = $xfArray;
			}
		}
		
		//Set
		function setValue($value) {
			$this->xfArray[$this->table][$this->row][$this->column] = $value;
			
			return $this->getAddress();
		}
		
		function setAddress($var1, $var2 = NULL, $var3 = NULL) {
			if(is_array($var1)&&(!is_null($var1['table'])&&!is_null($var1['row'])&&!is_null($var1['column']))) {
				$this->table = $var1['table'];
				$this->row = $var1['row'];
				$this->column = $var1['column'];
				$return = $this->getAddress();
			} else if(!is_null($var1)&&!is_null($var2)&&!is_null($var3)) {
				$this->table = $var1;
				$this->row = $var2;
				$this->column = $var3;
				$return = $this->getAddress();
			} else {
				$return = true;
			}
			
			return $return;
		}
		
		//Get
		function getValue() {
			$return = $this->xfArray[$this->table][$this->row][$this->column];
			return $return;
		}
		
		function getAddress() {
			$this->address['table'] = $this->table;
			$this->address['row'] = $this->row;
			$this->address['column'] = $this->column;
			
			$return = $this->address;
			return $return;
		}
		
		//Convert
		function postToXFArray() {
			/*
			Michael Son(michaelson@nate.com)
			May.10.2013.
			
			VIEW:
			<form target='example.php' method='post'>
				<input type='text' name='xfUsers[id][]' /><br />
				<input type='text' name='xfUsers[password][]' /><br />
				<input type='text' name='xfUsers[id][]' /><br />
				<input type='text' name='xfUsers[password][]' /><br />
				<br />
				<input type='submit' />
			</form>
			
			INPUT:
				$post['xfUsers']['id'][0] = "michaelson";
				$post['xfUsers']['password'][0] = "password";
				$post['xfUsers']['id'][1] = "root";
				$post['xfUsers']['password'][1] = "alpine";
			
			RETURN:
				$return['xfUsers'][0]['id'] = "michaelson";
				$return['xfUsers'][0]['password'] = "password"; 
				$return['xfUsers'][1]['id'] = "root";
				$return['xfUsers'][1]['password'] = "alpine";
			*/
			
			
			if(is_array($_POST)) {
				foreach($_POST as $table => $columns) {
					//$table is a tableName.
					//$columns must be a array.
					if(is_array($columns)) {
						foreach($columns as $column => $rows) {
							//$rows must be a array.
							if(is_array($rows)) {
								foreach($rows as $row => $value) {
									if(!is_null($value)) {
										$return[$table][$row][$column] = $value;
									}
								}
							} else {
								return true;
							}
						}
						unset($column, $rows);
					} else {
						return true;
					}
				}
				unset($table, $columns);
			} else {
				return true;
			}
			unset($key, $value);
			return $return;
		}
		
		function xfArrayToHTMLTable() {
			if(is_null($this->xfArray)) {
				$return = true;
			} else {
				foreach($this->xfArray as $table => $rows) {
					if(is_array($rows)) {
						ksort($rows);
						foreach($rows as $row => $columns) {
							foreach($columns as $column => $value) {
								$totalColumn[] = $column;
							}
						}
						unset($row, $columns, $column, $value);
						$totalColumn = array_unique($totalColumn);
						natcasesort($totalColumn);
						
						$return = "<table>\n";
						$return .= "\t<tr>\n";
						$return .= "\t\t<th>Row</th>\n";
						foreach($totalColumn as $column) {
							$return .= "\t\t<th>".$column."</th>\n";
						}
						$return .= "\t</tr>\n";
						foreach($rows as $row => $columns) {
							$return .= "\t<tr>\n";
							$return .= "\t\t<td>$row</td>\n";
							foreach($totalColumn as $column) {
								$return .= "\t\t<td>".$columns[$column]."</td>\n";
							}
							$return .= "\t</tr>\n";
						}
						$return .= "</table>\n";
					} else {
						print_r($this->xfArray);
					}
				}
			}
			return $return;
		}
		
		/*
		function arrayToXFArray() {
			if(is_null($this->array)) {
				$return = false;
			} else {
				$depth = 0;
				
				if(is_array($this->array)) {
					$depth++;
					$temp[$depth] = $this->array;
					$keys[$depth] = array_keys($this->array);
					$order[$depth] = 0;
					$return = "{";
					
					//Depth Loop
					for($i=0; $i<count($temp[$depth]); $i++) {
						print_r($temp[$depth]);
						echo "<br />\n";
						echo "<br />\n";
						$key = $keys[$depth][$i];
						if(is_array($temp[$depth][$key])) {
							$depth++;
							$temp[$depth] = $temp[$depth-1][$key];
							$keys[$depth] = array_keys($temp[$depth]);
							$order[$depth-1] = $i;
							$order[$depth] = 0;
							$i=-1;
							$return .= $key.":{";
						} else {
							$return .= $key.":".$temp[$depth][$key].";";
						}
						if($i==count($temp[$depth])-1) {
							$return .= "};";
							$depth--;
							if($depth!=0) {
								unset($temp[$depth+1]);
								unset($order[$depth+1]);
								$i=$order[$depth];
							}
						}
						//Value Loop
					}
					$return .= "}";
				} else {
					$return = false;
				}
			}
			if($return!=false) {
				$this->xfArray = $return;
			}
			return $return;
		}
		
		//Is it?
		function isXFArray() {
			if(is_null($this->xfArray)) {
				if(is_null($this->array)) {
					$return = false;
				} else {
					$this->arrayToXFArray();
					$return = true;
				}
			}
			$return = true;
		}
		*/
	}
?>