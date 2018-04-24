<?php
/**
 * Created by JetBrains PhpStorm.
 * User: caolei
 * Date: 15-5-23
 * Time: 下午4:00
 * To change this template use File | Settings | File Templates.
 * 7654官网改版个人中心=》我的帐户
 */
namespace Union\Grade;

class Base{

    protected static $_instance = null;

    /**
     * @return \Union\Grade\Base
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}
