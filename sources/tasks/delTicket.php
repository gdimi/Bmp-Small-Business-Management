<?php
/**
 * delete a case 
 *
 * this file deletes a case and stores it in trash in json format
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
 * @copyright  2014-2015 George Dimitrakopoulos
 * @license    GPLv2
 * @version    1.0
 * @link       -
 * @see        -
 * @since      Since 0.375-dev
 * @deprecated -
 */
if (!defined('_w00t_frm')) die('har har har');
$pos = $_GET['pos'];

if (!$pos or $pos != 'before') {
	$scerr = 'Task ['.$task.'] warning: no or wrong position of execution';
} else {
	if ($_GET['tid']) {
		$tid = (int)$_GET['tid'];
		$today = time();
		try {
			$sccon = new PDO('sqlite:pld/HyperLAB.db3');
			$sccon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
			//first get the clients data
			$scres = $sccon->query('SELECT * FROM "Case" WHERE id = '.$tid.';');
			if ($scres) {
				$storemsg = '';
				$caseData2store = array();

				foreach ($scres as $key=>$value) {
					$caseData2store[$key] = $value;
				}
				//store into contents/trashed
				$csJSONdata = json_encode($caseData2store);
				if (file_put_contents("content/trashed/case-$tid",$csJSONdata) === false) {
					$storemsg = ',but case data store failed';
				} else {
					$storemsg = ',and case data stored at content/trashed/case-'.$tid;
				}
                //now delete from database
				$scres = $sccon->query('DELETE FROM "Case" WHERE id = '.$tid.';');
				if ($scres) {
					$ahistory = "$today Case with id $tid is deleted \n";
					$tk_status = json_encode(array(
					 'status' => 'success',
					 'message'=> 'Case with id '.$tid.' is now deleted'.$storemsg.'.'
					));
					file_put_contents('content/action_history.txt',$ahistory,FILE_APPEND); //update history file
					echo $tk_status;
					exit(0);
				}
			}
		} catch(PDOException $ex) {
			$scerr = "An Error occured!".$ex->getMessage();
		}
	} else {
		$scerr = 'No case id!';
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
