<?php
/**
 * update client 
 *
 * this file updates a client 
 * 
 * PHP version 5.2+
 *
 * LICENCE: This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version
 *
 * @category   bmp\sources\ajax handlers
 * @package    bmp\sources
 * @author     Original Author <gdimi@hyperworks.gr>
 * @copyright  2014-2015 George Dimitrakopoulos
 * @license    GPLv2
 * @version    1.0
 * @link       -
 * @see        -
 * @since      Since 0.375-dev
 * @deprecated -
 */
if (!defined('_w00t_frm')) die('har har har');
$pos = $_GET['pos'];

$ecerr = '';

if (!$pos or $pos != 'before') {
	$ecerr = 'Task ['.$task.'] warning: no or wrong position of execution';
} else {
	$client = checkVals($ecerr,$_POST);

	if (is_array($client)) {
		try {
            $today = time();
			$sccon = new PDO('sqlite:pld/HyperLAB.db3');
			$sccon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );

			$update = 'UPDATE "client" SET name="'.$client['name'].'", tel1="'.$client['tel1'].'", tel2="'.$client['tel2'].'", email="'.$client['email'].'", address="'.$client['address'].'", info="'.$client['info'].'" WHERE id='.$client['id'].'; ';

			$sth = $sccon->prepare($update);
			$scres = $sth->execute();

			if ($scres) {
				$schtml = 'Client  <strong>'.$client['name']."</strong> (".$client['id'].")ενημερώθηκε επιτυχώς \n";
				file_put_contents('content/action_history.txt',$today.' '.$schtml,FILE_APPEND); //update history file
				$ecl_status = json_encode(array(
				 'status' => 'success',
				 'message'=> $schtml
				));
				echo $ecl_status;
				exit(0);
			}

		} catch(PDOException $ex) {
			$ecerr = "An Error occured!".$ex->getMessage();
		}
	} 
}

if ($ecerr) {
	$cs_status = json_encode(array(
	 'status' => 'error',
	 'message'=> $ecerr.'<br />'
	));
	echo $cs_status;
	exit(1);
}

function checkVals(&$ecerr,$postvals=array()) {
	$telIlligalChars = array(' ','-','_','+');
	$client = array();

	$client['name'] = trim($_POST['eclname']);
	$client['address'] = trim($_POST['ecladdr']);
	$client['info'] = trim($_POST['eclinfo']);
	$client['email'] = trim($_POST['eclmail']);
	$client['tel1'] = trim($_POST['ecltel1']);
	$client['tel2'] = trim($_POST['ecltel2']);
	$client['id'] = (int)($_POST['eclid']);
	if ($client['name'] == '') {
		$ecerr = 'No name found';
		return false;
	}

	$clTel1 = str_replace($telIlligalChars,'',$client['tel1']);
	$clTel2 = str_replace($telIlligalChars,'',$client['tel2']);
	
	if (strlen($client['name']) < 4) {
		$ecerr = 'Name '.$client['name'].' too short!'.strlen($client['name']);
		return false;
	}
	if ($client['clTel1'] && !is_numeric($client['tel1'])) {
		$ecerr = 'Cant you write a proper phone number??';
		return false;
	}
	if ($client['clTel2'] && !is_numeric($client['tel2'])) {
		$ecerr = 'Cant you write a proper phone number??';
		return false;
	}
	if ($client['email']) {
		if (!validEmailSimple($client['email'])) {
			$ecerr = 'This email is not valid';
			return false;
		}
	}
	if ($client['address']) {
		if (strlen($client['address']) < 4) {
			$ecerr = 'Address too short!';
			return false;
		}
	}

	if ($client['id'] <= 0) {
		$ecerr = 'Invalid id';
	}

	return $client;
}

function validEmailSimple($email) { 
	$regexp='/^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/';
	return preg_match($regexp, trim($email));
}

?>
