<?php
namespace BMP\Database;
use PDO;

if (!defined('_w00t_frm')) die('har har har');

class Db {
	private $user;
	private $dbname;
	private $pass;
	protected $conn;
    private static $instances = array();

    // do not allow the following 
    protected function __construct() {}
    private function __clone() {}
    private function __wakeup() {}
    private function __sleep() {}    

    public static function getInstance()
    {
        $caller = get_called_class(); // late-static-bound class name
        if (!isset(self::$instances[$caller])) {
            self::$instances[$caller] = new static;
        }
        return self::$instances[$caller];
    }

	function connect() {
		try {
			$this->conn = new PDO('sqlite:pld/HyperLAB.db3');
			$this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
			return '';
		} catch(PDOException $ex) {
			return "An Error occured!".$ex->getMessage(); //user friendly message
		}
	}
	
	protected function getConn() {
		return $this->conn;
	}
	
	protected function close() {
		$this->conn = null;
	}
    
    public function getInstances() {
        return self::$instances;
    }
}

?>
