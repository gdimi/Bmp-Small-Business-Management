<?php
//add a client
if (!defined('_w00t_frm')) die('har har har');

$acerr = '';

if (!$pos or $pos != 'before') {
	$acerr = 'Task ['.$task.'] warning: no or wrong position of execution';
} else {
	$client = checkVals($acerr,$_POST);
	if (is_array($client)) {
		$today = time();
		try {
			$sccon = new PDO('sqlite:pld/HyperLAB.db3');
			$sccon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
			$insert = 'INSERT INTO "Client" (name, tel1, tel2, email, address, info) VALUES (:name, :tel1, :tel2, :email, :address, :info)';
			$sth = $sccon->prepare($insert);
			$scres = $sth->execute($client);
			if ($scres) {
				$schtml = 'Client <strong>'.$client['name']."</strong> added successfuly \n";
				file_put_contents('action_history.txt',$today.' '.$schtml,FILE_APPEND); //update history file
				$tk_status = json_encode(array(
				 'status' => 'success',
				 'message'=> $schtml
				));
				echo $tk_status;
				exit(0);
			} else { // something gone wrong!
				$acerr = "possible sql error!";
			}
		} catch(PDOException $ex) {
			$acerr = "An Error occured!".$ex->getMessage();
		}
	} 
}

if ($acerr) {
	$tk_status = json_encode(array(
	 'status' => 'error',
	 'message'=> $acerr.'<br />'
	));
	echo $tk_status;
	exit(1);
}

function checkVals(&$acerr,$postvals=array()) {
	$telIlligalChars = array(' ','-','_','+');
	$client = array();
	$client['name'] = trim($_POST['clName']);
	$client['tel1'] = trim($_POST['clTel1']);
	$client['tel2'] = trim($_POST['clTel2']);
	$client['email'] = trim($_POST['clemail']);
	$client['address'] = trim($_POST['clAddress']);
	$client['info'] = trim($_POST['clOinfo']);
	if ($client['name'] == '') {
		$acerr = 'No name found';
		return false;
	}
	
	$clTel1 = str_replace($telIlligalChars,'',$client['tel1']);
	$clTel2 = str_replace($telIlligalChars,'',$client['tel2']);
	
	if (strlen($client['name']) < 4) {
		$acerr = 'Name '.$client['name'].' too short!'.strlen($client['name']);
		return false;
	}
	if ($client['clTel1'] && !is_numeric($client['tel1'])) {
		$acerr = 'Cant you write a proper phone number??';
		return false;
	}
	if ($client['clTel2'] && !is_numeric($client['tel2'])) {
		$acerr = 'Cant you write a proper phone number??';
		return false;
	}
	if ($client['email']) {
		if (!validEMail_simple($client['email'])) {
			$acerr = 'This email is not valid';
			return false;
		}
	}
	if ($client['address']) {
		if (strlen($client['address']) < 4) {
			$acerr = 'Address too short!';
			return false;
		}
	}
	return $client;
}

function validEMail_simple($email) { 
	$regexp='/^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/';
	return preg_match($regexp, trim($email));
}
?>
