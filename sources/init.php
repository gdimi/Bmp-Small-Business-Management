<?php
if (!defined('_w00t_frm')) die('har har har');

/*main init file
 * loads configuration
 * init some globally used variables
 * registers tasks
 * handle action and position of tasks
 * executes tasks
 * load language
 * load cases and cache
 */

//load settings
require_once('sources/config.php');
$dss = new DSconfig;

//set timezone
date_default_timezone_set($dss->timezone);

//globally used variables
$thisYear = date("Y");
$curTimestamp = time();
$defUploadDir = 'content/uploads';

//init vars
$task = '';
$pos = '';
$action = '';

//first register possible tasks
$tasks = Array();
$tasks['sclient'] = 'sclient.task';
$tasks['client'] = 'client.task';
$tasks['aclient'] = 'aclient.task';
$tasks['dclient'] = 'dclient.task';
$tasks['eclient'] = 'eclient.task';
$tasks['clvcf'] = 'client2vcf.task';
$tasks['atk'] = 'addTicket';
$tasks['dtk'] = 'delTicket';
$tasks['ctk'] = 'closeTicket.task';
$tasks['acl'] = 'allClients.task';
$tasks['etk'] = 'editTicket.task';
$tasks['cis'] = 'caseIdSearch.task';
$tasks['esoda'] = 'esoda.task';
$tasks['costs'] = 'costs.task';
$tasks['acost'] = 'acost.task';
$tasks['ecost'] = 'ecost.task';
$tasks['onecs'] = 'getOneCost.task';
$tasks['csd'] = 'dcost.task';
$tasks['stats'] = 'stats.task';
$tasks['cms'] = 'cms.task';
$tasks['cmsupd'] = 'cmsUpdate.task';
$tasks['evar'] = 'variousSave.task';
$tasks['uvar'] = 'variousUpdate.task';
$tasks['trash'] = 'trash.task';
$tasks['trashObj'] = 'trashObj.task';
$tasks['upload'] = 'upload.task';

//get task and position
if (isset($_GET['task']) && $_GET['task'] != '') $task = trim($_GET['task']);
if (isset($_GET['pos']) && $_GET['pos'] !='') $pos = trim($_GET['pos']);
//get action
if (isset($_GET['action']) && $_GET['action'] != '') $action = trim($_GET['action']);

//check if position
if (!$pos && $task) {
	$task_status = json_encode(array(
	 'status' => 'error',
	 'message'=> 'Fatal: no position of execution! for ['.$task.'] <br />'
	));
	echo $task_status;
	exit(1);
}
//handle before tasks
if ($task != '' && $tasks[$task] && $pos == 'before') {
	$task_file = 'sources/tasks/'.$tasks[$task].'.php';
	//echo $task_file.'<br />';
	if (file_exists($task_file)) {
		require_once($task_file);
		exit();
	} else {
		$task_status = json_encode(array(
		 'status' => 'error',
		 'message'=> 'Fatal: task file for ['.$task.'] not found<br />'
		));
		echo $task_status;
		exit(1);
	}
} elseif ($task && in_array($task,$tasks) == false) {
	$task_status = json_encode(array(
	 'status' => 'error',
	 'message'=> 'Uknown task '.$task
	));
	echo $task_status;
	exit(2);
} else {//continue normally
	require_once('sources/class.cache.php');
	require_once('sources/class.helper.php');
	require_once('sources/class.db.php');
	require_once('sources/class.tickets.php');
	require_once('sources/class.cms.php');
	require_once('sources/class.trash.php');

	//now load language
	if ($dss->lang) {
		$activeLanguage = $dss->lang;
	} else {
		$activeLanguage = "gr";
	}

	$lang = array();

	include_once('language/'.$activeLanguage.'.php');

	//load tickets and cache
	$tickets_handler = tickets::getInstance();
	$tickets_handler->attachDir = $defUploadDir;
    if ($dss->show_closed == 1) {
        $tickets_handler->sclosed = 10;
    } else {
        $tickets_handler->sclosed = 4;
    }

	$cache = new Cache;
	$cache->cachefile = 'tickets.html';
	$cache->cacheInit();

	//check cache file state and act accordingly
	if ($cache->getState() == 1 && $action != 'docache') {
		//$tickets = file_get_contents($cache->cachefilename);
	} else {
		if ($cache->getState() == 0 && $action != 'docache') { // there should be a cache file so throw an error and later try to create one
			echo 'cache not working?<br />state:'.$cache->getState().'<br />error:'.$cache->error; //TODO: check zero filesize also
		}
		try {
			$tickets_handler->connect();
		} catch(Exception $e) {
			echo $e->getMessage();
		}

		//check if tickets are to be sorted
		if (isset($_GET['sr'])) {
			$sort = $_GET['sr']; //TODO: better security
		}

		$tickets = $tickets_handler->readAllTicketsnew($sort);
	}

	//show history
	if ($dss->show_history) {
		$dev_history = $tickets_handler->returnHistory();
	}

    //load cms class and get objects
    $cms = new cms;
    $cms->getMotd();
    $cms->readBoard();

    //load trash 
    if (is_dir("content/trashed")) {
        $Trash = new Trash;
        $Trash->initTrash();
        $trashSize = round(($Trash->trashSize)/1024,2);
        $trashFiles = $Trash->trashFiles;
        if ($Trash->trashErr) {
            echo $Trash->trashErr;
        }
    } else {
        $scerr = 'Trash folder not found';
    }

	//handle after tasks
	if ($task !='' && $tasks[$task] && $pos == 'after') {
		$task_file = 'sources/tasks/'.$tasks[$task].'.php';
		if (file_exists($task_file)) {
			require_once($task_file);
		} else {
			echo 'Warning: task file for ['.$task.'] not found';
		}
	}

}
?>
