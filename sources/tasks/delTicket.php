<?php
/**
 * delete a case 
 *
 * this file deletes a case and stores it in trash in json format
 * 
 * PHP version 5
 *
 * LICENCE: This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version
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
				$objData2store = array();

				foreach ($scres as $key=>$value) {
                    foreach ($value as $kkey => $vvalue) {
                        if (is_numeric($kkey)) continue; //FIXME why there are these numeric keys in result?
                        $objData2store[$kkey] = $vvalue;
                    }
				}

				//store into contents/trashed
				$objJSONdata = json_encode($objData2store);
				if (file_put_contents("content/trashed/case-$tid",$objJSONdata) === false) {
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
