<?php
//show expenses
if (!defined('_w00t_frm')) die('har har har');
$pos = $_GET['pos'];

if (!$pos or $pos != 'before') {
	$scerr = 'Task ['.$task.'] warning: no or wrong position of execution';
} else {
	try {
		if ($_GET['cid']) {
            $id = (int)$_GET['cid'];
            $sccon = new PDO('sqlite:pld/HyperLAB.db3');
            $sccon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
            $scres = $sccon->query('SELECT description,amount,cdate FROM "costs" WHERE id = '.$id.' ;');
            if ($scres) {
                //TODO handle values and output
                foreach ($scres as $cost) {
                    $desc = $cost['description'];
                    $amount = $cost['amount'];
                    $cdate = date("d/m/Y",$cost['cdate']);
                }
                $tk_status = json_encode(array(
                 'status' => 'success',
                 'desc' => $desc,
                 'amount' => $amount,
                 'cdate' => $cdate
                ));
                echo $tk_status;
                exit(0);
            } else {
                $scerr = "An error occured pulling cost from db: $id";
            }
        } else {
            $scerr = 'No id supplied!';
        }
	} catch(PDOException $ex) {
		$scerr = "An Error occured!".$ex->getMessage();
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
