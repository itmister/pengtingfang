<?php
/**
 * 7654征集优秀猎人发赏金：任务表
 */
namespace Dao\Channel_7654;
class Task extends Channel_7654 
{

    protected static $_instance = null;

    /**
     * @return Task
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
