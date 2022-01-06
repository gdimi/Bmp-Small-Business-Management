<?php
/**
 * helpful model to get stuff from db
 * 
 * PHP version 5.6+
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version
 *
 * @category   bmp\sources\model
 * @package    bmp\sources
 * @author     Original Author <gdimi@hyperworks.gr>
 * @copyright  2014-2021 George Dimitrakopoulos
 * @license    GPLv2
 * @version    1.0
 * @link       -
 * @see        -
 * @since      0.658-dev
 * @deprecated -
 */

namespace BMP\Helpers;
use PDO;
if (!defined('_w00t_frm')) die('har har har');

class Model {
	
	public $model_error;
	public $dbh;
	private static $instance;

	private function __construct() {
		try {
			 $this->dbh = new PDO('sqlite:pld/HyperLAB.db3');
			 $this->dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
			 if ($this->dbh === NULL) { $this->model_error = 'ERRORERROR'; }
		} catch (PDOException $e) {
			 throw new PDOException($e->getMessage(), (int)$e->getCode());
		}
	}

	public static function getInstance() {
        if (!isset(self::$instance))
        {
            $object = __CLASS__;
            self::$instance = new $object;
        }
        return self::$instance;
    }
	
	//contacts
	function getContactDetails($cid) {
		try {
			$scres = $this->dbh->query('SELECT * FROM "Client" WHERE id = '.$cid.';');
			if ($scres) {
				return $scres->fetch(PDO::FETCH_ASSOC);			
			} 
		} catch(PDOException $ex) {
			$this->model_error = "An Error occurred!".$ex->getMessage();
		}
		
		return false;
	}
	
	function getContactName($cid) {
		try {
			$scres = $this->dbh->query('SELECT name FROM "Client" WHERE id = '.$cid.';');
			if ($scres) {
				foreach ($scres as $pcl) {
					$name = $pcl['name'];
				}
				
				return $name;
				
			} 
		} catch(PDOException $ex) {
			$this->model_error = "An Error occurred!".$ex->getMessage();
		}
		
		return false;
	}
	
	function getContactType($cid) {
		
	}
	
	function getContactList($type='') {
		
	}
	
}
