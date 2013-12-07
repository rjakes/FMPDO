<?php
/**
 * FMPDO Library.
 *
 * @package FMPDO
 *
 * Copyright � 2013, Roger Jacques Consulting
 * See enclosed MIT license
 */


class FmpdoCommandEdit
{

    var $table;
    var $id;
    var $fields = array();


    public function __construct($theTable, $id){

        $this->table = $theTable;
        $this->id = $id;
    }

    public function setField($field, $value){
        if(!isset($field) or !isset($value)){
            return new FmpdoError("Missing parameter to FmpdoCommandEdit->setField", "-1");
        }

        $this->fields[$field][0] = $value;
    }

    public function setFields($field_array){
        if(!isset($field_array) or !isset($value)){
            return new FmpdoError("Missing parameter to FmpdoCommandEdit->setFields", "-1");
        }
        if(!is_array($field_array) or !is_array($field_array[0])){
            return new FmpdoError("FmpdoCommandEdit->setFields parameter must be array of arrays", "-1");
        }

        foreach($field_array as $field){
            $field_name = $field['field'];
            $field_value = $field['value'];
            $this->fields[$field_name][0] = $field_value;

        }

    }

    public function execute(){
        $table = $this->fields;
        $id = $this->id;
        $field_array = $this->fields;

        $set_string = "";
        foreach($field_array as $k=>$v){    //TODO change into implode()
            $set_string .= $k."='".$v[0] ."',";
        }
        $set_string = substr($set_string, 0, -1);

        $sql = 'UPDATE '.$table.' SET ' . $set_string. ' WHERE id=\''. $id . "'";

        $db = FmpdoDb::getConnection();
        $query = $db->prepare($sql);

        foreach($field_array as $k=>$v){
            $query->bindParam(':'.$k, $v[0], PDO::PARAM_STR);
        }

        try{
            if (!$query) {
                return new FmpdoError($this->fmpdo->errorInfo());
            }
            $result =  $query->execute();
        } catch (Exception $e) {
            return new FmpdoError($e);
        }

        return $result;

    }

}