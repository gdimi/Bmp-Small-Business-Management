<?php
namespace BMP\Core;

if (!defined('_w00t_frm')) die('har har har');

class Cache {
	private $cachefolder;
	protected $cachetime;
	protected $state;
	public $cachefile;
	public $cacheData;
	public $error;
	public $cachefilename;
	
	function __construct() {
		$this->cachefolder = 'cache';
		$this->state = 0; // 0 no cache, 1 ok, 2 old cache, other is error
		$this->cachetime = 24*3600;
	}
	
	private function checkState() {
		if (file_exists($this->cachefilename)) { //if not exists then there is no cache!
			$mtime = filemtime($this->cachefilename);
			$filesize = filesize($this->cachefilename);
			if ($filesize == 0) { // if filesize is zero either something went wrong or there is no cache
				$this->error = 'Zero cachefile size, refreshing cache...';
				$this->state = 0;
				return false;
			}
			$now = time();
			if (($now - $mtime) < $this->cachetime) { // decide state on time passed since cache file modified
				$this->state = 1;
			} else {
				$this->state = 2;
			}
		} else {
			$this->state = 0;
			$this->error = 'cache file not found';
		}
		return true;
	}
	
	public function getState() {
		return $this->state;
	}
	
	public function cacheInit() {
		//constract full path of cache file and check state
		$this->cachefilename = $this->cachefolder.'/'.$this->cachefile;
		$this->checkState();
	}
	
	public function doCache() {
		//immidiately write cache file
		if (!$this->cacheData) {
			$this->error = 'No cache data';
			return false;
		} else {
			try {
				file_put_contents($this->cachefilename,$this->cacheData);
				$this->state = 1;
				return true;
			} catch(Exception $e) {
				$this->error = $e;
				return false;
			}
		}
	}
	
	public function refreshCache() {
		//check if old or no cache and write cache
		switch($this->state) {
			case 0:
			case 2:
				$this->doCache();
				break;
			case 1:
				break;
			default:
				$this->error = 'Unkown cache state';
				return false;
		}
	}
	
}

?>
