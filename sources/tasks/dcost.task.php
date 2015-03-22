<?php
/**
 * delete a cost 
 *
 * this file deletes a cost 
 * 
 * PHP version 5.2+
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
	if ($_GET['cid']) {
		$cid = (int)$_GET['cid'];
		$today = time();
		try {
			$sccon = new PDO('sqlite:pld/HyperLAB.db3');
			$sccon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
			$scres = $sccon->query('DELETE FROM "costs" WHERE id = '.$cid.';');
			if ($scres) {
				$ahistory = "$today Cost with id $cid is deleted \n";
				$c_status = json_encode(array(
				 'status' => 'success',
				 'message'=> 'Η λυπητερή με id '.$cid.' έχει ΔΙΑΓΡΑΦΕΙ.'
				));
				file_put_contents('content/action_history.txt',$ahistory,FILE_APPEND); //update history file
				echo $c_status;
				exit(0);
			}
		} catch(PDOException $ex) {
			$scerr = "An Error occured!".$ex->getMessage();
		}
	} else {
		$scerr = 'No case id!';
	}
}

if ($scerr) {
	$c_status = json_encode(array(
	 'status' => 'error',
	 'message'=> $scerr.'<br />'
	));
	echo $c_status;
	exit(1);
}
?>
