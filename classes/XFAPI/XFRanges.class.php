<?php
//XFRanges Class
//Michael Son(michaelson@nate.com)
//May.17.2013. - Adopt a new xFArray specification.
	//Require_once
	
	//Class
	class XFRanges extends XFObject {
		var $ranges;
		var $array;
		var $exception;
		
		function XFRanges($ranges) {
			$this->ranges = trim($ranges);
			$this->ranges = str_replace(" ", "", $this->ranges);
			$this->ranges = str_replace("\n", "", $this->ranges);
			if(substr($this->ranges, -1)==";"||substr($this->ranges, -1)==","||substr($this->ranges, -1)==":")
				$this->ranges = substr($this->ranges, 0, -1);
		}
		
		function rangesToArray() {
			$ranges = explode(";", $this->ranges);
			/*
			EXAMPLE
			$ranges[0] = "xfDocuNumber:1,2,3,4,5";
			$ranges[1] = "xfItemNumber:1-15,-6,-7,-8,-9,-10,-11,-12,-13,-14,-15";
			$ranges[2] = "10-20,-13-14";
			*/
			
			//Parsing
			$count = 0;
			foreach($ranges as $range) {
				//Table
				if(strpos($range, ":")===false) {
					$table = $count;
					$numbers = $range;
					$count++;
				} else {
					$parts = split(":", $range);
					$table = $parts[0];
					$numbers = $parts[1];
				}
				
				//Numbers
				$number = split(",", $numbers);
				foreach($number as $value) {
					if(strpos($value, "-")===false) {
						$this->array[$table][] = $value;
					} else {
						if(substr($value, 0, 1)=="-") {
							if(strpos(substr($value, 1), "-")===false) {
								//-12
								$this->exception[$table][] = substr($value, 1);
							} else {
								//-12-14
								$subRange = split("-", substr($value, 1));
								sort($subRange);
								for($i=$subRange[0]; $i<=$subRange[1]; $i++){
									$this->exception[$table][] = $i;
								}
								unset($subRange);
							}
						} else {
							$subRange = split("-", $value);
							sort($subRange);
							for($i=$subRange[0]; $i<=$subRange[1]; $i++){
								$this->array[$table][] = $i;
							}
							unset($subRange);
						}
					}
				}
				unset($table, $numbers, $number);
			}	
			
			//Optimizing
			foreach($this->array as $table => $numbers) {
				$unique_array[$table] = array_unique($numbers);
				if(!is_null($this->exception[$table])) {
					$unique_exception[$table] = array_unique($this->exception[$table]);
					$mergedArray[$table] = array_merge($numbers, $unique_exception[$table]);
					$mergedArray[$table] = array_unique($mergedArray[$table]);
					$temp[$table] = array_diff($mergedArray[$table], $unique_exception[$table]);
				} else {
					$temp[$table] = $unique_array[$table];
				}
				sort($temp[$table]);
			}
			
			unset($table, $numbers, $this->array);
			$this->array = $temp;
			$return = $this->array;
			
			return $return;
		}
		
		function rangesToXFArray() {
			$this->rangesToArray();
			
			foreach($this->array as $table => $values) {
				foreach($values as $key => $value) {
					$temp[$table][$key]['no'] = $value;
				}
			}
			$return = $temp;
			
			return $return;
		}
	}
