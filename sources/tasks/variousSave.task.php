<?php
/**
 * update various content
 *
 * this file updates the contents of  content/various.html. Translates entities like &lt; so that writing html is possible
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

$scerr = ''; //var to hold error msg. Initialized here so functions below can access it by addr

if (!$pos or $pos != 'before') {
	$scerr = 'Task ['.$task.'] warning: no or wrong position of execution';
} else {
    if (isset($_POST['varData'])) { 
		$varData = html_entity_decode($_POST['varData'],ENT_COMPAT | ENT_HTML5,"UTF-8"); //convert html entities
		file_put_contents("content/various.html"," "); //erase old data
		$evar = file_put_contents("content/various.html",$varData, LOCK_EX); //put new with a lock on file
		if ($evar === false) {
			$scerr = 'Failed to store data!';
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
} else {
    $tk_status = json_encode(array(
    'status'=>'success',
    'message'=>'various page updated successfully'
    ));
    echo $tk_status;
    exit(0);
}
?>
