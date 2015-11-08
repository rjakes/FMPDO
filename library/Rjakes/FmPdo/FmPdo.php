<?php
/**
 * @package Rjakes\FmPdo
 *
 * Copyright 2013-2015, Roger Jacques Consulting
 * See enclosed MIT license
 */
namespace Rjakes\FmPdo;

use Exception;

/**
 * Base FmPdo Class
 *
 * @package FmPdo
 * handles db connection and spawning of command objects
 */
class FmPdo
{

    protected $connection;
    private $error = '';
    private $locale = 'en';  //TODO move config setting out to their own file

    /**
     * FmPdo constructor.
     * @param array $dbConfig // an array of settings for the db connection
     */
    public function __construct($dbConfig = array())
    {
        try {
            if (array_key_exists('dsn', $dbConfig)) {
                $dsn = $dbConfig['dsn'];
            } else {
                $dsn = $dbConfig['driver']
                    . ':'
                    . $dbConfig['host']
                    . ':port='
                    . $dbConfig['port']
                    . ';dbname='
                    . $dbConfig['database'];
            }
            $this->connection = new Connect($dsn, $dbConfig['username'], $dbConfig['password']);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function setConnection($pdo)
    {
        $this->connection = $pdo;
    }


    /**
     *
     * Returns the API Version.
     *
     * In the form X.Y.Z, X for Major, Y for Minor (compatible APIs), Z for bug corrections.
     * @return string the FmPdo API version
     */
    public static function getAPIVersion()
    {

        return "0.1.0";
    }


    /**
     * Test for whether or not a variable is an Error object.
     *
     * @param mixed $results
     * @return boolean.
     * @static
     *
     */
    public static function isError($results)
    {
        return ($results instanceof Error);
    }

    /**
     * Returns the current value of $property.
     *
     * This is for retro-compatibility only and we highly discourage the usage of this method in new code.
     *
     * For instance, to get $this->test, call $this->getProperty('test');
     *
     * @param string $property // name of the property
     * @return mixed|null
     *
     */
    public function getProperty($property)
    {
        return isset($this->$property) ? $this->$property : null;
    }

    /**
     * @return Connect
     */
    public function getConnection()
    {
        return $this->connection;
    }


    /**
     * Fetch a record from the database by its primary key column
     * @param string $table // the name of the sql table
     * @param string $id // the value of the id/primary key
     * @return Error|Record
     */
    public function getRecordByID($table, $id)
    {
        $db = $this->getConnection();
        $query = $db->prepare("SELECT * FROM $table WHERE id='$id' LIMIT 1;");
        try {
            if (!$query) {
                return new Error($db->errorInfo());
            }
            $result = $query->execute();
        } catch (Exception $e) {
            return new Error($e);
        }
        $row = $query->fetch();
        return new Record($table, $row);
    }


    /**
     * @param string $table // the sql table that the query will be performed on
     * @return Find
     */
    public function newFindCommand($table)
    {
        $findCommand = new Find($table);
        return $findCommand;
    }

    /**
     * This method is for backwards compatibility
     * @param string $table // the sql table that the query will be performed on
     * @return Find
     */
    public function newFindAllCommand($table)
    {
        $findCommand = new Find($table);
        return $findCommand;
    }


    /**
     * @param string $table // the sql table that the edit will be performed in
     * @param string $id // the primary key of the record that will be edited
     * @return Edit
     */
    public function newEditCommand($table, $id)
    {
        $editCmd = new Edit($table, $id);
        return $editCmd;
    }

    /**
     * @param $table
     * @return Record
     */
    public function createRecord($table)
    {
        $record = new Record($table);
        return $record;
    }
}
