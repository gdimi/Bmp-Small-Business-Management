<?php
/**
 * delete a file
 *
 * this file deletes a file previously uploaded and attached to a case
 * 
 * PHP version 5+
 *
 * LICENCE: This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version
 *
 * @category   bmp\sources\ajax handlers
 * @package    bmp\sources
 * @author     Original Author <gdimi@hyperworks.gr>
 * @copyright  2014-2017 George Dimitrakopoulos
 * @license    GPLv2
 * @version    1.0
 * @link       -
 * @see        -
 * @since      Since 0.589-dev
 * @deprecated -
 */
if (!defined('_w00t_frm')) die('har har har');

if (!$pos or $pos != 'before') {
	$scerr = 'Task ['.$task.'] warning: no or wrong position of execution';
} else {
	$to = $_GET['file'];
	if ($file) {	
		include_once('sources/class.filesystem.php');
		if (is_file($file)) {
			$fs = new Filesystem;				
			if (!$fs->fsErr) {
				$tk_status = json_encode(array(
				 'status'=>'success',
				 'data' => 'file is erased!'
				));
				echo $tk_status;
				exit(0);
			} else {
				$scerr = $fs->fsErr;
			}
		} else {
			$scerr = 'File not found';
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
