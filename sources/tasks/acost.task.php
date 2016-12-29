<?php
//add an expense
if (!defined('_w00t_frm')) die('har har har');
$pos = $_GET['pos'];

$acerr = '';

//var_dump($_POST);

if (!$pos or $pos != 'before') {
	$acerr = 'Task ['.$task.'] warning: no or wrong position of execution';
} else {
	$cost = checkVals($acerr,$_POST);
   // var_dump($cost);
    //die();
	if (is_array($cost)) {
		try {
            $today = time();
			$sccon = new PDO('sqlite:pld/HyperLAB.db3');
			$sccon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
			$insert = 'INSERT INTO "costs" (description, cdate, amount) VALUES (:description, :cdate, :amount)';
			$sth = $sccon->prepare($insert);
			$scres = $sth->execute($cost);
			if ($scres) {
				//make shorter description for output message and action history line
				$cs_short_descr = trim(preg_replace('/\s\s+/', ' ', $cost['description']));
				$cs_short_descr = mb_substr($cs_short_descr,0,mb_strpos($cs_short_descr,' ')).'...';
				$schtml = ' Το έξοδο <strong>'.$cs_short_descr."</strong> προστέθηκε επιτυχώς \n";
				file_put_contents('content/action_history.txt',$today.' '.$schtml,FILE_APPEND); //update history file
				$cs_status = json_encode(array(
				 'status' => 'success',
				 'message'=> $schtml
				));
				echo $cs_status;
				exit(0);
			}
		} catch(PDOException $ex) {
			$acerr = "An Error occured!".$ex->getMessage();
		} catch(Exception $x) {
            $acerr = "An error occured!".$x->getMessage();
        }

	} 
}

if ($acerr) {
	$cs_status = json_encode(array(
	 'status' => 'error',
	 'message'=> $acerr.'<br />'
	));
	echo $cs_status;
	exit(1);
}

function checkVals(&$acerr,$postvals=array()) {
	$cost = array();
	$cost['description'] = trim($_POST['cDesc']);
	$cost['amount'] = trim($_POST['cAmount']);
	$cost['cdate'] = trim($_POST['cDate']);
	if ($cost['description'] == '') {
		$acerr = 'No description found';
		return false;
	}

	if (strlen($cost['description']) < 4) {
		$acerr = 'Η περιγραφή είναι πολύ μικρή! '.strlen($cost['description']);
		return false;
	}

	if ($cost['amount']) {
		if (!is_numeric($cost['amount'])) {
			$acerr = 'Cant you write a proper number??';
			return false;
		}
	} else {
		$acerr = 'No amount e?';
		return false;
	}

	if (!$cost['cdate']) {
		$acerr = 'Pou einai i imerominia oeo?';
		return false;
	} else {
        if (strpos($cost['cdate'],'/')) { // check for / in date and replace them so to avoid automatic use of american system m/d/Y
            $cdate = str_replace('/','-',$cost['cdate']);
        }
        $ctimestamp = strtotime($cdate);
		if ($ctimestamp == false) {
			$acerr = 'Re vale swsth imerominia..';
            return false;
		} else {
            $cost['cdate'] = "$ctimestamp";
        }
	}

	return $cost;
}

?>
