<?php
/* Main API endpoint */

namespace BMP\Database;

if (!defined('_w00t_frm')) die('har har har');

$scerr = false;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

if (!$pos or $pos != 'before') {
	$scerr = 'Task ['.$task.'] warning: no or wrong position of execution';
} else {
    /*echo 'descr:'.$api->getDescr();
    echo '<br> state:'.$api->getState();
    echo '<br> msg:'. $api->lastMsg();
    echo '<br> error:'. $api->errorState();*/

    $command = ''; //which command are we going to rung
    $dontSetState = true; //do we want to set a new state, hence update cookie??
    
    if (isset($_GET['comm']) && $_GET['comm'] != '') {
        $command = $_GET['comm'];
        if (isset($_GET['dss']) && (int)$_GET['dss'] == 0) { //set state if dss is zero
            $dontSetState = false;
        }
    } else { //no command is not accepted
        http_response_code(404);
        echo json_encode(array("message" => "42")); //the answer to everything
        exit(1);
    }
    

    require_once('sources/class.db.php');
    require_once('sources/class.api.php');
    
    $api = BMPApi::getInstance();
    $api->init($dontSetState);

    switch ($command) {
        case 'version':
            http_response_code(200);
            echo json_encode(array("message" => $api->version()));
            break;
        case 'hello':
            http_response_code(200);
            echo json_encode(array("message" => $api->getDescr()));
            break;
        case 'getstate':
            http_response_code(200);
            echo json_encode(array("message" => $api->getState()));
            break;
        case 'errorstate':
            http_response_code(200);
            echo json_encode(array("message" => $api->errorState()));
            break;
        case 'returnStatesErrors':
            http_response_code(200);
            echo json_encode(array("message" => $api->returnStatesErrors()));
            break;
        case 'lastmsg':
            http_response_code(200);
            echo json_encode(array("message" => $api->lastMsg()));
            break;
        case 'readlog':
            http_response_code(200);
            echo json_encode(array("message" => $api->readLog()));
            break;
        case 'getclient':
        case 'getclients':
        case 'getcase':
        case 'getcases':
        case 'getmotd':
        case 'getboard':
        case 'getvarious':
            http_response_code(418);
            break;
        default:
            http_response_code(400);
            echo json_encode(array("message" => "Uknown command"));
    }
}

if ($scerr) {
    http_response_code(503);
	$tk_status = json_encode(array(
	 'status' => 'error',
	 'message'=> $scerr.'<br />'
	));
	echo $tk_status;
	exit(1);
}
?>
