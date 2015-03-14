<?php
//XFFile(XFObject>XFIOFile)
//Michael Son(michaelson@nate.com)
//1.0.0.
//2013JUL06

//Require_once

class XFIOFile extends XFObject {
	var $xfPath;
	var $path;
	var $dir;
	var $basename;
	var $extension;
	
	var $hash;
	var $mime;
	
	function XFIOFile($xfPath) {
		$this->xfPath = $xfPath;
		$this->path = $_SERVER['DOCUMENT_ROOT'].$xfPath;
		if(file_exists($this->path)) {
			$pathinfo = pathinfo($this->xfPath);
			$this->dir = $pathinfo['dirname'];
			$this->basename = $pathinfo['basename'];
			$this->extension = $pathinfo['extension'];
			
			$this->hash = md5(base64_decode($this->readFile(true)));
			$this->mime = exec("file -bi '$this->path'");
		} else if(is_dir($this->path)) {
			if(substr($this->path, -1) == "/") {
				$this->path = substr($this->path, 0, -1);
			} 
			$this->dir = $this->path;
		} else {
			
		}
	}
	
	function readFile($base64 = NULL) {
		if(file_exists($this->path)) {
			//implode
			$return = implode("", file($this->path));
			
			/*fread
			$fileHandle = fopen($this->path, "r");
			$return = fread($fileHandle, filesize($this->path));
			fclose($fileHandle);
			*/
		} else {
			$return = NULL;
		}
		
		if($base64==true)
			$return = base64_encode($return);
		
		return $return;
	}
	
	function createFile($str) {
		if(!file_exists($this->path)) {
			$fileHandle = fopen($this->path, "w");
			fwrite($fileHandle, $str);
			fclose($fileHandle);
			$return = false;
		} else {
			$return = true;
		}
		return $return;
	}
	
	function modifyFile($str) {
		if(file_exists($this->path)) {
			$this->deleteFile();
			$this->createFile($str);
		} else {
			$return = true;
		}
		return $return;
	}
	
	function remaneFile($basename, $dir = NULL) {
		if(file_exists($this->path)) {
			if(is_null($dir))
				$dir = $this->dir;
			rename($this->path, $_SERVER['DOCUMENT_ROOT'].$dir."/".$basename);
		} else {
			$return = true;
		}
		return $return;
	}
	
	function deleteFile() {
		if(file_exists($this->path)) {
			unlink($this->path);
			$return = false;
		} else {
			$return = true;
		}
		return $return;
	}
}
?>