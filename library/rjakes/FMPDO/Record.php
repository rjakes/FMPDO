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

    function __construct($theTable, $pdo_row= array())
    {

        $this->table = $theTable;
        if(!empty($pdo_row) and !isset($pdo_row['id'])){
            return new Error("id column is required for Record Object");
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

    function commit()
    {
        $table = $this->table;
        $id = $this->recordid;
        $db = FMPDO::getConnection();

        $colum_nv = array();
        foreach($this->fields as $k => $v)
         {
             if($k != 'id')
             {
                $column_nv[$k] = $v[0];
             }
         }



        if($id===FALSE)
        {
            // this is a new record, need to insert
            $columnString = implode(',', array_keys($column_nv));
            $valueString = implode(',', array_fill(0, count($column_nv), '?'));
            $query = $db->prepare("INSERT INTO ".$table." ({$columnString}) VALUES ({$valueString})");
            $query->execute(array_values($column_nv));
            $this->recordid = $db->lastInsertId('id');
        }else{
          // this is an existing record, need to update
            $setString = "";
            foreach($column_nv as $k=>$v)
            {
                $setString .= $k."=?,";
            }
            $setString = substr($setString, 0, -1);

            $query = $db->prepare("UPDATE ".$table."  SET {$setString} WHERE id=".$id);
            $query->execute(array_values($column_nv));

        }




    }


}