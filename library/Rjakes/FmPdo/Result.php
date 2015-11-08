<?php
/**
 * @package Rjakes\FmPdo
 *
 * Copyright 2013-2015, Roger Jacques Consulting
 * See enclosed MIT license
 */
namespace Rjakes\FmPdo;

class Result
{

private $records = array();
private $fetchCount;

// @todo can refactor to be protected with getter instead?
public $dateType = array();
    
    /**
     * Result constructor.
     * @param string $table
     * @param bool $rows
     */
    public function __construct($table, $rows = false, FmPdo $fmPdo = null)
    {
        $this->fmPdo = $fmPdo;
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
              	$record->resultSet = $this;
                $this->records[$k] = $record;
            }
            $this->fetchCount = count($this->records);
        }
    }

    public function getRecords(){
        return $this->records;
    }

    public function getFirstRecord(){
        return $this->records[0];
    }

}