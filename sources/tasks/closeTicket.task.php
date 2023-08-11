<?php
/**
 * close a case 
 *
 * this file just closes a case
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
 * @copyright  2014-2016 George Dimitrakopoulos
 * @license    GPLv2
 * @version    1.0
 * @link       -
 * @see        -
 * @since      Since 0.438-dev
 * @deprecated -
 */
if (!defined('_w00t_frm')) die('har har har');
$pos = $_GET['pos'];
$storemsg = '';

if (!$pos or $pos != 'before') {
	$scerr = 'Task ['.$task.'] warning: no or wrong position of execution';
} else {
	if ($_GET['tid']) {
		$taqry = '';
		$tid = (int)$_GET['tid'];
		$today = time();
		if ($_GET['ttime']) {
			$taqry = ', updated="'.$today.'" ';
		}

		try {
            $sccon = new PDO('sqlite:pld/HyperLAB.db3');
            $sccon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
            //close status in database
            $scres = $sccon->query('UPDATE "Case" SET status = 4'.$taqry.' WHERE id = '.$tid.';');
            if ($scres) {
                $ahistory = "$today Case with id $tid is closed \n";
                $tk_status = json_encode(array(
                 'status' => 'success',
                 'message'=> 'Case with id '.$tid.' is now closed'.$storemsg.'.'
                ));
                file_put_contents('content/action_history.txt',$ahistory,FILE_APPEND); //update history file
                echo $tk_status;
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
	$tk_status = json_encode(array(
	 'status' => 'error',
	 'message'=> $scerr.'<br />'
	));
	echo $tk_status;
	exit(1);
}
?>
