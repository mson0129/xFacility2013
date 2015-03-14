<?php
//XFFile(XFObject>XFIODirectory)
//Michael Son(michaelson@nate.com)
//1.0.0.
//2013JUL06.

//Require_once

class XFIODirectory extends XFObject {
	var $xfPath;
	var $path;
	
	function XFIODirectory($xfPath) {
		if(substr($xfPath, -1)=="/")
			$xfPath = substr($xfPath, 0, -1);
		$this->xfPath = $xfPath;
		$this->path = $_SERVER['DOCUMENT_ROOT'].$this->xfPath;
		if(is_dir($this->path)) {
			
		}
	}
	
	function readDirectory() {
		if(is_dir($this->path)) {
			$handle = dir($this->path);
			while (false !== ($entry = $handle->read())) {
				$return[$i] = $entry;
				$i++;
			}
			$handle->close();
		}
		sort($return);
		reset($return);
		return $return;
	}
	
	function browseDirectory() {
		if(is_dir($this->path)) {
			$handle = opendir($this->path);
		} else {
			$handle = opendir(dirname($this->path));
		}
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				if(is_dir($this->path.'/'.$file))
					$return[] = $file;
			}
		}
		closedir($handle);
		return $return;
	}
	
	function listDirectory() {
		$this->browseDirectory();
	}
	
	function createDirectory() {
		if(!is_dir($path)) {
			mkdir($path, 0777);
		}
	}
	
	function modifyDirectory($dir) {
		$this->renameDirectory($dir);
	}
	
	function renameDirectory($dir) {
		if(file_exists($this->path)) {
			if(is_null($dir))
				$dir = $this->dir;
			rename($this->path, $_SERVER['DOCUMENT_ROOT'].$dir."/".$basename);
		} else {
			$return = true;
		}
		return $return;
	}
	
	function deleteDirectory() {
		rmdir($this->path);
	}
}