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
    private $fields = array();
    private $findCriteria = array();
    private $sortRules = array();

    /**
     * Find command constructor.
     * Assign variable and check parameter 
     */
    public function __construct($theTable) {

        $this->table = $theTable;
    }

    public function selectFields($fieldArray) {
        if (!isset($fieldArray)) {
            return new Error("Missing parameter to Find->setFields", "-1");
        }
        $this->fields = $fieldArray;
    }

    function addFindCriterion($field, $value) {
        $this->findCriteria[$field]['value'] = $value;
    }

    function addSortRule($field, $precedence, $direction='ascend') {
        $sqlOrder = strtolower($direction) == "descend" ? "DESC" : "ASC";
        $this->sortRules[$precedence]['field']= $field;
        $this->sortRules[$precedence]['direction']= $sqlOrder;
    }




    /**

     */


    //TODO move sql functions out where they can be used by other classes

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

    public function execute() {
        $db = FMPDO::getConnection();
        $selectString = self::sqlSelect($this->fields);
        $whereString = self::sqlWhere($this->findCriteria);
        asort($this->sortRules); // sort our order by statements by the precedence
        $orderString = self::sqlOrderBy($this->sortRules);

        $query = $db->prepare($selectString . ' FROM ' . $this->table . " " . $whereString ." ". $orderString);

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
