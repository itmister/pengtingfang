<?php
/**
 * 7654征集优秀猎人发赏金：市场经理任务表
 */
namespace Dao\Channel_7654;
class Manager_task extends Channel_7654 
{

    protected static $_instance = null;

    /**
     * @return Manager_task
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
