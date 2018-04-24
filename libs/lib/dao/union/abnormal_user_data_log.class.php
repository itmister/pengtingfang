<?php
namespace Dao\Union;
use Dao;

/**
 *  抓推广量异常的用户 暴涨  稳定
 * Class Abnormal_user_data
 * @package Dao\Abnormal_user_data_log
 */
class Abnormal_user_data_log extends Union {
    protected static $_instance = null;

    /**
     * @return Dao\Union\Abnormal_user_data_log
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}