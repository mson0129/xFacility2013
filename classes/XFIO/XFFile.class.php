<?php
//XFFile(XFObject>XFFile)
//Michael Son(michaelson@nate.com)
//1.0.0.
//2013.Jan.17.

//Require_once


//Class
class XFFile extends XFObject {
	var $path;
	
	function initPath($xfPath = NULL) {
		if($xfPath==NULL)
			$path = $this->path;
		$path = $_SERVER['DOCUMENT_ROOT'].$xfPath;
		return $path;
	}
	
	function pathInfo($xfPath)
	{
		$pathinfo = pathinfo($xfPath);
	
		if(!isset($pathinfo['filename']))
		{
			$pathinfo['filename'] = substr($pathinfo['basename'], 0, strrpos($pathinfo['basename'], '.'));
		}
	
		return $pathinfo;
	}
	
	//File
	function saveFile($xfPath) {
		$fileHandle = fopen($xfPath, "wb+");
		if(flock($fileHandle, LOCK_EX)) {
			fwrite($fileHandle, $data) or die("fwrite failed");
			flock($$fileHandle, LOCK_UN);
		}
		fclose($fileHandle) or die("fclose failed");
			
		return $xfPath;
	}
	
	function listFile($xfPath = NULL, $includePath = false) {
		$path = XFFile::initPath($xfPath);
		if(is_dir($path)) {
			$handle = opendir($path);
		} else {
			$handle = opendir(dirname($path));
		}
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				if(file_exists($path.'/'.$file)) {
					if($includePath==true) {
						$return[] = $xfPath.$file;
					} else {
						$return[] = $file;
					}
				}
			}
		}
		closedir($handle);
		return $return;
	}
	
	function readFile($xfPath = NULL) {
		$path = XFFile::initPath($xfPath);
		if(file_exists($path)) {
			$return = implode("", file($path));
		} else {
			$return = NULL;
		}
		return $return;
	}
	
	function deleteFile($xfPath = NULL) {
		$path = XFFile::initPath($xfPath);
		if(file_exists($path)) {
			unlink($path);
			$return = true;
		} else {
			$return = false;
		}
		return $return;
	}
	
	//Directory
	function createDirectory($xfPath = NULL) {
		$path = XFFile::initPath($xfPath);
		if(!is_dir($path)) {
			mkdir($path, 0777);
		}
	}
	
	function listDirectory($xfPath = NULL) {
		$path = XFFile::initPath($xfPath);
		if(is_dir($path)) {
			$handle = opendir($path);
		} else {
			$handle = opendir(dirname($path));
		}
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				if(is_dir($path.'/'.$file))
					$return[] = $file;
			}
		}
		closedir($handle);
		return $return;
	}
	
	function readDirectory($xfPath = NULL) {
		$path = XFFile::initPath($xfPath);
		if(is_dir($path)) {
			$handle = dir($path);
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
	
	function modifyDirectory($xfPath) {
		rename("old", "new");
	}
	
	function deleteDirectory($xfPath) {
		rmdir($xfPath);
	}
}
?>