<?php
//XFString Class
//Michael Son(michaelson@nate.com)
//Apr.2.2013.
//May.18.2013. - Renamed methods shortly & Added correct method.
	//Require_once
	
	//Class
	class XFString {
		var $string;
		var $length;
		
		function XFString($string) {
			//$this->string = "HelloWorld";
			//$this->length = 10;
			
			$this->string = $string;
			$this->length = strlen($string);
		}
		
		function correct($start = NULL, $len = NULL) {
			if(is_null($start)) {
				$start = 0;
			}
			if(is_null($len)) {
				$len = $this->length;
			}
			
			//Correcting $len
			if($start >= 0 && $len > 0 &&$start+$len>$this->length) {
				/*
				$start = 0;
				$len = 11;
				RETURN:
				$len = 10;
				*/
				$len = $this->length - $start;
			} else if($start < 0 && $len > -$start) {
				/*
				$start = -3;
				$len = 4;
				RETURN:
				$len = 3;
				*/
				$len = -$start;
			} else if($start < 0 && $this->length + $start < 0) {
				/*
				$start = -11
				RETURN:
				$start = 0;
				$len = 10;
				*/
				$start = 0;
				$len = $this->length;
			}
				
			//Error
			else if($start < 0 && $len < $start) {
				/*
				$start = -3;
				$len = -4;
				RETURN:
				$start = 0;
				$len = 0;
				*/
				$start = 0;
				$len = 0;
			} else if($start >= 0 && $start>$this->length) {
				/*
				$start = 11;
				RETURN:
				$start = 0;
				$len = 0;
				*/
				$start = 0;
				$len = 0;
			} else if($start >= 0 && $len < 0 && $this->length - $start + $len < 0) {
				/*
				$start = 0;
				$len = -11;
				RETURN:
				$start = 0;
				$len = 0;
				*/
				$start = 0;
				$len = 0;
			}
			
			//Correcting Minus
			if($start < 0)
				$start = $this->length + $start;
			if($len < 0)
				$len = $this->length + $len;
			
			$return[0] = $start;
			$return[1] = $len;
			
			return $return;
		}
		
		function upper($start = NULL, $len=NULL) {
			$temp = $this->correct($start, $len);
			$start = $temp[0];
			$len = $temp[1];
			
			//0, 1 .H.elloWorld
			//0, -9 .H.elloWorld
			//-10, 1 .H.elloWorld
			//-10, -9 .H.elloWorld
			$first = substr($this->string, 0, $start);
			$mid = strtoupper(substr($this->string, $start, $len));
			$last = substr($this->string, $start+$len);
			$this->string = $first.$mid.$last;
			$return = $this->string;
			
			return $return;
		}
		
		function lower($start = NULL, $len = NULL) {
			$temp = $this->correct($start, $len);
			$start = $temp[0];
			$len = $temp[1];
				
			//0, 1 .h.elloWorld
			//0, -9 .h.elloWorld
			//-10, 1 .h.elloWorld
			//-10, -9 .h.elloWorld
			$first = substr($this->string, 0, $start);
			$mid = strtolower(substr($this->string, $start, $len));
			$last = substr($this->string, $start+$len);
			$this->string = $first.$mid.$last;
			$return = $this->string;
				
			return $return;
		}
	}
?>