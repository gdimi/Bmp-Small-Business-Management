<?php
//display income table
if (!defined('_w00t_frm')) die('har har har');
$pos = $_GET['pos'];
$scerr = ''; //initialize error variable

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
				if ($im > 12) {
					$im = 1;
					$iy++;
				}
			}
			if (!$idate) { $idate = strtotime($iy.'-'.$im); }
			if (!$ldate) { $ldate = strtotime($ly.'-'.$lastMonth); }
			$idateSQL = ' AND updated < '.$idate.' AND updated > '.$ldate;
			$limit = '';
		}
		//die($idate.'|'.$ldate);
		//die($idate.'|'.$iy.'-'.$im.'|'.strtotime('2014-3').'|'.$now);
		$sccon = new PDO('sqlite:pld/HyperLAB.db3');
		$sccon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
		$scres = $sccon->query('SELECT id,type,title,price,updated FROM "Case" WHERE status >= 4'.$idateSQL.' ORDER BY updated DESC '.$limit.' ;');
		if ($scres) {
			$msg = '<table class="grid">
            <tr>
                <td>id</td>
                <td>title</td>
                <td>updated</td>
                <td>price</td>
            </tr>';
            $total = 0;
			foreach ($scres as $case) {
                $tcid = $case['type'].$case['id'];
               if ($case['type'] < 10) { //if id less than ten add a zero so to fix id length to 2 chars
                    $tcid = '0'.$tcid;
                }
				//$tdate = date("j/m/Y H:i:s",$case['created']);
				$tudate = date("j/m/Y H:i:s",$case['updated']);
				$msg .= '
                <tr>
                    <td>'.$tcid.'</td>
                    <td>'.$case['title'].'</td>
                    <td>'.$tudate.'</td>
                    <td>'.$case['price'].'</td>
                </tr>';
                $total += $case['price'];
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
