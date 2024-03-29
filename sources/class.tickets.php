<?php
/**
 * class to handle tickets in case tracker
 *
 * this file contains the class that handles cases in tracker and history functionality
 * 
 * PHP version 5+
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version
 *
 * @category   bmp\sources\ajax handlers
 * @package    bmp\sources
 * @author     Original Author <gdimi@hyperworks.gr>
 * @copyright  2014-2015 George Dimitrakopoulos
 * @license    GPLv2
 * @version    1.0
 * @link       -
 * @see        -
 * @since      Initial
 * @deprecated -
 */
namespace BMP\Database;

if (!defined('_w00t_frm')) die('har har har');

class Tickets extends Db {
	public $historyfile = 'content/action_history.txt';
	public $tickets = Array();
	public $attachDir;
	public $sclosed;

	function readHistory() {
		try {
		 return file_get_contents($this->historyfile);
		} catch(Exception $e) {
		 return $e;
		}
	}
	
	function returnHistory() {
		try {
         $rh = '';
		 $history =  file($this->historyfile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
		 $history = array_reverse($history);//reverse array so actions appear first to last in time order
		 foreach ($history as $line) {
			 $hdate = (int)substr($line,0,strpos($line,' '));
			 $hline = str_replace($hdate,'',$line);
			 $hdate = date('Y/m/j-H:i',$hdate);
			 $rh .= $hdate.' '.$hline.'<br />';
		 }
		 return ($rh);
		} catch(Exception $e) {
		 return $e;
		}
	}
	
	function checkForAttachment($cid) {
		$defUploadDir = $this->attachDir;
		$ticketfiles = array();
		
		if ($cid && $cid > 0) {
			$caseDir = $defUploadDir.'/'.$cid;
			if ($defUploadDir) {
				if (is_dir($caseDir)) {

					$files1 = scandir($caseDir);

					foreach ($files1 as $ifiles) {
						if ($ifiles !='.' && $ifiles !='..') {
							$ticketfiles[] = $ifiles;
						}
					}

					return $ticketfiles;
				} else {
					return '';
				}
			} else {
				return 'SYSTEM: no defUploadDir';
			}
		} else {
			return 'SYSTEM: invalid cid';
		}
	}


	function readAllTicketsnew($sort) {
		$tickets = Array();
		$ticket_cnt = 0;
		try {
			switch ($sort) {
				case "updated":
					$ssql = ' ORDER BY cs.updated DESC';
					break;				
				case "model":
					$ssql = ' ORDER BY cs.model ASC, cs.id DESC';
					break;
				case "tag":
					$ssql = ' ORDER BY cs.category DESC, cs.id DESC';
					break;
				case "client":
					$ssql = ' ORDER BY cl.name ASC, cs.id DESC';
					break;
				case "status":
					$ssql = ' ORDER BY cs.status DESC, cs.id DESC';
					break;
				case "prior":
					$ssql = ' ORDER BY cs.priority DESC, cs.id DESC';
					break;
				case "type":
					$ssql = ' ORDER BY cs.type ASC, cs.id DESC';
					break;
				case "user":
					$ssql = ' ORDER BY cs.user ASC, cs.id DESC';
					break;
				default:
					$ssql = ' ORDER BY cs.created DESC';
			}
			$cresult = $this->getConn()->query('SELECT cs.*,cl.name FROM "Case" AS cs LEFT JOIN "Client" AS cl ON  cl.id = cs.clientID AND cs.status < '.$this->sclosed.''.$ssql);
			if ($cresult) {
				foreach($cresult as $case) {
					$attached = $this->checkForAttachment($case['id']);

					$this->tickets[$case['id']]['id'] = $case['id'];
					$this->tickets[$case['id']]['title'] = $case['title'];
					$this->tickets[$case['id']]['model'] = $case['model'];
					$this->tickets[$case['id']]['info'] = $case['info'];
					$this->tickets[$case['id']]['client'] = $case['clientID'];
					$this->tickets[$case['id']]['cat'] = $case['category'];
					$this->tickets[$case['id']]['priority'] = $case['priority'];
					$this->tickets[$case['id']]['type'] = $case['type'];
					$this->tickets[$case['id']]['status'] = $case['status'];
					$this->tickets[$case['id']]['created'] = $case['created'];
					$this->tickets[$case['id']]['updated'] = $case['updated'];
					$this->tickets[$case['id']]['price'] = $case['price'];
					$this->tickets[$case['id']]['user'] = $case['user'];
					$this->tickets[$case['id']]['name'] = $case['name'];
					$this->tickets[$case['id']]['follow'] = $case['follow'];
					$this->tickets[$case['id']]['attachment'] = $attached;
				}
				$ticket_cnt++;
			} else {
				var_dump($cresult);
			}
		} catch(PDOException $ex) {
			echo "SYSTEM: An Error occured!".$ex->getMessage(); //user friendly message
		}
		return $this->tickets;
	}
}

?>
