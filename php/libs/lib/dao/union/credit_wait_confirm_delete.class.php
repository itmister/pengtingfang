<?php
/**
 * 删除积分
 */
namespace Dao\Union;
use \Dao;
class Credit_wait_confirm_delete extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Credit_wait_confirm_delete
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}
