<?php
/**
 *
 * this file contains the code that handles statistics requests
 * 
 * PHP version 5+
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version
 *
 * @category   bmp\sources\tasks
 * @package    bmp\sources
 * @author     Original Author <gdimi@hyperworks.gr>
 * @copyright  2014-2023 George Dimitrakopoulos
 * @license    GPLv2
 * @version    1.0
 * @link       -
 * @see        -
 * @since      0.733-dev
 * @deprecated -
 
 * last update 21/12/2023
 - moved query for number of closed cases to class stats  
 */
namespace BMP\Database;

if (!defined('_w00t_frm')) die('har har har');

require_once('sources/class.stats.php');

$caseType = $dss->caseType;

$from_time = '';
$to_time = $curTimestamp;
$sc_err = '';
$scerr = '';

//check if any client is to be excluded from stats-gross
$ex_clients = '';
$ex_sql = '';
$ex_join_sql = '';

//limits
$hardLimit = 500; // actual sql query limit
$softLimit = 10; // limit for result loops 

//master client array and usefull vars
$clArray = [];
$totalCasesThreshold = 5; //used on calculating the VPC (Value Per Case) for clients

//exclude from stats selected clients in config
if (count($dss->exclude_from_stats)) {
	$ex_clients = implode(',',$dss->exclude_from_stats);
	$ex_sql = " AND `ClientID` NOT IN ('".$ex_clients."')";
	$ex_join_sql = " AND cs.ClientID NOT IN ('".$ex_clients."')";
}


//see if there's a year we're intrested in
if (isset($_GET['iy']) && $_GET['iy'] > 0) {
    
    if ((int)$_GET['iy'] < 12) { // one of the btns 
        $days = 30 * (int)$_GET['iy'];
        $from_time = strtotime(date('Y-m-d', strtotime('-'.$days.' days')));
    } else { // yearly from select dropdown
        $from_time = $_GET['iy'];
        $year_only = date('Y',$from_time);

        if (strtotime($year_only.'-01-01 00:00:00') < strtotime($thisYear.'-01-01 00:00:00')) {
            $to_time = strtotime("$year_only-12-31 23:59:59");
        }
    }
} else {
	$from_time = strtotime($dss->startYear.'-01-01 00:00:00');
}

