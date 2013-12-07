<?php
/* FMPDO Library.
 *
 * @package FMPDO
    *
 * Copyright � 2013, Roger Jacques Consulting
 * See enclosed MIT license

 */
class FmpdoRecord
{
    private $table;
    private $recordid;
    private $fields = array();
    private $relatedSets = array();

    function __construct($theTable, $pdo_row= array())
    {

        $this->table = $theTable;
        if(!empty($pdo_row) and !isset($pdo_row['id'])){
            return new FmpdoError("id column is required for FmpdoRecord Object");
        }
        if(!empty($pdo_row)){
            $this->recordid = $pdo_row['id'];
            self::setFieldsFromPDOrow($pdo_row);
        }else{
            $this->recordid = FALSE;
        }
    // a recordid of false indicates a new record that must be inserted upon commit
    // a non false ID indicates an existing record that must be updated upon commit

    }

    function setFieldsFromPDOrow($pdo_row)
    {
        if(isset($pdo_row) and is_array($pdo_row))
        {

            foreach($pdo_row as $k => $v){
             $this->fields[$k] = Array($v);
            }
        }
    }

    function getField($field, $repetition= 0)
    {
        if(isset($this->fields[$field][$repetition])){
            return $this->fields[$field][$repetition];
        }else{
            return FALSE;
        }
    }


    function getRecordId()
    {
        if(isset($this->recordid)){
            return $this->recordid;
        }else{
            return FALSE;
        }
    }
}