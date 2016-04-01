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

class tickets extends db {
	public $historyfile = 'content/action_history.txt';
	public $tickets = Array();
	public $attachDir;


	function readHistory() {
		try {
		 return file_get_contents($this->historyfile);
		} catch(Exception $e) {
		 return $e;
		}
	}
	
	function returnHistory() {
		try {
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
		$caseDir = $defUploadDir.'/'.$cid;
		if ($cid && $cid > 0) {
			if ($defUploadDir) {
				if (is_dir($caseDir)) {

					$files1 = scandir($caseDir);

					foreach ($files1 as $ifiles) {
						if ($ifiles !='.' && $ifiles !='..') {
							$ticketfiles = $ifiles;
						}
					}

					return $ticketfiles;
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
			}
			$cresult = $this->getConn()->query('SELECT cs.*,cl.name FROM "Case" AS cs INNER JOIN "Client" AS cl ON  cl.id = cs.clientID'.$ssql);
			if ($cresult) {
				foreach($cresult as $case) {
					$attach = $this->checkForAttachment($case['id']);

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
					$this->tickets[$case['id']]['attachment'] = $attach;
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
