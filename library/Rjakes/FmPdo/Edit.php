<?php
/**
 * FMPDO Library
 *
 * @package FMPDO
 *
 * Copyright � 2013, Roger Jacques Consulting
 * See enclosed MIT license
 */


class Edit
{

    private $table;
    private $id;
    private $fields = array();


    public function __construct($theTable, $id){

        $this->table = $theTable;
        $this->id = $id;
    }

    public function setField($field, $value){
        if(!isset($field) or !isset($value)){
            return new Error("Missing parameter to Edit->setField", "-1");
        }

        $this->fields[$field][0] = $value;
    }

    public function setFields($field_array){
        if(!isset($field_array) or !isset($value)){
            return new Error("Missing parameter to Edit->setFields", "-1");
        }
        if(!is_array($field_array) or !is_array($field_array[0])){
            return new Error("Edit->setFields parameter must be array of arrays", "-1");
        }

        foreach($field_array as $field){
            $field_name = $field['field'];
            $field_value = $field['value'];
            $this->fields[$field_name][0] = $field_value;
        }

    }

    public function execute(){

        $set_string = "";
        foreach($this->fields as $k=>$v){
            $set_string .= $k."=:".$k .",";
        }
        $set_string = substr($set_string, 0, -1);

        $sql = 'UPDATE '.$this->table.' SET ' . $set_string. ' WHERE id=:id';

        $db = FMPDO::getConnection();

        try
        {
            $query = $db->prepare($sql);
        }
        catch (Exception $e)
        {
            return new Error($e);
        }

        $query->bindParam(':id', $this->id, PDO::PARAM_STR);

        foreach($this->fields as $k=>$v){
            $query->bindParam(':'.$k, $v[0], PDO::PARAM_STR);
        }

        try{
            if (!$query) {
                return new Error($this->fmpdo->errorInfo());
            }
            $result =  $query->execute();
        } catch (Exception $e) {
            return new Error($e);
        }

        return $result;

    }

}
