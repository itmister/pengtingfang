<?php
namespace Dao;
/**
 * ��Dao��������crud����
 * Class Crud
 * @package Dao
 */
trait Crud {

    /**
     * ȡ�����
     */
    public function get_last_sort_idx( $where ) {
        return $this->total( $where ) + 1;
    }

    /**
     * @param $where
     */
    public function search( $where ) {
        $where = $this->_parse_where( $where );
        $sql = "
        select
            *
        from
          {$this->_get_table_name()}
        {$where}
        ";
        $result = $this->query( $sql );
        return $result;
    }
}