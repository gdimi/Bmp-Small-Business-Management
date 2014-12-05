<?php
class tickets extends db {
	public $ticketfile = 'DEV_TODO.txt';
	public $historyfile = 'action_history.txt';
	public $tickets = Array();
	//public $ticketfiles = Array();
	//public $ticketfolder = 'tickets';

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
			 $rh .=$hdate.' '.$hline.'<br />';
		 }
		 return ($rh);
		} catch(Exception $e) {
		 return $e;
		}
	}
	
	function ScanforTickets() {
		if ($this->ticketfolder) {
			if (is_dir($this->ticketfolder)) {
				$files1 = scandir($this->ticketfolder);
				if ($this->ticketfolder != '.') {
					$path = $this->ticketfolder.'/';
				} else {
					$path = '';
				}
				foreach ($files1 as $ifiles) {
					if ($ifiles !='.' && $ifiles !='..') {
						$this->ticketfiles[] = $ifiles;
					}
				}
				return true;
			}
		} else {
			return false;
		}
	}
	
	function readAllTicketsnew() {
		$tickets = Array();
		$ticket_cnt = 0;
		try {
			//$cresult = $this->getConn()->query('SELECT * FROM "Case" LIMIT 0, 30');
			$cresult = $this->getConn()->query('SELECT cs.*,cl.name FROM "Case" AS cs INNER JOIN "Client" AS cl ON  cl.id = cs.clientID');
			if ($cresult) {
				foreach($cresult as $case) {
				//var_dump($ticket);
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
					$this->tickets[$case['id']]['closed'] = $case['closed'];
				}
				$ticket_cnt++;
			} else {
				var_dump($cresult);
			}
		} catch(PDOException $ex) {
			echo "An Error occured!".$ex->getMessage(); //user friendly message
		}
		return $this->tickets;
	}
}

?>
