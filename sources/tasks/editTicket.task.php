<?php
/**
 * update a ticket 
 *
 * this file updates a case 
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
namespace BMP\Core;
use PDO;
 
if (!defined('_w00t_frm')) die('har har har');

//check position of execution
if ($_GET['pos'] != 'before') {
	echo json_encode(array(
		'status' => 'error',
		'message'=> 'Wrong or no position of execution'
		));
		exit(1) ;
}

require_once('sources/Class.formVal.php');
require_once('sources/config.php');
$dss = new DSconfig;
$tfrm = new ValForm;

$terror = '';
$ticket = Array();
$tprior = Array("1","2","3");//FIXME use REAL config attributes
$ttype = $dss->caseType;
$tstatus = $dss->caseStatus;
$tpriority = $dss->casePriority;

$ticket['id'] = $_POST['tid'];
$ticket['title'] = $_POST['title'];
$ticket['model'] = $_POST['model'];
$ticket['info'] = $_POST['info'];
$ticket['client'] = $_POST['client'];
$ticket['clientID'] = (int)$_POST['cid'];
$ticket['priority'] = (int)$_POST['epriority'];
$ticket['type'] = (int)$_POST['etype'];
$ticket['status'] = (int)$_POST['estatus'];
$ticket['user'] = $_POST['user'];
$ticket['category'] = $_POST['cat'];
$ticket['price'] = (float)$_POST['price'];
$ticket['follow'] = $_POST['follow'];
if (isset($_POST['ctupdate'])) { $ticket['ctupdate'] = $_POST['ctupdate']; }


//print_r($tickets);
//make default values for non-required fields
if ($ticket['price'] == '') { $ticket['price'] = "0.0"; }
if ($ticket['info'] == '') { $ticket['info'] = '-'; }
if ($ticket['model'] == '') { $ticket['model'] = '-'; }
if ($ticket['category'] == '') { $ticket['category'] = '-'; }

foreach($ticket as $key=>$value) {
	if ($value == '') {
		$terror = $key.' is empty!';
		echo json_encode(array(
		'status' => 'error',
		'message'=> 'Error mate! '.$terror
		));
		exit(1);
	} else {
		$ticket[$key] = $tfrm->removeXss($value);
	}
}

//check values and finalize
$ticket['title'] = $tfrm->truncateString($ticket['title'],255);
$ticket['model'] = $tfrm->truncateString($ticket['model'],255);
$ticket['info'] = trim($ticket['info']);
$ticket['category'] = $tfrm->truncateString($ticket['category'],255);
$ticket['user'] = $tfrm->truncateString($ticket['user'],64);

if (!in_array($ticket['priority'],$tprior)) {
	$terror = 'Uknown ticket priority';
}
if (!$ttype[$ticket['type']]) {
	$terror = 'Uknown ticket type';
}
if (!$tstatus[$ticket['status']]) {
	$terror = 'Uknown ticket status';
}

if (strlen($ticket['title']) < 5) {
	$terror = 'Title is too small!';
}
/*if (strlen($ticket['error']) < 1) {
	$terror = 'The Error  is too small!';
}*/


if ($terror) {
	foreach($ticket as $value) {
		$terror .='| '.$value;
	}
	echo json_encode(array(
    'status' => 'error',
    'message'=> 'Errrorrr: '.$terror
    ));
	exit(1) ;
}

//prepare mail
$to      = $dss->mailto;
$subject = 'updated case for '.$dss->sitename;
$headers = 'From: '.$dss->mailfrom . "\r\n" .
    'Reply-To: '.$dss->mailto . "\r\n" .
    'X-Mailer: PHP/' . phpversion();


//now lets make a new ticket!
//check if we're not going to change the updated value of the ticket so to avoid wrong calcs based on close status and updated date
if (!isset($ticket['ctupdate']) || $ticket['ctupdate'] != '1') {
	$ticket['updated'] = time();
	$updatedqry = '", updated="'.$ticket['updated'];
} else {
	$updatedqry = '';
}

//FIXME in template & task, there is no 'client' field, only his/hers id
$client = $ticket['client']; //store client name for email
unset($ticket['client']); //unset client key from array for correct pdo insert binding

$edTicket = "\n
[title: ${ticket['title']}]\n
[tag: ${ticket['category']}]\n
[model:${ticket['model']}]\n
[info: ${ticket['info']}]\n
[client: $client (${ticket['clientID']})]\n
[FLAGS: ${tpriority[$ticket['priority']]},${ttype[$ticket['type']]},${tstatus[$ticket['status']]}]\n
[price: ${ticket['price']}]\n
[name: ${ticket['user']}]\n
[follow: ${ticket['follow']}]\n";

$ahistory = time()." ${ticket['user']} updated case ${ticket['title']} \n";

try {
    $sccon = new PDO('sqlite:pld/HyperLAB.db3');
    $sccon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
    $update = 'UPDATE "Case" SET title="'.$ticket['title'].$updatedqry.'", model="'.$ticket['model'].'", info="'.$ticket['info'].'", category="'.$ticket['category'].'", clientID='.$ticket['clientID'].', priority='.$ticket['priority'].', type='.$ticket['type'].', status='.$ticket['status'].', price='.$ticket['price'].', user="'.$ticket['user'].'", follow="'.$ticket['follow'].'"  WHERE id='.$ticket['id'].' ;';
	$sth = $sccon->prepare($update);
	$scres = $sth->execute();
	if ($scres) {
		$schtml = 'Case <strong>'.$ticket['title'].'</strong> updated successfuly';
		$tk_status = json_encode(array(
		 'status' => 'success',
		 'message'=> $schtml
		));
		//check if we're to send or not notification mail
		if ($_POST['ctnotify'] != '1') {
			mail($to,$subject,$edTicket,$headers);
		}
        file_put_contents('content/action_history.txt',$ahistory,FILE_APPEND); //update history file
		echo $tk_status;
		exit(0);
	}
} catch(PDOException $ex) {
	$terror = $ex->getMessage();
} catch(Exception $e) {
	$terror = $e->getMessage();
}

$tk_status = json_encode(array(
'status' => 'error',
'message'=> $terror
));
mail($to, $subject, $terror, $headers);
echo $tk_status;
exit(1) ;

?>
