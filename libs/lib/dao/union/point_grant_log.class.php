<?php
namespace Dao\Union;
use Dao;

/**
 *  抓推广量异常的用户 暴涨  稳定
 * Class Point_grant_log
 * @package Dao\Union\Point_grant_log
 */
class Point_grant_log extends Union {
    protected static $_instance = null;

    /**
     * @return Dao\Union\Point_grant_log
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}