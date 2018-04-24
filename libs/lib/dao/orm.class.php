<?php
namespace Dao;
/**
 * 对Dao对象增加ORM特性
 * Class Crud
 * @package Dao
 */
trait Orm {

    protected $_fields = '*';
    protected $_where = '';
    protected $_group_by = '';
    protected $_order_by = '';
    protected $_having = '';
    protected $_limit = '';

    /**
     * @param string $fields
     * @return Orm
     */
    public function fields( $fields = '*' ) {
        $this->_fields = $fields;
        return $this;
    }

    public function where( $where ) {
        $this->_where = $this->_parse_where( $where );
        return $this;
    }

    public function order_by( $order ) {
        $this->_order_by = " ORDER BY {$order} ";
        return $this;
    }

    public function group_by( $group_by ) {
        $this->_group_by = " GROUP BY {$group_by} ";
        return $this;
    }

    public function limit( $start, $num = null ) {
        if (!isset($num)) {
            $this->_limit = " LIMIT " . intval($start) . " ";
        }
        else {
            $start = intval( $start);
            $num = intval ($num);
            $this->_limit = " LIMIT {$start},{$num} ";
        }
        return $this;
    }

    /**
     * @return array
     */
    public function find( $params = null ) {
        $sql = <<<eot
            select
                {$this->_fields}
            from
                {$this->_get_table_name()}

            {$this->_where}
            {$this->_group_by}
             {$this->_order_by}
            {$this->_limit}
eot;
        $this->_reset();
        return $this->query($sql);
    }

    /**
     * 取记录的某一个字段
     * @param $field
     */
    public function value( $field ) {
        if (empty($this->_limit)) $this->limit(1);
        $data = $this->find();
       return (!empty($data) && isset($data[0][$field])) ? $data[0][$field] : '';
    }

    /**
     * 取排序号
     */
    public function get_last_sort_idx( $where ) {
        return $this->total( $where ) + 1;
    }

    private function _reset() {
        $this->_fields = "*";
        $this->_where = '';
        $this->_group_by = '';
        $this->_order_by = '';
        $this->_limit = '';
    }
}