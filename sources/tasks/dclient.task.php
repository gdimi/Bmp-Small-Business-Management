<?php
//delete client
if (!defined('_w00t_frm')) die('har har har');

if (!$pos or $pos != 'before') {
	$scerr = 'Task ['.$task.'] warning: no or wrong position of execution';
} else {
	if ($_GET['clid']) {
		$cid = (int)$_GET['clid'];
		try { 
			$sccon = new PDO('sqlite:pld/HyperLAB.db3');
			$sccon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
			//first get the clients data
			$scres = $sccon->query('SELECT * FROM "Client" WHERE id = '.$cid.';');
			if ($scres) {
				$storemsg = '';
				$clientData2store = array();

				foreach ($scres as $key=>$value) {
					$clientData2store[$key] = $value;
				}
				//store into contents/trashed
				$clJSONdata = json_encode($clientData2store);
				if (!file_put_contents("content/trashed/client-$cid",$clJSONdata)) {
					$storemsg = ',but client data store failed';
				} else {
					$storemsg = ',and client data stored at content/trashed/client-'.$cid;
				}
                //now delete from database
				$scres = $sccon->query('DELETE FROM "Client" WHERE id = '.$cid.';');
				if ($scres) {
					$tk_status = json_encode(array(
					 'status' => 'success',
					 'message'=> 'Client with id '.$cid.' is now deleted'.$storemsg.'.'
					));
					echo $tk_status;
					exit(0);
				} else {
					$scerr = 'An error occured during delete!';
				}
			}
		} catch(PDOException $ex) {
			$scerr = "An Error occured!".$ex->getMessage();
		}
	} else {
		$scerr = 'Fatal: No client id supplied';
	}
}

if ($scerr) {
	$tk_status = json_encode(array(
	 'status' => 'error',
	 'message'=> $scerr.'<br />'
	));
	echo $tk_status;
	exit(1);
}
?>
