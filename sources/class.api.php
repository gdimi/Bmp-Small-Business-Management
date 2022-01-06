<?php
/**
 * class to handle api requests
 *
 * this file contains the class that handles api requests
 * 
 * PHP version 5+
 *
 *   This program is free software; you can redistribute it and/or modify
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
 * @since      0.733-dev
 * @deprecated -
 */

namespace BMP\Database;

if (!defined('_w00t_frm')) die('har har har');

class BMPApi extends Db {
    public $BMPStatus;
    public $BMPstate;
    protected $BMPApiError;
    protected $BMPApiMsg;
    protected $version;
    
    public function init() {
        $this->BMPStatus = $this->getStatus();
        $this->BMPApiError = 0;
        $this->BMPstate = false;
        $this->version = '1.0.0alpha';
        $this->setState();
    }
    
    /* check that we can access db and return true or false */
    private function getStatus() {
        return $this->connect();
    }
    
    /* get current state */
    public function getState() {
        return $this->BMPstate;
    }
    
    /* add a cookie with the current state */
    private function setState() {
        $state = $this->calcChanges();
        $this->BMPstate = $state;
        
        if ($state != 0 && $this->BPMApiError == 0) {
            setcookie("BMPstate", "$state", time()+3600, "/","", 1);
        } else {
            $this->_Log('API: Cannot set state');
        }
    }
    
    /* calculate the state */
    public function calcChanges() {

        $tsArray = array();
        
        // get latest case changed
        try {
            $cresult = $this->getConn()->query('SELECT `updated` FROM `Case` ORDER BY `updated` DESC LIMIT 1');
            if ($cresult) {
                foreach($cresult as $timestamp) {
                    $tsArray['case'] = $timestamp[0];
                }
            } else {
                $this->_Log($cresult);
            }
        } catch(PDOException $ex) {
            $this->BMPApiMsg =  "SYSTEM: An Error occured!".$ex->getMessage(); //user friendly message
            $this->BMPApiError = 1;
        }
        // get modification timestamp of history file
        $hstat = stat('content/action_history.txt');

        if (!$hstat) {
            $this->_Log('State: cannot stat action history file');
        } else {
            $tsArray['history'] = $hstat['atime'];
        }
        
        // get modification timestamp of board 
        $bstat = stat('content/board');

        if (!$bstat) {
            $this->_Log('State: cannot stat board file');
        } else {
            $tsArray['board'] = $bstat['atime'];
        }
        
        // get modification timestamp of motd 
        $mstat = stat('content/motd');

        if (!$mstat) {
            $this->_Log('State: cannot stat motd file');
        } else {
            $tsArray['motd'] = $mstat['atime'];
        }
        
        // get modification timestamp of various page 
        $vstat = stat('content/various.html');

        if (!$vstat) {
            $this->_Log('State: cannot stat various file');
        } else {
            $tsArray['various'] = $vstat['atime'];
        }
        
        asort($tsArray);
        
        return end($tsArray);
        //return $tsArray;
    }
    
    /* Return description */
    public function getDescr() {
        return 'This is a read only API for BMP. Version: '.$this->version;
    }
    
    /* Return version */
    public function version() {
        return $this->version;
    }
    
    /* Return last message */
    public function lastMsg() {
        return $this->BMPApiMsg;
    }
    
    /* Return last error state */
    public function errorState() {
        return $this->BMPApiError;
    }
    
    /* API logger */
    private function _Log($data) {
        //set last message
        $this->BMPApiMsg = $data;
    }
    

     
}
?>