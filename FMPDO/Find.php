<?php
/* FMPDO Library.
 *
 * @package FMPDO
    *
 * Copyright � 2013, Roger Jacques Consulting
 * See enclosed MIT license

 */

require_once(__DIR__ . '/Error.php');

class FMPDO_Command_Find extends FMPDO {

    var $_table;
    var $_fmpdo;
    var $_sql;
    var $_fields = array();
    var $_findCriteria = array();
    var $_sortRules = array();

    /**
     * Find command constructor.
     * Assign variable and check parameter 
     */
    public function FMPDO_Command_Find($sql_config, $table) {
        if (!empty($sql_config))
            parent::__construct($sql_config);
        if (!isset($table)) {
            return new FMPDO_Error("Missing parameter to FMPDO_Command_Find", "-1");
        }
        $this->_table = $table;
    }

    public function selectFields($field_array) {
        if (!isset($field_array)) {
            return new FMPDO_Error("Missing parameter to FMPDO_Command_Find->setFields", "-1");
        }
        $this->_fields = $field_array;
    }

    function addFindCriterion($field, $value) {
        $this->_findCriteria[$field]['value'] = $value;
        // TODO - add support for operator in third parameter:  $this->_findCriteria[$field]['operator'] = $operator;
    }

    function addSortRule($field, $precedence, $direction='ascend') {
        $sqlOrder = strtolower($direction) == "descend" ? "DESC" : "ASC";
        $this->_sortRules[$precedence]['field']= $field;
        $this->_sortRules[$precedence]['direction']= $sqlOrder;
    }


    public function setCriterion($cmdObj, $column, $value) {

        if (!isset($column) or !isset($value)) {
            return new FMPDO_Error("Missing parameter to addFindCriterion", "-1");
        }
        $sqlWhere = $this->sqlWhere($column, $value);
        $sql = sprintf('%s %s', $cmdObj->queryString, $sqlWhere);
        $query = $this->db->prepare($sql);
        try {
            if (!$query) {
                return new FMPDO_Error($this->db->errorInfo());
            }
        } catch (Exception $e) {
            return new FMPDO_Error($e);
        }
        return $query;
    }


    /**

     */
    public function setSortRule($cmdObj, $column, $precedence, $order = null) {

        if (!isset($column)) {
            return new FMPDO_Error("Missing parameter to addSortRule", "-1");
        }
        $sqlSortRule = $this->sqlSort($column, $precedence, $order);
        $sql = sprintf('%s %s', $cmdObj->queryString, $sqlSortRule);
        $query = $this->db->prepare($sql);
        try {
            if (!$query) {
                return new FMPDO_Error($this->db->errorInfo());
            }
        } catch (Exception $e) {
            return new FMPDO_Error($e);
        }
        return $query;
    }

    //TODO move sql functions out where they can be used by other classes

    private function sqlSelect($field_array){
        $select_string = "SELECT ";
        if (empty($field_array)) {
            $select_string .= '*';
        }else{

            foreach ($field_array as $k => $v) {
                $select_string .= $k . ',';
            }
            $select_string = substr($select_string, 0, -1);
        }
        return $select_string;
    }


    private function sqlWhere($where_array) {
        $where_string = "";
        if(!empty($where_array)){
            foreach($where_array as $k=>$v){
                $op = isset($v['operator']) ? $v['operator'] : "=";
                $where_string .= $k. " " .$op ." '". $v['value'] ."',";
            }
            $where_string = "WHERE ".substr($where_string, 0, -1);
        }
        return $where_string;
    }

    private function sqlOrderBy($orderby_array) {

        $order_string = "";
        if(!empty($orderby_array)){
            foreach($orderby_array as $order_by){
                $order_string .= " '". $order_by['field']. "' " . $order_by['direction'] .",";
            }
            $order_string = "ORDER BY".substr($order_string, 0, -1);
        }
        return $order_string;
    }

    public function execute() {
        $table = $this->_table;
        $select_string = self::sqlSelect($this->_fields);
        $where_string = self::sqlWhere($this->_findCriteria);
        asort($this->_sortRules); // sort our order by statements by the precedence
        $order_string = self::sqlOrderBy($this->_sortRules);


        $query = $this->db->prepare($select_string . ' FROM ' . $table . " " . $where_string ." ". $order_string);
        try {
            if (!$query) {
                return new FMPDO_Error($this->db->errorInfo());
            }
            $result =  $query->execute();
        } catch (Exception $e) {
            return new FMPDO_Error($e);
        }
        return new FMPDO_Result($query->fetchAll());
    }

}
