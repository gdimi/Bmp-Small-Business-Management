<?php
//show trends for customers, cases etc
if (!defined('_w00t_frm')) die('har har har');

$caseType = $dss->caseType;

$from_time = '';
$to_time = '';
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
    
    /*for ($i = $yearStart;$i <= $yearNow;$i++) {
         
        $totalPerYear = 0;
         
        $from_time = strtotime("$i-01-01 00:00:00");
        $to_time = strtotime("$i-12-31 23:59:59");
        //print_r($from_time)."\n\n";
        //print_r($to_time)."\n\n";
        $qry = 'SELECT type, COUNT(0) AS theCount, SUM("price") as theTotal FROM `Case` WHERE status > 3 AND updated > '.$from_time.' AND updated <= '.$to_time.$ex_sql.' GROUP BY type ORDER BY type DESC;';

        //echo $i.'-'.$qry.'<br>';

        $scres = $sccon->query($qry);
        if ($scres) {
            //$schtml .= '<div class="case-types"><h3>'.$lang['stats-listbytypebygross'].'</h3>';
            foreach ($scres as $ctli) {
                $ctype = $caseType[$ctli['type']];
                //$perc = ($ctli['theTotal']/$allinc)*100;
                //$perc = round($perc,2);
                //$barWidth = round($perc * 2.8);
                
                $cases["$i"][$ctype] = $ctli['theTotal'];
                
                $totalPerYear += $ctli['theTotal'];
            }
            
            $cases["$i"]['total'] = $totalPerYear;
        }
        
        //print_r($qry);
    }
    
    //print_r($cases);*/
    
    
    $ttspy = calcTotalStatsPerYear($sccon,$yearStart,$yearNow,$ex_sql);
    
    $dataY = $ttspy;
    $dataQ = calcTotalStatsPerQuarter($sccon,$yearStart,$yearNow,$ex_sql);
}

//Calculate total stats per year
function calcTotalStatsPerYear (&$sccon,$firstYear,$thisYear,$ex_sql) {
    $StatsPerYear = [];
    $years = [];
    $income = [];
    $cases = [];
    $IPC = [];
    
    $income["Income"] = array();
    $cases["Cases"] = array();
    $IPC["IncomePerCase"] = array();
    
    $income["Income"]["amount"] = array();
    $cases["Cases"]["num"] = array();
    $IPC["IncomePerCase"]["IPC"] = array();
    
    for ($i = $firstYear; $i <= $thisYear; $i++) {
        
        $years[] = "$i";
        
        $scres = $sccon->query('SELECT count(0) as totalYcases, SUM("price") as totalYtziros FROM "Case" WHERE "status" > 3 AND datetime("Case"."updated",\'unixepoch\') >= \''.$i.'-01-01 00:00:00\' AND datetime("Case"."updated",\'unixepoch\') <= \''.$i.'-12-31 23:59:59\''.$ex_sql.';');
        
        if ($scres) {
            
            foreach ($scres as $totals) {
                //var_dump($totals['totalYcases']);

                
                $income["Income"]["amount"][] = $totals['totalYtziros'];
                $cases["Cases"]["num"][] = $totals['totalYcases']*10;
                $IPC["IncomePerCase"]["IPC"][] = round(($totals['totalYtziros'] / $totals['totalYcases']),2)*10;
            }
        }
    }
    
    $income["Years"] = $years;
    $cases["Years"] = $years;
    $IPC["Years"] = $years;
    
    $statsPerYear[] = $income;
    $statsPerYear[] = $cases;
    $statsPerYear[] = $IPC;
    
    
    return $statsPerYear;
}


function calcTotalStatsPerQuarter (&$sccon,$firstYear,$thisYear,$ex_sql) {
    $StatsPerQ = [];
    $years = [];
    $quarters = [];
    $income = [];
    $cases = [];
    $IPC = [];
    
    $income["Income"] = array();
    $cases["Cases"] = array();
    $IPC["IncomePerCase"] = array();
    
    $income["Income"]["amount"] = array();
    $cases["Cases"]["num"] = array();
    $IPC["IncomePerCase"]["IPC"] = array();   

    for ($i = $firstYear; $i <= $thisYear; $i++) {
        
        $years[] = "$i";

        $monthFrom = 1;
        $monthTo = 3;
               
        for ($q = 1; $q <= 4; $q++) {
            
            //these will be our labels in the frontend graph
            if ($q == 1) {
                $quarters[] = "$i - Q$q";
            } else {
                $quarters[] = "Q$q";
            }
                       
            //1 - 3   12 - 11 | 12/12 -> 12 - 9 | 12/4
            //4 - 6   12 - 8 | 12/3  -> 12 - 6 | 12/2
            //7 - 9   12 - 5 | ???   -> 12 - 3 | ???
            //10 - 12 12 - 2 | ???   -> 12 - 0 | 12/12

            $monthFromSql = "$monthFrom";
            $monthToSql = "$monthTo";
            
            //add a zero in front of month if < 10
            if ($monthFrom < 10) {
                $monthFromSql = "0"."$monthFrom";
            }
            
            if ($monthTo < 10) {
                $monthToSql = "0"."$monthTo";
            }
            
            $scres = $sccon->query('SELECT count(0) as totalYcases, SUM("price") as totalYtziros FROM "Case" WHERE "status" > 3 AND datetime("Case"."updated",\'unixepoch\') >= \''.$i.'-'.$monthFromSql.'-01 00:00:00\' AND datetime("Case"."updated",\'unixepoch\') <= \''.$i.'-'.$monthToSql.'-31 23:59:59\''.$ex_sql.';');
        
           // echo 'SELECT count(0) as totalYcases, SUM("price") as totalYtziros FROM "Case" WHERE "status" > 3 AND datetime("Case"."updated",'."'unixepoch') >= '".$i.'-'.$monthFrom."-01 00:00:00' AND datetime(".'"Case"."updated",'."'unixepoch') <= '".$i.'-'.$monthTo."-31 23:59:59'".$ex_sql.';';
        
            if ($scres) {
                
                //echo $i.'-'.$monthFromSql."-01 00:00:00 - ".$i.'-'.$monthToSql."-31 23:59:59' <br>";
                
                foreach ($scres as $totals) {
                    //var_dump($totals);
                    
                    $income["Income"]["amount"][] = $totals['totalYtziros'];
                    $cases["Cases"]["num"][] = $totals['totalYcases']*10;
                    if ($totals['totalYtziros'] > 0 && $totals['totalYcases'] > 0) {
                        $IPC["IncomePerCase"]["IPC"][] = round(($totals['totalYtziros'] / $totals['totalYcases']),2)*10;
                    } else {
                        $IPC["IncomePerCase"]["IPC"][] = 0;
                    }
                }
            }
            
            $monthFrom = $monthFrom + 3;
            $monthTo = $monthTo + 3;
        }        
    }
    
    $StatsPerQ[] = $income; 
    $StatsPerQ[] = $cases; 
    $StatsPerQ[] = $IPC; 
    $StatsPerQ[] = $quarters;     

    return $StatsPerQ;
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
     'dataY'=> json_encode($dataY),
     'dataQ'=> json_encode($dataQ)
    ));
    echo $tk_status;
    exit(0);    
}
?>
