<?php
/* FMPDO Library.
 *
 * @package FMPDO
    *
 * Copyright � 2013, Roger Jacques Consulting
 * See enclosed MIT license

 */
class Record
{
    private $table;
    private $recordid;
    private $fields = array();
    private $relatedSets = array();

    function __construct($theTable, $pdoRow= array())
    {

        $this->table = $theTable;
        if(!empty($pdoRow) and !isset($pdoRow['id'])){
            return new Error("id column is required for Record Object");
        }
        if(!empty($pdoRow)){
            $this->recordid = $pdoRow['id'];
            self::setFieldsFromPDOrow($pdoRow);
        }else{
            $this->recordid = NULL;
        }
    // a recordid of null indicates a new record that must be inserted upon commit
    // a non null ID indicates an existing record that must be updated upon commit

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


    function setField($field, $value, $repetition= 0)
    {

            $this->fields[$field][$repetition] = $value;

    }


    function getRecordId()
    {
        if(isset($this->recordid)){
            return $this->recordid;
        }else{
            return FALSE;
        }
    }

    /**
     * Saves new or existing records to the database
     */
    function commit()
    {
        $db = FMPDO::getConnection();

        $columnNv = array();
        foreach($this->fields as $k => $v)
         {
             if($k != 'id')
             {
                $columnNv[$k] = $v[0];
             }
         }

        if($this->recordid==NULL)
        {
            // this is a new record, need to insert
            $columnString = implode(',', array_keys($columnNv));
            $valueString = implode(',', array_fill(0, count($columnNv), '?'));
            $query = $db->prepare("INSERT INTO ".$this->table." ($columnString) VALUES ($valueString)");
            $query->execute(array_values($columnNv));
            $this->recordid = $db->lastInsertId('id');
        }else{
          // this is an existing record, need to update
            $setString = "";
            foreach($columnNv as $k=>$v)
            {
                $setString .= $k."=?,";
            }
            $setString = substr($setString, 0, -1);

            $query = $db->prepare("UPDATE ".$this->table."  SET $setString WHERE id=".$this->recordid);
            $query->execute(array_values($columnNv));

        }
    }

}