if (!$pos or $pos != 'before') {
	$scerr = 'Task ['.$task.'] warning: no or wrong position of execution';
} else {
	//check if lang is set and if not load english
	$lang = array();
    
	if (isset($_GET['lang'])) {
		$language = trim(strip_tags($_GET['lang']));
		include("language/${language}.php");
	} else {
		include("language/en.php");
	}
    
	try { //get number of cases
        $statsInstance = Stats::getInstance();
        $statsInstance->init();

        $scall = $statsInstance->getNumOfCases($from_time,$to_time,$ex_sql);
        //$scall = $sccon->query('SELECT COUNT(0) as theAll FROM "Case" WHERE status > 3 AND updated >= '.$from_time.' AND updated <= '.$to_time.$ex_sql.';');
        
        if ($scall) {
            foreach ($scall as $all) {
                $all = $all['theAll'];
            }
            $schtml = '<strong>'.$lang['stats-nofcases'].' '.$all.'</strong>';
        }
		if ($all > 0) {
			//get total current income
			$scinc = $statsInstance->getIncomeTotal($from_time,$to_time,$ex_sql);
			if ($scinc) {
				foreach ($scinc as $allinc) {
					$allinc = $allinc['theIncome'];
				}
				$schtml .= ' | <strong>'.$lang['stats-gross'].' '.$allinc.'</strong><br /><hr size="1" />';
			}
			//make list by case type
			$scres = $sccon->query('SELECT type, COUNT(0) AS theCount FROM "Case" WHERE updated > '.$from_time.' AND updated <= '.$to_time.$ex_sql.' GROUP BY "type" ORDER BY theCount  DESC;');
			if ($scres) {
				$schtml .= '<div class="case-types"><h3>'.$lang['stats-listbytype'].'</h3>';
				foreach ($scres as $ctl) {
					$ctype = $caseType[$ctl['type']];
					$perc = ($ctl['theCount']/$all)*100;
					$perc = round($perc,2); 
					$barWidth = round($perc * 2.8);
					$schtml .="
					<div>
						<span class=\"sctype\">$ctype</span> | <span class=\"stc\">${ctl['theCount']}</span><span class=\"sprc\">$perc %</span><span class=\"spbar\" style=\"width:${barWidth}px\">&nbsp;</span>
					</div>
					";
				}
				$schtml .="</div>";
			} else {
				$scerr = "An error occured in list by case!";
			}
			// get list by case by tziros 
			$scres = $sccon->query('SELECT type, COUNT(0) AS theCount, SUM("price") as theTotal FROM "Case" WHERE status > 3 AND updated > '.$from_time.' AND updated <= '.$to_time.$ex_sql.' GROUP BY "type" ORDER BY theTotal DESC;');
			if ($scres) {
				$schtml .= '<div class="case-types"><h3>'.$lang['stats-listbytypebygross'].'</h3>';
				foreach ($scres as $ctli) {
					$ctype = $caseType[$ctli['type']];
					$perc = ($ctli['theTotal']/$allinc)*100;
					$perc = round($perc,2);
					$barWidth = round($perc * 2.8);
					$schtml .="
					<div>
						<span class=\"sctype\">$ctype</span> | <span class=\"stc\">${ctli['theTotal']}</span><span class=\"sprc\">$perc %</span><span class=\"spbar\" style=\"width:${barWidth}px\">&nbsp;</span>
					</div>
					";
				}
				$schtml .="</div>";
			}
			// get top 8 customers by total income and display
			$scres = $sccon->query('SELECT  cl.name, SUM("price") as theSUM FROM "Case"  AS cs INNER JOIN "Client" AS cl ON cl.id = cs.clientID WHERE cs.status > 3 AND cs.updated > '.$from_time.' AND updated <= '.$to_time.$ex_join_sql.' GROUP BY cs.clientID ORDER BY theSUM DESC LIMIT 10;');
			if ($scres) {
				$schtml .= '<div class="case-types"><h3>'.$lang['stats-listbyclient'].'</h3>';
				foreach ($scres as $cli) {
					$schtml .="
					<div>
						<span class=\"clname\">${cli['name']}</span> | <span class=\"stc\">${cli['theSUM']}</span>
					</div>
					";
				}
				$schtml .="</div>";
			}
			// get top 8 customers by # of cases and display
			$scres = $sccon->query('SELECT  cl.name, COUNT(0) as theCount FROM "Case"  AS cs  INNER JOIN "Client" AS cl ON cl.id = cs.clientID WHERE cs.status > 3 AND cs.updated > '.$from_time.' AND updated <= '.$to_time.$ex_join_sql.' GROUP BY cs.clientID ORDER BY theCount DESC LIMIT 10;');
			if ($scres) {
				$schtml .= '<div class="case-types"><h3>'.$lang['stats-listbyclient-noc'].'</h3>';
				foreach ($scres as $clcn) {
					$schtml .="
					<div>
						<span class=\"clname\">${clcn['name']}</span> | <span class=\"stc\">${clcn['theCount']}</span>
					</div>
					";
				}
				$schtml .="</div>"; //lang:".$_GET['lang'];
			}
		}
    } catch(PDOException $ex) {
        $scerr = "An Error occured!".$ex->getMessage();
    }
}

if ($scerr) {
	$tk_status = json_encode(array(
	 'status' => 'error',
	 'message'=> $scerr.'<br />'
	));
	echo $tk_status;
	exit(1);
} else {
    $tk_status = json_encode(array(
     'status' => 'success',
     'message'=> $schtml
    ));
    echo $tk_status;
    exit(0);    
}
?>
