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
 
 * last update 29/11/2022
 - add an array for logging last 100 messages
 */

namespace BMP\Database;

if (!defined('_w00t_frm')) die('har har har');

class BMPApi extends Db {
    public $BMPStatus;
    public $BMPstate;
    protected $BMPApiError;
    protected $BMPApiMsg;
    protected $version;
    private $arrayStates;
    protected $apiLog;
    
    public function init($dontSetState = false) {
        $this->BMPStatus = $this->getStatus();
        $this->BMPApiError = 0;
        $this->BMPstate = false;
        $this->version = '1.0.1alpha';
        $this->arrayStates = $this->arrayChanges();
        $this->setState($dontSetState);
        $this->apiLog = array();
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
    private function setState($dontSetCookie = false) {
        $state = $this->calcChanges();
        $this->BMPstate = $state;
        
        if ($state != 0 && $this->BMPApiError == 0 && !$dontSetCookie) {
            setcookie("BMPstate", "$state", time()+3600, "/","", 1);
        } else {
            $this->_Log('API: Cannot set state cookie');
        }
    }
    
    /*return an array with states and errors*/
    private function arrayChanges() {
        $tsArray = array();
        $tsErrorArr = array();
        
        // get latest case changed
        try {
            $cresult = $this->getConn()->query('SELECT `updated` FROM `Case` ORDER BY `updated` DESC LIMIT 1');
            if ($cresult) {
                foreach($cresult as $timestamp) {
                    $tsArray['case'] = $timestamp[0];
                }
            } else {
                $tsErrorArr['db'] = $cresult;
            }
        } catch(PDOException $ex) {
            $this->BMPApiMsg =  "SYSTEM: An Error occured!".$ex->getMessage(); //user friendly message
            $this->BMPApiError = 1;
        }
        // get modification timestamp of history file
        $hstat = stat('content/action_history.txt');

        if (!$hstat) {
            $tsArray['history'] = 'State: cannot stat action history file';
        } else {
            $tsErrorArr['history'] = $hstat['mtime'];
        }
        
        // get modification timestamp of board 
        $bstat = stat('content/board');

        if (!$bstat) {
            $tsErrorArr['board'] ='State: cannot stat board file';
        } else {
            $tsArray['board'] = $bstat['mtime'];
        }
        
        // get modification timestamp of motd 
        $mstat = stat('content/motd');

        if (!$mstat) {
            $tsErrorArr['motd'] = 'State: cannot stat motd file';
        } else {
            $tsArray['motd'] = $mstat['mtime'];
        }
        
        // get modification timestamp of various page 
        $vstat = stat('content/various.html');

        if (!$vstat) {
            $tsErrorArr['various'] = 'State: cannot stat various file';
        } else {
            $tsArray['various'] = $vstat['mtime'];
        }
        
        asort($tsArray,SORT_NUMERIC);
        
        return $tsArray+$tsErrorArr;
    }
    
    public function getVarious() {
        try {
            $various = file_get_contents('content/various.html');
            return $various;
        } catch(Exception $ex) {
            $this->BMPApiError = 1;
            $this->BMPApiMsg = 'Error: Cannot read various content';
            return false;
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
            $tsArray['history'] = $hstat['mtime'];
        }
        
        // get modification timestamp of board 
        $bstat = stat('content/board');

        if (!$bstat) {
            $this->_Log('State: cannot stat board file');
        } else {
            $tsArray['board'] = $bstat['mtime'];
        }
        
        // get modification timestamp of motd 
        $mstat = stat('content/motd');

        if (!$mstat) {
            $this->_Log('State: cannot stat motd file');
        } else {
            $tsArray['motd'] = $mstat['mtime'];
        }
        
        // get modification timestamp of various page 
        $vstat = stat('content/various.html');

        if (!$vstat) {
            $this->_Log('State: cannot stat various file');
        } else {
            $tsArray['various'] = $vstat['mtime'];
        }
        
        asort($tsArray,SORT_NUMERIC);
        
        return end($tsArray);
        //return $tsArray;
    }
    
    /*return json with all states and errors*/
    public function returnStatesErrors() {
        return json_encode($this->arrayStates);
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
    
    /*Return log*/
    public function readLog() {
        return $this->apiLog;
    }
    
    /* API logger */
    private function _Log($data) {
        //set last message
        $this->BMPApiMsg = $data;
        
        //hold only last 100 messages in array log
        if (count($this->apiLog) > 100) {
            $this->apiLog = array();
        }
        //store data
        $this->apiLog[] = $data;
    }
    

     
}
?>
