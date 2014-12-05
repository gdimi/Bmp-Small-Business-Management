<?php
//delete a ticket
if (!defined('_w00t_frm')) die('har har har');
$pos = $_GET['pos'];

if (!$pos or $pos != 'before') {
	$scerr = 'Task ['.$task.'] warning: no or wrong position of execution';
} else {
	if ($_GET['tid']) {
		$tid = (int)$_GET['tid'];
		$today = time();
		try {
			$sccon = new PDO('sqlite:pld/HyperLAB.db3');
			$sccon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
			$scres = $sccon->query('DELETE FROM "Case" WHERE id = '.$tid.';');
			if ($scres) {
				$ahistory = "$today Case with id $tid is deleted \n";
				$tk_status = json_encode(array(
				 'status' => 'success',
				 'message'=> 'Case with id '.$tid.' is now deleted.'
				));
				file_put_contents('action_history.txt',$ahistory,FILE_APPEND); //update history file
				echo $tk_status;
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
	$tk_status = json_encode(array(
	 'status' => 'error',
	 'message'=> $scerr.'<br />'
	));
	echo $tk_status;
	exit(1);
}
?>
