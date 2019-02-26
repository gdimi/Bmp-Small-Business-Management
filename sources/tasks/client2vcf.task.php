<?php
/**
 * export a clients info to vcf card
 *
 * this file gets the client's info from db and outputs it in vcf format
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
		$cid = (int)$_GET['clid'];
		try {
			$sccon = new PDO('sqlite:pld/HyperLAB.db3');
			$sccon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
			$scres = $sccon->query('SELECT id,name,tel1,tel2,email,address FROM "Client" WHERE id='.$cid.';');
			if ($scres) {
				$msg = '<div class="grid">';
				foreach ($scres as $client) {
					//vcf data
					$vcf = utf8_encode('BEGIN:VCARD
							VERSION:4.0
							N:;'.$client['name'].';;;
							FN:'.$client['name'].'
							EMAIL:'.$client['email'].'
							ORG:'.$client['name'].'
							TEL:'.$client['tel1'].'
							TEL;type=FAX:'.$client['tel2'].'
							URL;type=PREF:
							ADR:;'.$client['address'].';;;;;Greece
							END:VCARD');
				}

				//handle name
				$filename = str_replace(' ','_',$client['name']).'.vcf';

				//set headers to download file rather than displayed
				header('Content-Type: text/vcard');
				header('Content-Disposition: attachment; filename="' . $filename . '";');
				echo $vcf;

			}
		} catch(PDOException $ex) {
			$scerr = "An Error occured!".$ex->getMessage();
		}
	}
}

if ($scerr) {
	echo $scerr;
	exit(1);
}
?>
