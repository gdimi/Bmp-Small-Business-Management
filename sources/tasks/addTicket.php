<?php
/*history:
 * 27/10/13 - added name in save
 */ 
//TODO: add session validation...
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
//$ticket['id'] = $_POST['id'];
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
$ticket['title'] = $tfrm->truncate_str($ticket['title'],255);
$ticket['model'] = $tfrm->truncate_str($ticket['model'],255);
$ticket['info'] = $tfrm->truncate_str($ticket['info'],4096);
$ticket['category'] = $tfrm->truncate_str($ticket['category'],255);
$ticket['user'] = $tfrm->truncate_str($ticket['user'],64);
$ticket['follow'] = $tfrm->truncate_str($ticket['follow'],255);

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
if ($ticket['status'] == 4) {
	$ticket['closed'] == $ticket['created'];
} else {
	$ticket['closed'] == '';
}

try {
    $sccon = new PDO('sqlite:pld/HyperLAB.db3');
    $sccon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
    $insert ='INSERT INTO "Case" (title, model, info, clientID, category, priority, type, status, created, updated , user, price, follow, closed) VALUES (:title, :model, :info, :clientID, :category, :priority, :type, :status, :created, :updated, :user, :price, :follow, :closed)';
	$sth = $sccon->prepare($insert);
	$scres = $sth->execute($ticket);
	if ($scres) {
		$schtml = 'Case <strong>'.$ticket['title'].'</strong> added successfuly';
		$tk_status = json_encode(array(
		 'status' => 'success',
		 'message'=> $schtml
		));
        mail($to,$subject,$newTicket,$headers); //send notification mail
        file_put_contents('action_history.txt',$ahistory,FILE_APPEND); //update history file
		echo $tk_status;
		exit(0);
	}
} catch(PDOException $ex) {
	$tk_status = json_encode(array(
    'status' => 'error',
    'message'=> $ex->getMessage()
    ));
	mail($to, $subject, $ex->getMessage(), $headers);
    echo $tk_status;
	exit(1) ;
}
/*
try {
  //$ticket_json = json_encode($ticket);
  //file_put_contents('DEV_TODO.txt',$newTicket,FILE_APPEND);
  //file_put_contents("tickets/".str_replace('/','-',$ticket['cat']).'_'.str_replace('/','-',$today)."_".str_replace(' ','-',$ticket['title']),$ticket_json);
  $tk_status = json_encode(array(
    'status' => 'success',
    'message'=>'<div id="tkt_success" style="color:green; border:medium solid green;padding:8px;">Case <strong>'.$ticket['title'].'</strong> stored successfuly</div>'
  ));
  mail($to,$subject,$newTicket,$headers);
  echo $tk_status;
  exit(0);
} catch(Exception $e) {
	$tk_status = json_encode(array(
    'status' => 'error',
    'message'=> $e->getMessage()
    ));
	mail($to, $subject, $e->getMessage(), $headers);
    echo $tk_status;
	exit(1) ;
}
*/

?>
