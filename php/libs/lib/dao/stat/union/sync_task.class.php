<?php
/**
 * 7654统计后台同步数据
 */
namespace Dao\Stat\Union;
use \Dao\Stat\Stat;
use \Dao\Orm;

class Sync_task extends Stat {

    /**
     * @return Sync_task
     */
    public static function get_instance(){ return parent::get_instance(); }
}