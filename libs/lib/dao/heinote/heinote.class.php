<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/2
 * Time: 15:29
 */
namespace Dao\Heinote;
use Dao\Dao;

class Heinote extends Dao {
    protected $_connection_key = 'DB_HEINOTE';
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