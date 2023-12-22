<?php
/**
 * class to handle statistics requests
 *
 * this file contains the class that handles statistics requests
 * 
 * PHP version 5+
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version
 *
 * @category   bmp\sources\classes
 * @package    bmp\sources
 * @author     Original Author <gdimi@hyperworks.gr>
 * @copyright  2014-2023 George Dimitrakopoulos
 * @license    GPLv2
 * @version    1.0
 * @link       -
 * @see        -
 * @since      0.733-dev
 * @deprecated -
 
 * last update 21/12/2023
 - added getNumOfCases method
 */
namespace BMP\Database;

class Stats extends Db {
    function groupByType() {
		$gbt = Array();

		try {
			$cresult = $this->getConn()->query('SELECT cs.*,cl.name FROM "Case" AS cs INNER JOIN "Client" AS cl ON  cl.id = cs.clientID');
			if ($cresult) {
				foreach($cresult as $gbt_res) {
                    //TODO
				}
			} else {
				var_dump($cresult);
			}
		} catch(PDOException $ex) {
			echo "An Error occured!".$ex->getMessage(); //user friendly message
		}
		return $gbt;
    }
    
    function medianPrice() {
        
    }
}

?>
