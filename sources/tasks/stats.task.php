<?php
//search for client
if (!defined('_w00t_frm')) die('har har har');

//require_once('sources/config.php');
//$dss = new DSconfig;

$caseType = $dss->caseType;

if (!$pos or $pos != 'before') {
	$scerr = 'Task ['.$task.'] warning: no or wrong position of execution';
} else {
    try { //get number of cases
        $sccon = new PDO('sqlite:pld/HyperLAB.db3');
        $sccon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
        $scall = $sccon->query('SELECT COUNT(0) as theAll FROM "Case";');
        if ($scall) {
            foreach ($scall as $all) {
                $all = $all['theAll'];
            }
            $schtml = '<strong>Σύνολο cases: '.$all.'</strong>';
        }
        //get total current income
        $scinc = $sccon->query('SELECT SUM("price") as theIncome FROM "Case" WHERE status > 3;');
        if ($scinc) {
            foreach ($scinc as $allinc) {
                $allinc = $allinc['theIncome'];
            }
            $schtml .= ' | <strong>Σύνολο τζίρου: '.$allinc.'</strong><br /><hr size="1" />';
        }
        //make list by case type
        $scres = $sccon->query('SELECT type, COUNT(0) AS theCount FROM "Case" GROUP BY "type" ORDER BY theCount  DESC;');
        if ($scres) {
            $schtml .= '<div class="case-types"><h3>Ανάλυση ανά είδος</h3>';
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
        $scres = $sccon->query('SELECT type, COUNT(0) AS theCount, SUM("price") as theTotal FROM "Case" WHERE status > 3 GROUP BY "type" ORDER BY theTotal DESC;');
        if ($scres) {
            $schtml .= '<div class="case-types"><h3>Ανάλυση ανά είδος ανά τζίρο</h3>';
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
        $scres = $sccon->query('SELECT  cl.name, SUM("price") as theSUM FROM "Case"  AS cs  INNER JOIN "Client" AS cl ON cl.id = cs.clientID WHERE cs.status > 3 GROUP BY cs.clientID ORDER BY theSUM DESC LIMIT 8;');
        if ($scres) {
            $schtml .= '<div class="case-types"><h3>Ανάλυση ανά πελάτη ανά τζίρο (τοπ 8)</h3>';
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
        $scres = $sccon->query('SELECT  cl.name, COUNT(0) as theCount FROM "Case"  AS cs  INNER JOIN "Client" AS cl ON cl.id = cs.clientID WHERE cs.status > 3 GROUP BY cs.clientID ORDER BY theCount DESC LIMIT 8;');
        if ($scres) {
            $schtml .= '<div class="case-types"><h3>Ανάλυση ανά πελάτη με αριθμό cases (τοπ 8)</h3>';
            foreach ($scres as $clcn) {
                $schtml .="
                <div>
                    <span class=\"clname\">${clcn['name']}</span> | <span class=\"stc\">${clcn['theCount']}</span>
                </div>
                ";
            }
            $schtml .="</div>";
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
