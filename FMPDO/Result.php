<?php
/* FMPDO Library.
 *
 * @package FMPDO
    *
 * Copyright � 2013, Roger Jacques Consulting
 * See enclosed MIT license

 */
class FMPDO_Result
{

var $_records = array();
var $_fetchCount;


    function FMPDO_Result($rows= FALSE){

        if($rows and empty($rows)){
         return new FMPDO__Error($this, 'No data was found.');
        }

        if(!$rows){
         return new FileMaker_Error($this, 'Rows missing from result.');
        }

        if($rows){
            self::addPDOrows($rows);
        }
    }

    private function addPDOrows($rows){

        if(isset($rows)){

            foreach ($rows as $row){
                $record = new FMPDO_Record($row);
                $this->_records[] = $record;
            }
            $this->_fetchCount = count($this->_records);
        }
    }

    function getRecords(){
        return $this->_records;
    }

    function getFirstRecord(){
        return $this->_records[0];
    }

}