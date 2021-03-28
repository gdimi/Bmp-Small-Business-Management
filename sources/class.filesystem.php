<?php
/**
 * filesystem class
 *
 * this file contains the main filesystem class
 * 
 * PHP version 5+
 *
 * LICENCE: This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version
 *
 * @category   bmp\sources\classes
 * @package    bmp\sources
 * @author     Original Author <gdimi@hyperworks.gr>
 * @copyright  2014-2021 George Dimitrakopoulos
 * @license    GPLv2
 * @version    1.0
 * @link       -
 * @see        -
 * @since      Since 0.589-dev
 * @deprecated -
 */
if (!defined('_w00t_frm')) die('har har har');

class Filesystem {

    public $fsErr;
    public $uploadFolder;
	private $uploadTypes;
    
	function __construct($uploadTypes=array()) {
		$this->fsErr = '';
		$this->uploadFolder = 'content/uploads/';
		$this->uploadTypes = $uploadTypes;
	}
	
	//delete a file. Only deletes allowed types set in config
	public function unlinkFile($obj) {
		if (is_readable($obj)) {
			$fileType = $this->getFileType($obj);
			
			if(!in_array($fileType,$this->uploadTypes)) {
				$this->fsErr = "Sorry, this file type ($fileType) is not allowed.";
			} else {
				if (unlink($obj)) {
					$this->fsErr = '';
					return true;
				} else {
					$this->fsErr = 'could not delete file';
				}
			}
		} else {
			$this->fsErr = 'file not found';
		}
		
		return false;
	}
	
	protected function checkFilePerms($obj) {
		
	}
	
	protected function checkDirPerms($obj) {
		
	}
	
	protected function checkDiskSpace() {
		
	}
	
	protected function getHomePath() {
		
	}
	
	//save file write using lock
	public function safeFileWrite($fileName, $data) {    
		if ($fp = fopen($fileName, 'w')) {
			$startTime = microtime(TRUE);
			do {
				$canWrite = flock($fp, LOCK_EX);
			    // If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
				if(!$canWrite) usleep(round(rand(0, 100)*1000));
			} while ((!$canWrite) && ((microtime(TRUE)-$startTime) < 5));

			//file was locked so now we can store information
			if ($canWrite) {
				$wd = fwrite($fp, $data);
				flock($fp, LOCK_UN);
				
				if (!$wd) {
					$this->fsErr = 'Could not write file '.$filename;
					return false;
				}
			}
			fclose($fp);
			
			return true;
		}
	}
	
	public function getFileType($thefile) {
		return strtolower(pathinfo($thefile,PATHINFO_EXTENSION));
	}

}

?>
