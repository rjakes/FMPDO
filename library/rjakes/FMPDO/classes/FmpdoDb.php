<?php



class FmpdoDb extends PDO {

    public static $connection;  // holds the pdo connection

    function __construct() {
        try {
            extract(FMPDO::$db_config);
            if(!isset($port)){
                $port= '3306';  //TODO add correct port for passed in driver
            }
            parent::__construct(sprintf('%s:host=%s;port=%s;dbname=%s', $driver, $host, $port, $database), $user, $password);
            $this->setAttribute(parent::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES UTF8');
            $this->setAttribute(parent::ATTR_ERRMODE, parent::ERRMODE_EXCEPTION);
            $this->setAttribute(parent::ATTR_EMULATE_PREPARES, false);
            $this->setAttribute(parent::ATTR_DEFAULT_FETCH_MODE, parent::FETCH_ASSOC);

        } catch (PDOException $e) {

            return ("Connection failed ".$e->getMessage());
        }
    }

    public static function getConnection(){
        if(!self::$connection){
            self::$connection = new self();
        }
        return self::$connection;
    }

}