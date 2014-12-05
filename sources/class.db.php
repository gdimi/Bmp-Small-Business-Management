<?php
if (!defined('_w00t_frm')) die('har har har');

class db {
	private $user;
	private $dbname;
	private $pass;
	protected $conn;
    private static $instances = array();
    protected function __construct() {}
    protected function __clone() {}

    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    public static function getInstance()
    {
        $cls = get_called_class(); // late-static-bound class name
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static;
        }
        return self::$instances[$cls];
    }

	function connect() {
		try {
			$this->conn = new PDO('sqlite:pld/HyperLAB.db3');
			$this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
			//echo 'connected';
		} catch(PDOException $ex) {
			echo "An Error occured!".$ex->getMessage(); //user friendly message
		}
	}
	
	protected function getConn() {
		return $this->conn;
	}
	
	protected function close() {
		$this->conn = null;
	}
}

?>
