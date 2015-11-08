<?php
/**
 * @package Rjakes\FmPdo
 *
 * Copyright 2013-2015, Roger Jacques Consulting
 * See enclosed MIT license
 */
namespace Rjakes\FmPdo;

use Exception;
use PDO;

class Edit
{
    /**
     * @var FmPdo
     */
    private $fmPdo;
    private $table;
    private $id;
    private $fields = array();

    /**
     * Edit constructor.
     * @param $theTable
     * @param $id
     * @param FmPdo|null $fmPdo
     */
    public function __construct($theTable, $id, FmPdo $fmPdo = null){
        $this->fmPdo = $fmPdo;
        $this->table = $theTable;
        $this->id = $id;
    }

    /**
     * @param $field
     * @param $value
     * @return null|Error
     */
    public function setField($field, $value){
        if(!isset($field) or !isset($value)){
            return new Error("Missing parameter to Edit->setField", "-1");
        }

        $this->fields[$field][0] = $value;

        return null;
    }

    /**
     * @param $field_array
     * @return null|Error
     */
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

        return null;
    }

    /**
     * @return bool|Error
     */
    public function execute(){

        $set_string = "";
        foreach($this->fields as $k=>$v){
            $set_string .= $k."=:".$k .",";
        }
        $set_string = substr($set_string, 0, -1);

        $sql = 'UPDATE '.$this->table.' SET ' . $set_string. ' WHERE id=:id';

        $db = $this->fmPdo->getConnection();

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
