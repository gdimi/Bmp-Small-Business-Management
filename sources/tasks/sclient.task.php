<?php
//search for client
if (!defined('_w00t_frm')) die('har har har');

if (!$pos or $pos != 'before') {
	$scerr = 'Task ['.$task.'] warning: no or wrong position of execution';
} else {
	if ($_POST['term']) {
		$term = $_POST['term'];
		try {
			$sccon = new PDO('sqlite:pld/HyperLAB.db3');
			$sccon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
			$scres = $sccon->query('SELECT name,id FROM "Client" WHERE name LIKE "%'.$term.'%" LIMIT 10');
			if ($scres) {
				foreach ($scres as $pcl) {
					$schtml .='<div>'.$pcl['name'].' (<span id="client_'.$pcl['id'].'">'.$pcl['id'].'</span>)</div>';
				}
				$tk_status = json_encode(array(
				 'status' => 'success',
				 'message'=> $schtml
				));
				echo $tk_status;
				exit(0);
			}
		} catch(PDOException $ex) {
			$scerr = "An Error occured!".$ex->getMessage();
		}
	} else {
		$scerr = 'No term';
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
