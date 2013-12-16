<?php


use \PDO as PDO;

class Connect extends PDO {

	/**
	 * Creates a database connection with the correct options for emulating FileMaker.
	 * 
	 * Its is a specialization of PDO.
	 *
	 * @param string $dsn see PDO documentation for format
	 * @throws PDOException
	 */
	function __construct($dsn,$username,$password,$options=array()) {
		try {
			parent::__construct($dsn, $username, $password, $options);
			$this->setAttribute(parent::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES UTF8');
			$this->setAttribute(parent::ATTR_ERRMODE, parent::ERRMODE_EXCEPTION);
			$this->setAttribute(parent::ATTR_EMULATE_PREPARES, false);
			$this->setAttribute(parent::ATTR_DEFAULT_FETCH_MODE, parent::FETCH_ASSOC);
		} catch (PDOException $e) {
			throw $e;

		}
	}

}