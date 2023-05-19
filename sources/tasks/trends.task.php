<?php
//show trends for customers, cases etc
if (!defined('_w00t_frm')) die('har har har');

$caseType = $dss->caseType;

$from_time = '';
$to_time = $curTimestamp;
$sc_err = '';
$scerr = '';

//check if any client is to be excluded from stats-gross
$ex_clients = '';
$ex_sql = '';
$ex_join_sql = '';

if (!$pos or $pos != 'before') {
	$scerr = 'Task ['.$task.'] warning: no or wrong position of execution';
} else {
    if (count($dss->exclude_from_stats)) {
        $ex_clients = implode(',',$dss->exclude_from_stats);
        $ex_sql = " AND `ClientID` NOT IN ('".$ex_clients."')";
        $ex_join_sql = " AND cs.ClientID NOT IN ('".$ex_clients."')";
    }
    
    
    $yearNow = (int)date('Y');
    $yearStart = (int)$dss->startYear;
    
    $from_time = strtotime($dss->startYear.'-01-01-00:00');

    $clients = array();
    $cases = array();
    
    $sccon = new PDO('sqlite:pld/HyperLAB.db3');
    $sccon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );

    // get top 8 customers by total income and display
    /*$scres = $sccon->query('SELECT  cl.name, SUM("price") as theSUM FROM "Case"  AS cs INNER JOIN "Client" AS cl ON cl.id = cs.clientID WHERE cs.status > 3 AND cs.updated > '.$from_time.' AND updated <= '.$to_time.$ex_join_sql.' GROUP BY cs.clientID ORDER BY theSUM DESC LIMIT 12;');
    if ($scres) {
        foreach ($scres as $cli) {
            $clients["${cli['name']}"] = $cli['theSUM'];
        }
    }
   print_r($clients);*/
    $labels = array();
    // get list by case by tziros 
    //var_dump((int)$dss->startYear);
    //var_dump($yearNow);
    for ($i = $yearStart;$i <= $yearNow;$i++) {
         
        $from_time = strtotime("$i-01-01-00:00");
        $to_time = strtotime("$i-12-31-59:59");
        //print_r($from_time)."\n\n";
        //print_r($to_time)."\n\n";
        $qry = 'SELECT type, COUNT(0) AS theCount, SUM("price") as theTotal FROM "Case" WHERE status > 3 AND updated > '.$from_time.' AND updated <= '.$to_time.$ex_sql.' GROUP BY "type" ORDER BY type DESC;';
        $scres = $sccon->query($qry);
        if ($scres) {
            //$schtml .= '<div class="case-types"><h3>'.$lang['stats-listbytypebygross'].'</h3>';
            foreach ($scres as $ctli) {
                $ctype = $caseType[$ctli['type']];
                $perc = ($ctli['theTotal']/$allinc)*100;
                $perc = round($perc,2);
                $barWidth = round($perc * 2.8);
                
                $cases["$i"][$ctype] = $ctli['theTotal'];
            }
        }
        
        //print_r($qry);
    }
    
    //print_r($cases);
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
     'data'=> json_encode($cases)
    ));
    echo $tk_status;
    exit(0);    
}
?>
