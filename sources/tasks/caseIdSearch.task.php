<?php
//delete a ticket
if (!defined('_w00t_frm')) die('har har har');
$pos = $_GET['pos'];

if (!$pos or $pos != 'before') {
	$scerr = 'Task ['.$task.'] warning: no or wrong position of execution';
} else {
	if ($_GET['ci']) {
		$cid = $_GET['ci'];
		if (is_numeric($cid)) {
            require_once('sources/config.php');
            $dss = new DSconfig;
			$cid = substr($cid,2);
			try {
				$sccon = new PDO('sqlite:pld/HyperLAB.db3');
				$sccon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
				$scres = $sccon->query('SELECT cs.*,cl.name FROM "Case" AS cs INNER JOIN "Client" AS cl ON  cl.id = cs.clientID WHERE cs.id='.$cid.' LIMIT 1;');
				if ($scres) {
					foreach ($scres as $case) {
                        $msg = '<h2>'.$case['title'].'</h2><table>';
                        $tstat = $dss->caseStatus[$case['status']];
                        $tstat_class = str_replace(' ','_',$tstat);
                        $ttype = $dss->caseType[$case['type']];
                        $tdate = date("j/m/Y H:i:s",$case['created']);
                        $tudate = date("j/m/Y H:i:s",$case['updated']);
                        if ($case['priority'] == 1) { $tprior = 'Low'; } elseif ($case['priority'] == 2) { $tprior = 'Medium'; } else { $tprior = 'High'; }
                        $ticket_data .=  "
                        <tr class=\"$tstat_class $tprior ${case['user']}\">
                            <td>Id</td><td  style=\"background-color:white\">${case['id']}</td>
                        </tr>
                        <tr>
                            <td>Created</td><td style=\"background-color:white\">$tdate</td>
                        </tr>
                        <tr>
                            <td>Updated</td><td style=\"background-color:white\">$tudate</td>
                        </tr>
                        <tr>
                            <td>Model/SN</td><td style=\"background-color:white\">${case['model']}</td>
                        </tr>
                        <tr>
                            <td>Info</td><td style=\"background-color:white\">${case['info']}</td>
                        </tr>
                        <tr>
                            <td>Tag</td><td style=\"background-color:white\">${case['category']}</td>
                        </tr>
                        <tr>
                            <td>Client</td><td style=\"background-color:white\"><a href=\"javascript:void(0);\" id=\"cl_s${case['clientID']}\" class=\"cclient\">${case['name']}</a></td>
                        </tr>
                        <tr>
                            <td>Status</td><td class=\"ct-stat${case['status']}\" style=\"background-color:white\">${tstat}</td>
                        </tr>
                        <tr>
                            <td>Priority</td><td class=\"ct-$tprior\">$tprior</td>
                        </tr>
                        <tr>
                            <td>Type</td><td class=\"ct-type${case['type']}\" style=\"background-color:white\">${ttype}</td>
                        </tr>
                        <tr>
                            <td>Price</td><td class=\"ct-price\" style=\"background-color:white\">${case['price']}</td>
                        </tr>
                        <tr>
                            <td>User</td><td class=\"ct-user\" style=\"background-color:white\">${case['user']}</td>
                        </tr>
                        <tr>
                            <td>Action</td><td style=\"background-color:white\"><span class=\"del-tck\" id=\"dt_ss${case['id']}\">D</span></td>
                        </tr>";
					}
                    $msg .= $ticket_data.'</table>';
					if ($msg == null) {// what if there is no such case?
						$msg = 'No such case found re ntervisi';
					}
					$tk_status = json_encode(array(
					 'status' => 'success',
					 'message'=> $msg
					));
					echo $tk_status;
					exit(0);
				}
			} catch(PDOException $ex) {
				$scerr = "An Error occured!".$ex->getMessage();
			}
		} else {
			$scerr = 'Case id must be numeric!';
		}
	} else {
		$scerr = ' No case id found!';
	}
}

if ($scerr) {
	$tk_status = json_encode(array(
	 'status' => 'error',
	 'message'=> $scerr.'<br />'
	));
	echo $tk_status;
	exit(1);
}
?>
