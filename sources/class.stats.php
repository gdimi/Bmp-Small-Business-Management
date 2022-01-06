<?php
namespace BMP\Core;

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
