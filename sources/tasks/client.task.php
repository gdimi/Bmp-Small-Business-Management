<?php
//search for client
if (!defined('_w00t_frm')) die('har har har');

if (!$pos or $pos != 'before') {
	$scerr = 'Task ['.$task.'] warning: no or wrong position of execution';
} else {
	if ($_GET['cid']) {
		$cid = $_GET['cid'];
		try {
			$sccon = new PDO('sqlite:pld/HyperLAB.db3');
			$sccon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
			$scres = $sccon->query('SELECT * FROM "Client" WHERE id = '.$cid.';');
			//$scres = $sccon->query('SELECT cl.*, cs.title, cs.updated FROM "Client" AS cl INNER JOIN "Case" AS cs ON cl.id = cs.clientID WHERE cl.id = '.$cid.';');
			if ($scres) {
				foreach ($scres as $pcl) {
					$schtml .="
					<div>
						<form id=\"delc_frm\" name=\"delc-frm\">
							<label for=\"name\">Όνομα</label> <input type=\"text\" name=\"eclname\" value=\"${pcl['name']}\" /><br />
							<label for=\"tel1\">Τηλέφωνο1</label> <input type=\"text\" name=\"ecltel1\" value=\"${pcl['tel1']}\" /><br />
							<label for=\"tel2\">Τηλέφωνο2</label> <input type=\"text\" name=\"ecltel2\" value=\"${pcl['tel2']}\" /><br />
							<label for=\"email\">email</label> <input type=\"text\" name=\"eclmail\" value=\"${pcl['email']}\" /><br />
							<label for=\"address\">Διεύθυνση</label> <input type=\"text\" name=\"ecladdr\" value=\"${pcl['address']}\" /><br />
							<label for=\"info\">Info</label>  <textarea name=\"eclinfo\" >${pcl['info']}</textarea><br />
                            <input type=\"hidden\" id=\"delcid\" value=\"${cid}\" />
						</form>
					</div>
					";
				}
                //now get its cases
				$scres = $sccon->query('SELECT title,updated,type,id FROM "Case" WHERE clientID = '.$cid.' ORDER BY updated DESC;');
				if ($scres) {
					$schtml .="<h3>Cases</h3><div>";
					foreach ($scres as $ccl) {
						$updated = date('Y/m/d',$ccl['updated']);
						if ($ccl['type'] < 10) { //if id less than ten add a zero so to fix id length to 2 chars
							$cct = '0'.$ccl['type'];
						} else {
							$cct = $ccl['type'];
						}
						$schtml .='<div>'.$updated.' '.$ccl['title'].' ( '.$cct.$ccl['id'].' )</div>';
					}
					$schtml .="</div>";
				}
                //now add delete button
                $schtml .='';
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
		$scerr = 'No cid';
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
