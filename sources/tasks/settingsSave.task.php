<?php
/**
 * save settings taks
 *
 * this file gets the contents of  content/various.html.
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
 * @copyright  2014-2021 George Dimitrakopoulos
 * @license    GPLv2
 * @version    1.0
 * @link       -
 * @see        -
 * @since      Since 0.733
 * @deprecated -
 */
use BMP\Core; 

if (!defined('_w00t_frm')) die('har har har');

$scerr = ''; //var to hold error msg. Initialized here so functions below can access it by addr

if (!$pos or $pos != 'before') {
	$scerr = 'Task ['.$task.'] warning: no or wrong position of execution';
} else {
	require_once('sources/class.filesystem.php');
	include_once('sources/class.settings.php');
	
	$settings = new Settings(array(),$thisYear);
	$settings->dss = $dss;
	
	$res = $settings->processSettings();
	
	if (is_array($res)) {
		if ($res['error'] == true) {
			$scerr = $res['message'];
		} 
	} else {
		$scerr = 'Unknown result';
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
    'status'=>'success',
    'message'=>$res['message']
    ));
    echo $tk_status;
    exit(0);
}
?>
