<?php
/**
 * @package Rjakes\FmPdo
 *
 * Copyright 2013-2015, Roger Jacques Consulting
 * See enclosed MIT license
 */
namespace Rjakes\FmPdo;

use PDO;

class Find {
	/**
	 * @var FmPdo
	 */
	private $fmPdo;
	private $table;
	private $selectFields = array();
	private $findCriteria = array();
	private $sortRules = array();
	private $limit;

	/**
	 * Find command constructor.
	 * Assign variable and check parameter
	 */
	public function __construct($table, FmPdo $fmPdo = null){
		$this->fmPdo = $fmPdo;
		$this->table = $table;
	}

	/**
	 * Adds field to be used in the SQL select, to limit the return
	 * @param $fieldArray
	 * @return NULL | Error
	 */
	public function setSelectFields($fieldArray) {
		if (!isset($fieldArray) or !is_array($fieldArray)) {
			return new Error("Missing parameter to Find->setSelectFields", "-1");
		}
		$this->selectFields = $fieldArray;
	}

	/**
	 * Adds a find criteria with an optional find operator
	 * @param $field
	 * @param $value
	 */
	public function addFindCriterion($field, $value, $operator='=') {
		$this->findCriteria[$field]['value'] = $value;
		$this->findCriteria[$field]['operator'] = $operator;

	}

	/**
	 * Adds a single sort rule to the array of sort rules
	 * @param $field
	 * @param $precedence
	 * @param string $direction // direction names are FileMaker standard for backwards compatibility
	 */
	public function addSortRule($field, $precedence, $direction='ascend') {
		$sqlOrder = strtolower($direction) == "descend" ? "DESC" : "ASC";
		$this->sortRules[$precedence] = array('field' => $field, 'direction' => $sqlOrder);
	}

	/**
	 * Stores the optional SQL LIMIT value
	 * @param $limit
	 */
	public function setLimit($limit) {
		$this->limit = $limit;
	}


	/**
	 * Transforms the passed select fields into SQL
	 * @param $fieldArray
	 * @return string
	 */
	private function sqlSelect($fieldArray){
		$selectString = "SELECT ";
		if (empty($fieldArray)) {
			$selectString .= '*';
		}else{

			foreach ($fieldArray as $k => $v) {
				$selectString .= $v . ',';
			}
			$selectString = substr($selectString, 0, -1);
		}
		return $selectString;
	}


	/**
	 * Transforms the passed find criteria into SQL
	 * @param $whereArray
	 * @return string
	 */
	private function sqlWhere($whereArray) {
		$whereString = "";
		if(!empty($whereArray)){
			foreach($whereArray as $k=>$v){
				$op = isset($v['operator']) ? $v['operator'] : "=";
				$whereString .= $k.$op.":". $k." AND ";
			}
			$whereString = "WHERE ".substr($whereString, 0, -5);
		}
		return $whereString;
	}


	/**
	 * Transforms the passed orderby array into SQL
	 * @param $orderByArray
	 * @return string
	 */
	private function sqlOrderBy($orderByArray) {
		$orderString = "";
        ksort($orderByArray);
		if(!empty($orderByArray)){
			foreach($orderByArray as $orderBy){
				$orderString .= " `". $orderBy['field']. "` " . $orderBy['direction'] .",";
			}
			$orderString = "ORDER BY".substr($orderString, 0, -1);
		}
		return $orderString;
	}

	/**
	 * Transforms passed limit value into SQL
	 * @param $limit
	 * @return string
	 */
	private function sqlLimit($limit)
	{
		if(intval($limit)){
			return "LIMIT " . intval($limit);
		}
	}

	public function setRelatedSetsFilters(){
		//this function is supposedly like a limit.
		//@see FileMaker_Command_Find
		//we don't support this limit function right now.
	}


	public function assemble() {
		$selectString = $this->sqlSelect($this->selectFields);
		$whereString = $this->sqlWhere($this->findCriteria);
		$orderString = $this->sqlOrderBy($this->sortRules);
		$limitString = $this->sqlLimit($this->limit);
		return $selectString . ' FROM ' . $this->table . " " . $whereString ." ". $orderString  ." ". $limitString.';';
	}



	/**
	 * Assembles the object properties and executes the SQL SELECT statement
	 * @return Error|Result
	 */
	public function execute() {
		$db = $this->fmPdo->getConnection();
		try {
			$query = $db->prepare($this->assemble());
			if (!$query) {
				return new Error($db->errorInfo());
			}
			foreach($this->findCriteria as $k=>$v){
				$query->bindParam(':'.$k, $v['value'], PDO::PARAM_STR);
			}
			$result =  $query->execute();
		} catch (Exception $e) {
			return new Error($e);
		}

        $rows = $query->fetchAll();

        if(count($rows) > 0){
            return new Result($this->table, $rows);
        }else{
             return new Error("No records found", "401");
        }

	}

}
