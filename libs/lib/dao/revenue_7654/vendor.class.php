<?php
/**
 * 厂商信息
 */
namespace Dao\Revenue_7654;
use \Dao;
class Vendor extends Revenue_7654 {
    protected static $_instance = null;

    /**
     * @return  \Dao\Revenue_7654\Vendor
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     *  获取线下合作软件id
     * @return mixed
     */
    public function get_softs(){
        $sql = "select promotion_name,soft_id ,price from {$this->_realTableName} where cooperation_mode in(2,3) and cooperation_statu = 1";
        return $this->query($sql);
    }
}
