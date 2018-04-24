<?php
/**
 * 统计-市场经理-月工资数据
 */
namespace Dao\Stat;
use \Dao\Orm;

class Report_salary extends Stat {
    use Orm;
    /**
     * @return Report_salary
     */
    public static function get_instance(){ return parent::get_instance(); }
}
