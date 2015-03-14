<?php
//XFRange Class
//Michael Son(michaelson@nate.com)
//Apr.02.2013.
//May.17.2013. - Adopt a new xFArray Specification.
	//Require_once
	
	//Class
	class XFRange extends XFObject {
		var $range;
		var $array;
		var $exception;
		
		function XFRange($range) {
			$this->range = trim($range);
			$this->range = str_replace(" ", "", $this->range);
			$this->range = str_replace("\n", "", $this->range);
			if(substr($this->range, -1)==";"||substr($this->range, -1)==",")
				$this->range = substr($this->range, 0, -1);
		}
		
		function rangeToArray() {
			//Parse
			$number = split("[,;]", $this->range);
			
			foreach($number as $value) {
				if(strpos($value, "-")===false) {
					$this->array[] = $value;
				} else {
					if(substr($value, 0, 1)=="-") {
						if(strpos(substr($value, 1), "-")===false) {
							//-12
							$this->exception[] = substr($value, 1);
						} else {
							//-12-14
							$subRange = split("-", substr($value, 1));
							sort($subRange);
							for($i=$subRange[0]; $i<=$subRange[1]; $i++){
								$this->exception[] = $i;
							}
							unset($subRange);
						}
					} else {
						//11-20
						$subRange = split("-", $value);
						sort($subRange);
						for($i=$subRange[0]; $i<=$subRange[1]; $i++){
							$this->array[] = $i;
						}
						unset($subRange);
					}
				}
			}
			
			//Optimizing
			if(!is_null($this->exception)) {
				$this->exception = array_unique($this->exception);
				$totalArray = array_merge($this->array, $this->exception);
				$totalArray = array_unique($totalArray);
				$this->array = array_diff($totalArray, $this->exception);
			} else {
				$this->array = array_unique($this->array);
			}
			
			sort($this->array);
			
			$return = $this->array;
			return $return; 
		}
		
		function rangeToXFArray($table) {
			$this->rangeToArray();
			
			foreach($this->array as $key => $value) {
				$temp[$table][$key]['no'] = $value;
			}
			
			$return = $temp;
			return $return;
		}
	}
?>