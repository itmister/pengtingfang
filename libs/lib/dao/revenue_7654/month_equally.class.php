<?php
/**
 * 月度均摊
 */
namespace Dao\Revenue_7654;
use \Dao;
class Month_Equally extends Revenue_7654 {
    protected static $_instance = null;
    /**
     * @return Month_Equally
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_info($ym){
        $sql = "select * from {$this->_realTableName} where ym = {$ym} limit 1";
        $ret = $this->query($sql);
        return $ret[0] ? $ret[0] : [];
    }

    /**
     * 设定的月度工资
     * @param $ym
     * @param $salary
     * @return int|mixed
     */
    public function set_salary($ym,$salary){
        $avg_salary = number_format($salary / intval(date('t', strtotime((string)$ym))), 2, '.', '');
        $sql = "INSERT INTO {$this->_realTableName} (ym,salary,avg_salary) VALUES ({$ym},{$salary},{$avg_salary}) ON DUPLICATE KEY UPDATE salary = {$salary},
                  avg_salary = {$avg_salary}";
        return  $this->exec($sql);
    }

    /**
     * 设定月度公摊
     * @param $ym
     * @param $equally
     * @return int|mixed
     */
    public function set_equally($ym,$equally){
        $avg_equally = number_format($equally / intval(date('t', strtotime((string)$ym))), 2, '.', '');
        $sql = "INSERT INTO {$this->_realTableName} (ym,equally,avg_equally) VALUES ({$ym},{$equally},{$avg_equally}) ON DUPLICATE KEY UPDATE equally = {$equally},
                  avg_equally = {$avg_equally}";
        return  $this->exec($sql);
    }

    /**
     * 设定月度报销
     * @param $ym
     * @param $expense
     * @return int|mixed
     */
    public function set_expense($ym,$expense){
        $avg_expense = number_format($expense / intval(date('t', strtotime((string)$ym))), 2, '.', '');
        $sql = "INSERT INTO {$this->_realTableName} (ym,expense,avg_expense) VALUES ({$ym},{$expense},{$avg_expense}) ON DUPLICATE KEY UPDATE expense = {$expense},
                  avg_expense = {$avg_expense}";
        return $this->exec($sql);
    }
}
