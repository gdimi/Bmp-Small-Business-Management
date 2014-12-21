<?php
//show expenses table
if (!defined('_w00t_frm')) die('har har har');
$pos = $_GET['pos'];

if (!$pos or $pos != 'before') {
	$scerr = 'Task ['.$task.'] warning: no or wrong position of execution';
} else {
	try {
		if ($_GET['im'] and $_GET['iy']) {
			date_default_timezone_set('UTC+2');
			$now = time();
			$curMonth = date('n')+1;
			$curYear = date('Y');
			$curDay = date('d');
			$im = $_GET['im']+1;
			$lastMonth = $im - 1;
			$iy = $_GET['iy'];
			$ly = $iy;
			if ($iy > $curYear) {
				$idate = $now;
			} else {
				if ($im > 12 && $iy == $curYear) {
					$im = 1;
					$lastMonth;
					$iy++;
				} elseif ($im > 12 && $iy < $curYear) {
					$im = 12;
				}
			}
			if (!$idate) { $idate = strtotime($iy.'-'.$im); }
			if (!$ldate) { $ldate = strtotime($ly.'-'.$lastMonth); }
			$idateSQL = ' WHERE cdate < '.$idate.' AND cdate > '.$ldate;
			$limit = '';
		}
		//die($idate.'|'.$ldate);
		//die($idate.'|'.$iy.'-'.$im.'|'.strtotime('2014-3').'|'.$now);
		//echo 'SELECT id,description,amount,cdate FROM "costs" '.$idateSQL.' ORDER BY cdate DESC '.$limit.' ;';
		//die();
		$sccon = new PDO('sqlite:pld/HyperLAB.db3');
		$sccon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
		$scres = $sccon->query('SELECT id,description,amount,cdate FROM "costs" '.$idateSQL.' ORDER BY cdate DESC '.$limit.' ;');
		if ($scres) {
			$msg = '<table class="grid">
            <tr>
                <td>id</td>
                <td>description</td>
                <td>date</td>
                <td>amount</td>
                <td>action</td>
            </tr>';
            $total = 0;
			foreach ($scres as $cost) {
				$tudate = date("j/m/Y",$cost['cdate']);
				$msg .= '
                <tr>
                    <td>'.$cost['id'].'</td>
                    <td>'.$cost['description'].'</td>
                    <td>'.$tudate.'</td>
                    <td>'.$cost['amount'].'</td>
                    <td> <span id="ctd_'.$cost['id'].'" style="" class="cost-delete">D</span> <span id="cte_'.$cost['id'].'" class="cost-edit" style="">E</span></td>
                </tr>';
                $total += $cost['amount'];
			}
			$msg .= '</table><div style="position: absolute; top: 0px; left: 35%;">Σύνολο: <strong>'.$total.'</strong></div>';
			$tk_status = json_encode(array(
			 'status' => 'success',
			 'message'=> $msg
			));
			echo $tk_status;
			exit(0);
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
