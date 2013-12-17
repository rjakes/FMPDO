<?php
/* FMPDO Library.
 *
 * @package FMPDO
    *
 * Copyright � 2013, Roger Jacques Consulting
 * See enclosed MIT license

 */
class Result
{

private $records = array();
private $fetchCount;



    function __construct($table, $rows= FALSE){

        if($rows and empty($rows)){
         return new Error('No data was found.', '401');
        }

        if(!$rows){
         return new Error('Rows missing from result.', '401');
        }


        self::addPDOrows($table, $rows);
        $this->table = $table;
    }

    private function addPDOrows($table, $rows){

        if(isset($rows)){
            foreach ($rows as $k => $row){
                $record = new Record($table, $row);
                $this->records[$k] = $record;
            }
            $this->fetchCount = count($this->records);
        }
    }

    function getRecords(){
        return $this->records;
    }

    function getFirstRecord(){
        return $this->records[0];
    }

}