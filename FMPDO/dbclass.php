<?php

final class dbclass extends PDO {

    public function __construct(array $params) {
        try {
            extract($params);
            if(!isset($port)){
                $port= '3306';  //TODO add correct port for passed in driver
            }
            parent::__construct(sprintf('%s:host=%s;port=%s;dbname=%s', $dbType, $host, $port, $database), $user, $password);
            $this->setAttribute(parent::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES UTF8');
            $this->setAttribute(parent::ATTR_ERRMODE, parent::ERRMODE_EXCEPTION);
            $this->setAttribute(parent::ATTR_EMULATE_PREPARES, false);
            $this->setAttribute(parent::ATTR_DEFAULT_FETCH_MODE, parent::FETCH_ASSOC);
        } catch (PDOException $e) {

            die("Could not connect with database!".$e->getMessage());
        }
    }

}
