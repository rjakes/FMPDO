<?php
/* FMPDO Library.
 *
 * @package FMPDO
    *
 * Copyright � 2013, Roger Jacques Consulting
 * See enclosed MIT license

 */

require_once(__DIR__ . '/FmpdoError.php');

class FmpdoCommandFind {

    var $_table;
    var $_fields = array();
    var $_findCriteria = array();
    var $_sortRules = array();

    /**
     * Find command constructor.
     * Assign variable and check parameter 
     */
    public function FmpdoCommandFind($table) {

        $this->_table = $table;
    }

    public function selectFields($field_array) {
        if (!isset($field_array)) {
            return new FmpdoError("Missing parameter to FmpdoCommandFind->setFields", "-1");
        }
        $this->_fields = $field_array;
    }

    function addFindCriterion($field, $value) {
        $this->_findCriteria[$field]['value'] = $value;
    }

    function addSortRule($field, $precedence, $direction='ascend') {
        $sqlOrder = strtolower($direction) == "descend" ? "DESC" : "ASC";
        $this->_sortRules[$precedence]['field']= $field;
        $this->_sortRules[$precedence]['direction']= $sqlOrder;
    }


    /**

     */


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

        $db = FmpdoDb::getConnection();


        $query = $db->prepare($select_string . ' FROM ' . $table . " " . $where_string ." ". $order_string);
        try {
            if (!$query) {
                return new FmpdoError($db->errorInfo());
            }
            $result =  $query->execute();
        } catch (Exception $e) {
            return new FmpdoError($e);
        }
        return new FmpdoResult($query->fetchAll());
    }

}
