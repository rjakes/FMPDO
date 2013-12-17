<?php
/* FMPDO Library
 *
 * @package FMPDO
    *
 * Copyright � 2013, Roger Jacques Consulting
 * See enclosed MIT license

 */

class Find {

    private $table;
    private $selectFields = array();
    private $findCriteria = array();
    private $sortRules = array();
    private $limit;

    /**
     * Find command constructor.
     * Assign variable and check parameter 
     */
    public function __construct($table) {

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
    function addFindCriterion($field, $value, $operator='=') {
        $this->findCriteria[$field]['value'] = $value;
        $this->findCriteria[$field]['operator'] = $operator;

    }

    /**
     * Adds a single sort rule to the array of sort rules
     * @param $field
     * @param $precedence
     * @param string $direction // direction names are FileMaker standard for backwards compatibility
     */
    function addSortRule($field, $precedence, $direction='ascend') {
        $sqlOrder = strtolower($direction) == "descend" ? "DESC" : "ASC";
        $this->sortRules[$precedence]['field']= $field;
        $this->sortRules[$precedence]['direction']= $sqlOrder;
    }

    /**
     * Stores the optional SQL LIMIT value
     * @param $limit
     */
    function setLimit($limit) {
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
                $whereString .= $k.$op.":". $k.",";
            }
            $whereString = "WHERE ".substr($whereString, 0, -1);
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
        if(!empty($orderByArray)){
            foreach($orderByArray as $orderBy){
                $orderString .= " '". $orderBy['field']. "' " . $orderBy['direction'] .",";
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


    /**
     * Assembles the object properties and executes the SQL SELECT statement
     * @return Error|Result
     */
    public function execute() {
        $db = FMPDO::getConnection();
        $selectString = self::sqlSelect($this->selectFields);
        $whereString = self::sqlWhere($this->findCriteria);
        asort($this->sortRules); // sort our order by statements by the precedence
        $orderString = self::sqlOrderBy($this->sortRules);
        $limitString = self::sqlLimit($this->limit);

        $query = $db->prepare($selectString . ' FROM ' . $this->table . " " . $whereString ." ". $orderString  ." ". $limitString.';');

        foreach($this->findCriteria as $k=>$v){
            $query->bindParam(':'.$k, $v['value'], PDO::PARAM_STR);
        }

        try {
            if (!$query) {
                return new Error($db->errorInfo());
            }
            $result =  $query->execute();
        } catch (Exception $e) {
            return new Error($e);
        }
        return new Result($this->table, $query->fetchAll());
    }

}
