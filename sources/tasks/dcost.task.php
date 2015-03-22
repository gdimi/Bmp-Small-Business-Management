<?php
//delete a ticket
if (!defined('_w00t_frm')) die('har har har');
$pos = $_GET['pos'];

if (!$pos or $pos != 'before') {
	$scerr = 'Task ['.$task.'] warning: no or wrong position of execution';
} else {
	if ($_GET['cid']) {
		$cid = (int)$_GET['cid'];
		$today = time();
		try {
			$sccon = new PDO('sqlite:pld/HyperLAB.db3');
			$sccon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
			$scres = $sccon->query('DELETE FROM "costs" WHERE id = '.$cid.';');
			if ($scres) {
				$ahistory = "$today Cost with id $cid is deleted \n";
				$c_status = json_encode(array(
				 'status' => 'success',
				 'message'=> 'Η λυπητερή με id '.$cid.' έχει ΔΙΑΓΡΑΦΕΙ.'
				));
				file_put_contents('content/action_history.txt',$ahistory,FILE_APPEND); //update history file
				echo $c_status;
				exit(0);
			}
		} catch(PDOException $ex) {
			$scerr = "An Error occured!".$ex->getMessage();
		}
	} else {
		$scerr = 'No case id!';
	}
}

if ($scerr) {
	$c_status = json_encode(array(
	 'status' => 'error',
	 'message'=> $scerr.'<br />'
	));
	echo $c_status;
	exit(1);
}
?>
