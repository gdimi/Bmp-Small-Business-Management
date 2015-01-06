<?php
/**********************
 * update ticket task *
 **********************/

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
$ticket['title'] = $tfrm->truncate_str($ticket['title'],255);
$ticket['model'] = $tfrm->truncate_str($ticket['model'],255);
$ticket['info'] = $tfrm->truncate_str($ticket['info'],4096);
$ticket['category'] = $tfrm->truncate_str($ticket['category'],255);
$ticket['user'] = $tfrm->truncate_str($ticket['user'],64);

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

$ahistory = "${ticket['updated']} ${ticket['user']} updated case ${ticket['title']} \n";

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
        mail($to,$subject,$edTicket,$headers); //send notification mail
        file_put_contents('action_history.txt',$ahistory,FILE_APPEND); //update history file
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
