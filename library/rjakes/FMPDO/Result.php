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
         return new Error($this, 'No data was found.');
        }

        if(!$rows){
         return new FileMaker_Error($this, 'Rows missing from result.');
        }


        self::addPDOrows($table, $rows);
        $this->table = $table;
    }

    private function addPDOrows($table, $rows){

        if(isset($rows)){

            foreach ($rows as $row){
                $record = new Record($table, $row);
                $this->records[] = $record;
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