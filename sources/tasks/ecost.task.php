<?php
//edit an expense
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
			$update = 'UPDATE "costs" SET description="'.$cost['description'].'", cdate="'.$cost['cdate'].'", amount="'.$cost['amount'].'" WHERE id='.$cost['id'].'; ';
			$sth = $sccon->prepare($update);
			$scres = $sth->execute();
			if ($scres) {
				$cs_short_descr = substr($cost['description'],0,40);
				$schtml = ' Το έξοδο <strong>'.$cs_short_descr."</strong> ενημερώθηκε επιτυχώς \n";
				file_put_contents('action_history.txt',$today.' '.$schtml,FILE_APPEND); //update history file
				$cs_status = json_encode(array(
				 'status' => 'success',
				 'message'=> $schtml
				));
				echo $cs_status;
				exit(0);
			}
		} catch(PDOException $ex) {
			$acerr = "An Error occured!".$ex->getMessage();
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
	$cost['description'] = trim($_POST['ecDesc']);
	$cost['amount'] = trim($_POST['ecAmount']);
	$cost['cdate'] = trim($_POST['ecDate']);
	$cost['id'] = (int)($_POST['ecId']);
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
    
    if (!$cost['id'] || $cost['id'] < 0) {
        $acerr = 'Invalid or no id';
        return false;
    }

	return $cost;
}

?>