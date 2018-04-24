<?php
/**
 * 7654征集优秀猎人发赏金：用户日志
 */
namespace Dao\Channel_7654;
class Task_user_log extends Channel_7654 
{

    protected static $_instance = null;

    /**
     * @return Task_user_log
     */
    public static function get_instance()
    {
        if (empty(self::$_instance)) 
        {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}
