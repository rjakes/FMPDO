<?php


class DB extends PDO {

    public static $connection;  // holds the pdo connection

    function __construct() {
        try {
            extract(FMPDO::$sql_config);
            if(!isset($port)){
                $port= '3306';  //TODO add correct port for passed in driver
            }
            parent::__construct(sprintf('%s:host=%s;port=%s;dbname=%s', $dbType, $host, $port, $database), $user, $password);
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
