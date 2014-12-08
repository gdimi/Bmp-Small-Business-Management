<?php
if (!defined('_w00t_frm')) die('har har har');

/*main init file
 * loads configuration
 * registers tasks
 * handle action and position of tasks
 * executes tasks
 * load language
 * load cases and cache
 */
//load settings
require_once('sources/config.php');
$dss = new DSconfig;

//first register possible tasks
$tasks = Array();
$tasks['sclient'] = 'sclient.task';
$tasks['client'] = 'client.task';
$tasks['aclient'] = 'aclient.task';
$tasks['atk'] = 'addTicket';
$tasks['dtk'] = 'delTicket';
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
//get task and position
$task = trim($_GET['task']);
$pos = trim($_GET['pos']);
//get action
$action = trim($_GET['action']);
//check if position
if (!$pos && $task) {
	die('Fatal: no position of execution!');
}
//echo "$task | $pos <br/>";
//handle before tasks
if ($tasks[$task] && $pos == 'before') {
	$task_file = 'sources/tasks/'.$tasks[$task].'.php';
	//echo $task_file.'<br />';
	if (file_exists($task_file)) {
		require_once($task_file);
		exit();
	} else {
		echo 'Fatal: task file for ['.$task.'] not found';
	}
} else {//continue normally
	require_once('sources/class.cache.php');
	require_once('sources/class.helper.php');
	require_once('sources/class.db.php');
	require_once('sources/class.tickets.php');
	require_once('sources/class.cms.php');

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
		$tickets = $tickets_handler->readAllTicketsnew();
	}

	//show history
	if ($dss->show_history) {
		$dev_history = $tickets_handler->returnHistory();
	}

    //load cms class and get objects
    $cms = new cms;
    $cms->getMotd();
    $cms->readBoard();

	//handle after tasks
	if ($tasks[$task] && $pos == 'after') {
		$task_file = 'sources/tasks/'.$tasks[$task].'.php';
		if (file_exists($task_file)) {
			require_once($task_file);
		} else {
			echo 'Warning: task file for ['.$task.'] not found';
		}
	}

}
?>
