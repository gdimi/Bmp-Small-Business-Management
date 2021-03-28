<?php
/**
 * export a clients cases to csv
 *
 * this file gets the client's cases from db and outputs it in a csv file
 * 
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   bmp\sources\ajax handlers
 * @package    bmp\sources
 * @author     Original Author <gdimi@hyperworks.gr>
 * @copyright  2014-2019 George Dimitrakopoulos
 * @license    GPLv2
 * @version    1.0
 * @link       -
 * @see        -
 * @since      Since 0.579-dev
 */
if (!defined('_w00t_frm')) die('har har har');
$pos = $_GET['pos'];

if (!$pos or $pos != 'before') {
	$scerr = 'Task ['.$task.'] warning: no or wrong position of execution';
} else {
	if ($_GET['clid']) {
		$clid = (int)$_GET['clid'];
		if ($clid > 0) { 
			$delimiter = ",";
			$filename = "cases_" . date('Y-m-d') . ".csv";
			
			//create a file pointer
			$f = fopen('php://memory', 'w');

			//set column headers
			$fields = array('id','title','model','info','clientID','category','priority','type','status','created','updated','user','price','follow','closed');
			fputcsv($f, $fields, $delimiter);

			try {
				$sccon = new PDO('sqlite:pld/HyperLAB.db3');
				$sccon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
				$scres = $sccon->query('SELECT * FROM "Case" WHERE clientID = '.$clid.' ORDER BY updated DESC;');
/*id title model info clientID category priority type status created updated user price follow closed */
				if ($scres) {
					foreach ($scres as $ccl) {
						/*$updated = date('Y/m/d',$ccl['updated']);
						$created = date('Y/m/d',$ccl['created']);
						if ($ccl['type'] < 10) { //if id less than ten add a zero so to fix id length to 2 chars
							$cct = '0'.$ccl['type'];
						} else {
							$cct = $ccl['type'];
						}
						$cases .='<div>'.$updated.' '.$ccl['title'].' <a href="javascript:void(0);" class="ccl'.$cct.$ccl['id'].'">( '.$cct.$ccl['id'].' )</a></div>';*/
						$lineData = array($ccl['id'],$ccl['title'],$ccl['model'],$ccl['info'],$ccl['clientID'],$ccl['category'],$ccl['priority'],$ccl['type'],$ccl['status'],$ccl['created'],$ccl['updated'],$ccl['user'],$ccl['price'],$ccl['follow'],$ccl['closed']);
						fputcsv($f, $lineData, $delimiter);
					}
				}
				//move back to beginning of file
				fseek($f, 0);
					
				//set headers to download file rather than displayed
				header('Content-Type: text/csv');
				header('Content-Disposition: attachment; filename="' . $filename . '";');

				//output all remaining data on a file pointer
				fpassthru($f);
			} catch(PDOException $ex) {
				$scerr = "An Error occured!".$ex->getMessage();
			}
		}
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
