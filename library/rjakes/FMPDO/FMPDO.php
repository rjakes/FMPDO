<?php

/* FMPDO Library.
 *
 * @package FMPDO
    *
 * Copyright � 2013, Roger Jacques Consulting
 * See enclosed MIT license

 */
require_once("classes/Autoload.php");



/**
 * Base FMPDO Class
 *
 * @package FMPDO
 * handles db connection and spawning of command objects
 */
class FMPDO {

    public static $connection;
    private $error = '';
    private $locale = 'en';  //TODO move config setting out to their own file

    /**
     * FMPDO Constructor
     * @param array $sql_config  an array of settings for the db connection
     *
     */
    function __construct($db_config = array()) {

        try
        {
        self::$connection = new FmpdoDb($db_config);
        }
        catch (Exception $e)
        {
         throw $e;

        }
    }


    /**
     * @return string // the FMPDO API version
     */
    function getAPIVersion(){

        return "0.0.0";
    }


    /**
     * Test for whether or not a variable is an FmpdoError object.
     *
     * @param mixed $variable
     * @return boolean.
     * @static
     *
     */
    function isError($variable)
    {
        return is_a($variable, 'FmpdoError');
    }

    /**
     * Returns the current value of $property
     *
     * @param string name of the property
     * @return boolean.
     *
     */
    function getProperty($property) {
        return isset($this->$property) ? $this->$property : null;
    }

    /**
     * @param $property
     * @return the static connection for this instance of FMPDO
     */
    public static function getConnection() {
        return self::$connection;
    }


    /**
     * Fetches a record from the database by its id column
     *
     * @param $table the name of the sql table
     * @param $id  the value of the id/primary key
     * @return FmpdoError|FmpdoRecord
     */
    public function getRecordByID($table, $id) {
        $db = FMPDO::getConnection();
        $query = $db->prepare("SELECT *  FROM " . $table . " WHERE id="."'$id' " ."LIMIT 1" );
        try {
            if (!$query) {
                return new FmpdoError($db->errorInfo());
            }
            $result =  $query->execute();
        } catch (Exception $e) {
            return new FmpdoError($e);
        }
        $rows=$query->fetchAll();
        return new FmpdoRecord($table, $rows[0]);
    }


    /**
     * Instantiates a new FMPDO_Find object
     *
     * @param $table the sql table that the query will be performed on
     * @return FmpdoCommandFind
     */
    function newFindCommand($table) {
        $findCommand = new FmpdoCommandFind($table);
        return $findCommand;
    }


    /**
     * Instantiates a new FMPDO_Edit object
     *
     * @param $table  the sql table that the edit will be performed in
     * @param $id  // the primary key of the record that will be edited
     * @return FmpdoCommandEdit
     */
    function newEditCommand($table, $id) {
        $editCmd = new FmpdoCommandEdit($table, $id);
        return $editCmd;
    }

    function createRecord($table) {
        $record = new FmpdoRecord($table);
        return $record;
    }

}