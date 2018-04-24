<?php
/**
 * 存储过程
 */
namespace Dao\Channel_7654;
use \Dao;
class Procedure extends Channel_7654 {

    protected static $_instance = null;

    /**
     * @return Dao\Channel_7654\Procedure
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * 执行取数据存储过程
     * @param $str
     * @return array
     */
    public function query_call( $str ){
        $arr_data   = $this->query( $str );
        $total      = array_pop( array_pop( $arr_data ) );//第一行第一列存放总量
        return array(
            'total' => $total,
            'list' => $arr_data,
        );
    }

    public function query_multi( $sql ) {
        $arr_data = $this->db()->query_multi( $sql );
        return $arr_data;
    }
}
