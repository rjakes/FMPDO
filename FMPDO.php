<?php

require_once(__DIR__ . '/FMPDO/dbclass.php');
require_once(__DIR__ . '/FMPDO/Record.php');
require_once(__DIR__ . '/FMPDO/Result.php');
require_once(__DIR__ . '/FMPDO/Edit.php');
require_once(__DIR__ . '/FMPDO/Error.php');
require_once(__DIR__ . '/FMPDO/Find.php');

class FMPDO {

    var $fmpdo;
    var $findCmd;
    var $result = false;
    var $error = '';
    var $locale = 'en';  //TODO move config setting out to their own file

    function __construct($sql_config = array()) {

        // build the db connection from the included db class file

        if (!empty($sql_config)) {
            try {
                $this->db = new dbclass($sql_config);
            } catch (Exception $e) {
                $this->$error = 'Could not connect with database!' . $e;
            }

            $this->sql_config = $sql_config;
        } else {
            $this->error = 'Class instantiated without sql config array';
        }
    }

    function isError($variable)
    {
        return is_a($variable, 'FMPDO_Error');
    }


    function getProperty($property) {
        return isset($this->$property) ? $this->$property : null;
    }

    public function getRecordByID($table, $id) {
        $query = $this->db->prepare("SELECT *  FROM " . $table . " WHERE id="."'$id' " ."LIMIT 1" );
        try {
            if (!$query) {
                return new FMPDO_Error($this->db->errorInfo());
            }
            $result =  $query->execute();
        } catch (Exception $e) {
            return new FMPDO_Error($e);
        }
        $rows=$query->fetchAll();
        return new FMPDO_Record($rows[0]);
    }

    function newFindCommand($table) {
        $findCommand = new FMPDO_Command_Find($this->sql_config, $table);
        return $findCommand;
    }


    function newEditCommand($table, $id) {
        $editCmd = new FMPDO_Command_Edit($table, $id, $this->sql_config);
        return $editCmd;
    }

}