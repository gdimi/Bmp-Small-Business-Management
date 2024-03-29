<?php
/**
 * add a case 
 *
 * this file adds a case
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
 * @copyright  2014-2016 George Dimitrakopoulos
 * @license    GPLv2
 * @version    1.0
 * @link       -
 * @see        -
 * @since      Since 0.375-dev
 * @deprecated -
 */
//TODO: add session validation...
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

$ticket = Array();
$tprior = Array("1","2","3");//FIXME add REAL config attributes
$ttype = $dss->caseType;
$tstatus = $dss->caseStatus;
$tpriority = $dss->casePriority;

$terror = '';
$fmsgerr = '';

$ticket['title'] = $_POST['title'];
$ticket['model'] = $_POST['model'];
$ticket['info'] = $_POST['info'];
$ticket['client'] = $_POST['client'];
$ticket['clientID'] = (int)$_POST['cid'];
$ticket['priority'] = (int)$_POST['priority'];
$ticket['type'] = (int)$_POST['type'];
$ticket['status'] = (int)$_POST['status'];
$ticket['user'] = $_POST['your-name'];
$ticket['category'] = $_POST['cat'];
$ticket['price'] = (float)$_POST['price'];
$ticket['follow'] = $_POST['follow'];
$fileUploaded = $_POST['fileUploaded'];

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
$ticket['follow'] = $tfrm->truncateString($ticket['follow'],255);

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
/*if (strlen($ticket['info']) < 2) {
	$terror = 'The description  is too small!';
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
$subject = 'new ticket for '.$dss->sitename;
$headers = 'From: '.$dss->mailfrom . "\r\n" .
    'Reply-To: '.$dss->mailto . "\r\n" .
    'X-Mailer: PHP/' . phpversion();


//now lets make a new ticket!
$ticket['created'] = time();
$ticket['updated'] = $ticket['created'];

$client = $ticket['client']; //store client name for email
unset($ticket['client']); //unset client key from array for correct pdo insert binding

//translate the tickets flags to actual human words

/*$tk_prior = $tpriority[$ticket['priority']];
$tk_type = $ttype[$ticket['type']];
$tk_stat = $tstatus[$ticket['status']];*/
$tcreated = date('Y-m-d H:i',$ticket['created']);//put human readable date instead of timestamp

$newTicket = "\n
[title: ${ticket['title']}]\n
[date: ${tcreated}]\n
[tag: ${ticket['category']}]\n
[model: ${ticket['model']}]\n
[info: ${ticket['info']}]\n
[client: $client (${ticket['clientID']})]\n
[FLAGS: ${tpriority[$ticket['priority']]},${ttype[$ticket['type']]},${tstatus[$ticket['status']]}]\n
[price: ${ticket['price']}]\n
[Follow: ${ticket['follow']}]\n
[name: ${ticket['user']}]\n";
//[FLAGS:${ticket['priority']},${ticket['type']},${ticket['status']}]\n

$ahistory = "${ticket['created']} ${ticket['user']} added case <strong> ${ticket['title']} </strong> \n";
//print_r($ticket);

//as this is adding for 1st time, we only need to check if it is already closed case so to update accordingly "closed" field.
$ticket['closed'] = '';
if ($ticket['status'] == 4) {
	$ticket['closed'] == $ticket['created'];
} 

try {
    $sccon = new PDO('sqlite:pld/HyperLAB.db3');
    $sccon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    $insert ='INSERT INTO "Case" (title, model, info, clientID, category, priority, type, status, created, updated , user, price, follow, closed) VALUES (:title, :model, :info, :clientID, :category, :priority, :type, :status, :created, :updated, :user, :price, :follow, :closed)';
	$sth = $sccon->prepare($insert);
	$scres = $sth->execute($ticket);
	if ($scres) {
		$lastId = $sccon->lastInsertId(); //get the id of the INSERT
		//handle attachments if any
		if ($fileUploaded && $lastId) {
			$fmsgerr = '';
			$target_dir = 'content/uploads/'.$lastId;
			$ourFile = 'content/uploads/tmp/'.$fileUploaded;
			if (file_exists($ourFile)) {
				if (!file_exists($target_dir)) {
					if (!mkdir($target_dir, 0777, true)) {
						$fmsgerr = '<br />Upload error: could not create directory:'.$target_dir;
					} else {
						if (!rename($ourFile,$target_dir.'/'.$fileUploaded)) {
							$fmsgerr = '<br />Upload error: Could not copy uploaded file('.$fileUploaded.') to '.$target_dir;
						}
					}
				}
			} else {
				$fmsgerr = '<br />Upload error:  uploaded file not found:'.$ourFile;
			}
		} elseif (!$lastId) {
			$fmsgerr = '<br>Upload error: invalid last id '.$lastId;
		}

		if (!$fmsgerr) {
			$schtml = 'Case <strong>'.$ticket['title'].'</strong> added successfuly';
			$tk_status = json_encode(array(
			 'status' => 'success',
			 'message'=> $schtml
			));
			mail($to,$subject,$newTicket,$headers); //send notification mail
			file_put_contents('content/action_history.txt',$ahistory,FILE_APPEND); //update history file
			echo $tk_status;
			exit(0);
		} else {
			$tk_status = json_encode(array(
			'status' => 'error',
			'message'=> $fmsgerr
			));
		}
	}
} catch(PDOException $ex) {
	$tk_status = json_encode(array(
    'status' => 'error',
    'message'=> $ex->getMessage()
    ));
}

if (is_object($ex) && method_exists($ex,getMessage)) {
        mail($to, $subject, $ex->getMessage(), $headers);
} else {
        mail($to, $subject, $tk_status, $headers);
}

echo $tk_status;
exit(1) ;

?>
