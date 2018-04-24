<?php
/**
 * 市场经理下属软件安装统计模型
 */
namespace Dao\Stat;

class Manager_performance_detail extends Stat {

    protected static $_instance = null;

    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}
