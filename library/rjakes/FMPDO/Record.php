<?php
/* FMPDO Library
 *
 * @package FMPDO
    *
 * Copyright � 2013, Roger Jacques Consulting
 * See enclosed MIT license

 */

/**
 * Record class, a container for a new record or a database row that has been retrieved
 */
class Record
{
    private $table;
    private $recordid;
    private $fields = array();
    private $relatedSets = array();
    
    public $resultSet = null;

    /**
     * A recordid of null indicates a new record that will be inserted upon commit
     * A non null ID indicates an existing record that will be updated upon commit
     * @param $theTable
     * @param array $pdoRow
     */
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


    }

    /** Sets all field values from passed row
     * row must be an associative array fetch from PDO
     * @param $pdo_row
     */
    function setFieldsFromPDOrow($pdo_row)
    {
        if(isset($pdo_row) and is_array($pdo_row))
        {
            foreach($pdo_row as $k => $v){
             $this->fields[$k] = Array($v);
            }
        }
    }

    /** Fetches the specified field value from the object
     * @param $field
     * @param int $repetition
     * @return value|Error
     */
    function getField($field, $repetition = 0)
    {
    	return $this->fields[$field][$repetition];
        if(isset($this->fields[$field][$repetition]) ||
        		(array_key_exists($field,$this->fields) && array_key_exists($repetition, $this->fields[$field])) ){
            return $this->fields[$field][$repetition];
        }else{
            return new Error("Failed to retrieve value for column '".$field."'");
        }
    }
    
    public function getAllFields(){
    	return $this->fields;
    }

    /** Converts value of passed field to a time stamp
     * Only supports column types of timestamp, date and time
     * @param $field
     * @param int $repetition
     * @return Error|int
     */
    function getFieldAsTimestamp($field, $repetition = 0,$force = false)
    {
        $fieldValue = $this->getField($field, $repetition);
        if ($force) return strtotime($fieldValue);
        if (FMPDO::isError($fieldValue)) {
            return $fieldValue;
        }
        $hasDate = substr_count($fieldValue, '-') == 2; TRUE; FALSE;
        $hasTime = substr_count($fieldValue, ':') >= 2; TRUE; FALSE;

        if($hasDate && $hasTime)
        {
            // try to convert as a timestamp value
            $timestamp = @strtotime($fieldValue);
            if ($timestamp === false) {
                return new Error('Failed to convert "' . $fieldValue . '" to a UNIX timestamp.');
            }
        }
        elseif($hasDate)
        {
            // try to convert as a date value
            $fieldValueArray = explode('-', $fieldValue);
            if (count($fieldValueArray) != 3) {
                return new Error('Failed to parse "' . $fieldValue . '" as a date value.');
            }
            $timestamp = @mktime(0, 0, 0, $fieldValueArray[1], $fieldValueArray[2], $fieldValueArray[0]);
            if ($timestamp === false) {
                return new Error('Failed to convert "' . $fieldValue . '" to a UNIX timestamp.');
            }

        }
        elseif($hasTime)
        {
            // try to convert as a time value
            $fieldValueArray = explode(':', $fieldValue);
            if (count($fieldValueArray) < 3) {   // allow microtime, though we will ignore it
                return new Error('Failed to parse "' . $fieldValue . '" as a time value.');
            }
            $timestamp = @mktime($fieldValueArray[0], $fieldValueArray[1], $fieldValueArray[2], 1, 1, 1970);
            if ($timestamp === false) {
                return new Error('Failed to convert "' . $fieldValue . '" to a UNIX timestamp.');
            }
        }
        else{
            return new Error('The value supplied for '.$field.' ('.$fieldValue .') cannot be converted to a UNIX timestamp.');
        }

        return $timestamp;
    }


    /**
     * Set a single field value in the record
     * @param $field
     * @param $value
     * @param int $repetition
     */
    function setField($field, $value, $repetition= 0)
    {
        $this->fields[$field][$repetition] = $value;
    }


    /**
     * Fetches the recordid, which is the same as the id column
     * Returns NULL for new records that have not been committed yet
     * @return bool|null
     */
    function getRecordId()
    {
        if(isset($this->recordid)){
            return $this->recordid;
        }else{
            return NULL;
        }
    }

    /**
     * Saves new or existing records to the database
     * New records are inserted, and the recordid is set back into the object
     * Records that resulted from a prior query are updated
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