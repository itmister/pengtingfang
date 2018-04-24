<?php
namespace Dao\Revenue_7654;
use Dao\Dao;

class Revenue_7654 extends Dao {
    protected $_connection_key = 'DB_REVENUE_7654';
    protected $_prefix = '';

    /**
     * 取年月日，为解决不同的表存在8位与6位年月的问题
     * @param $table_name
     * @param $ymd
     * @return integer
     */
    protected function _get_table_ymd( $table_name, $ymd ) {
        $ymd = intval($ymd);
        $bit_6_table = array('log_register', 'log_manager_performance');
        if (in_array($table_name, $bit_6_table)) {
            return $ymd > 20000000 ? $ymd - 20000000 : $ymd;
        }
        return $ymd < 20000000 ? $ymd + 20000000 : $ymd;
    }
}