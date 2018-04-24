<?php
namespace Dao\Union;
use Dao;

/**
 *  周年庆活动分享
 * Class Celebrate_share_writhe
 * @package Dao\Union
 */
class Celebrate_share_writhe extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Celebrate_share_writhe
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

}