<?php
/* FMPDO Library.
 *
 * @package FMPDO
    *
 * Copyright � 2013, Roger Jacques Consulting
 * See enclosed MIT license

 */

require_once(__DIR__ . '/Error.php');

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
    public function __construct($theTable) {

        $this->table = $theTable;
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
                $selectString .= $k . ',';
            }
            $selectString = substr($selectString, 0, -1);
        }
        return $selectString;
    }


    /**
     * Transforms the passed find criteria into SQL
     * @param $where_array
     * @return string
     */
    private function sqlWhere($where_array) {
        $whereString = "";
        if(!empty($where_array)){
            foreach($where_array as $k=>$v){
                $op = isset($v['operator']) ? $v['operator'] : "=";
                $whereString .= $k. " " .$op ." :". $k .",";
            }
            $whereString = "WHERE ".substr($whereString, 0, -1);
        }
        return $whereString;
    }


    /**
     * Transforms the passed orderby array into SQL
     * @param $orderby_array
     * @return string
     */
    private function sqlOrderBy($orderby_array) {

        $orderString = "";
        if(!empty($orderby_array)){
            foreach($orderby_array as $order_by){
                $orderString .= " '". $order_by['field']. "' " . $order_by['direction'] .",";
            }
            $orderString = "ORDER BY".substr($orderString, 0, -1);
        }
        return $orderString;
    }

    private function sqlLimit($limit)
    {
        if(intval($limit)){
            return "LIMIT " . intval($limit);
        }
    }


    public function execute() {
        $db = FMPDO::getConnection();
        $selectString = self::sqlSelect($this->selectFields);
        $whereString = self::sqlWhere($this->findCriteria);
        asort($this->sortRules); // sort our order by statements by the precedence
        $orderString = self::sqlOrderBy($this->sortRules);
        $limitString = self::sqlLimit($this->limit);

        $query = $db->prepare($selectString . ' FROM ' . $this->table . " " . $whereString ." ". $orderString  ." ". $limitString);

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
