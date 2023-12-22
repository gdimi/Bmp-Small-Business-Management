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
    function init() {
        $this->connect();      
    }
    
    function getNumOfCases($from_time,$to_time,$ex_sql) {

		try {
			$cresult = $this->getConn()->query('SELECT COUNT(0) as theAll FROM "Case" WHERE status > 3 AND updated >= '.$from_time.' AND updated <= '.$to_time.$ex_sql.';');
			if ($cresult) {
                return $cresult;
			} 
		} catch(PDOException $ex) {
			echo "An Error occured!".$ex->getMessage(); //user friendly message
		}
    }    
    
    function getIncomeTotal($from_time,$to_time,$ex_sql) {

		try {
			$cresult = $this->getConn()->query('SELECT SUM("price") as theIncome FROM "Case" WHERE status > 3 AND updated > '.$from_time.' AND updated <= '.$to_time.$ex_sql.';');
			if ($cresult) {
                return $cresult;
			} 
		} catch(PDOException $ex) {
			echo "An Error occured!".$ex->getMessage(); //user friendly message
		}
    }    
    
    function listByCaseType($from_time,$to_time,$ex_sql) {

		try {
			$cresult = $this->getConn()->query('SELECT type, COUNT(0) AS theCount FROM "Case" WHERE updated > '.$from_time.' AND updated <= '.$to_time.$ex_sql.' GROUP BY "type" ORDER BY theCount DESC;');            
			if ($cresult) {
                return $cresult;
			} 
		} catch(PDOException $ex) {
			echo "An Error occured!".$ex->getMessage(); //user friendly message
		}
    }
    
    function listByCaseTypeByTziros($from_time,$to_time,$ex_sql) {

		try {
			$cresult = $this->getConn()->query('SELECT type, COUNT(0) AS theCount, SUM("price") as theTotal FROM "Case" WHERE status > 3 AND updated > '.$from_time.' AND updated <= '.$to_time.$ex_sql.' GROUP BY "type" ORDER BY theTotal DESC;');

			if ($cresult) {
                return $cresult;
			} 
		} catch(PDOException $ex) {
			echo "An Error occured!".$ex->getMessage(); //user friendly message
		}
    }    
    
    function getTopCustomersByIncome($from_time,$to_time,$ex_join_sql,$hardLimit) {

		try {
			$cresult = $this->getConn()->query('SELECT  cl.name, cl.id, SUM("price") as theSUM FROM "Case"  AS cs INNER JOIN "Client" AS cl ON cl.id = cs.clientID WHERE cs.status > 3 AND cs.updated > '.$from_time.' AND updated <= '.$to_time.$ex_join_sql.' GROUP BY cs.clientID ORDER BY theSUM DESC LIMIT '.$hardLimit.';');
			if ($cresult) {
                return $cresult;
			} 
		} catch(PDOException $ex) {
			echo "An Error occured!".$ex->getMessage(); //user friendly message
		}
    }
    
    function getTopCustomersByNofCases($from_time,$to_time,$ex_join_sql,$hardLimit) {

		try {
			$cresult = $this->getConn()->query('SELECT  cl.name, cl.id, COUNT(*) as theCount FROM "Case"  AS cs  INNER JOIN "Client" AS cl ON cl.id = cs.clientID WHERE cs.status > 3 AND cs.updated > '.$from_time.' AND updated <= '.$to_time.$ex_join_sql.' GROUP BY cs.clientID ORDER BY theCount DESC LIMIT '.$hardLimit.';');
			if ($cresult) {
                return $cresult;
			} 
		} catch(PDOException $ex) {
			echo "An Error occured!".$ex->getMessage(); //user friendly message
		}
    }

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
