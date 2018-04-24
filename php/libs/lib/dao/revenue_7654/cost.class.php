<?php
/**
 * 成本
 */
namespace Dao\Revenue_7654;
use \Dao;
class Cost extends Revenue_7654 {
    protected static $_instance = null;
    /**
     * @return Cost
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * 获取单日成本信息
     * @param $ymd
     * @return array
     */
    public function get_info($ymd){
        $sql = "select * from {$this->_realTableName} where ymd = {$ymd}";
        $ret = $this->query($sql);
        return $ret[0]?$ret[0]:[];
    }


    /**
     * 拉取的总条数和总成本
     * @param $s_date
     * @param $e_date
     * @return array
     */
    public function get_count_and_cost($s_date,$e_date){
        $where  = sprintf("ymd >= %s and ymd <= %s",$s_date,$e_date);
        $sql = "select count(1) as num,sum(total_cost) as total_cost  from {$this->_realTableName} where {$where} ";
        $ret = $this->query($sql);
        return $ret[0] ? $ret[0]:[];
    }

    /**
     * @param $s_date
     * @param $e_date
     * @param $offset
     * @param $page_size
     * @return mixed
     */
    public function get_list($s_date,$e_date,$offset,$page_size){
        $where  = sprintf("ymd >= %s and ymd <= %s",$s_date,$e_date);
        $sql = "select * from {$this->_realTableName} where {$where} order BY ymd desc limit {$offset},{$page_size}";
        return $this->query($sql);
    }

    /**
     *  整体收益 left jion income 表
     * @param $s_date
     * @param $e_date
     * @param $offset
     * @param $page_size
     * @return mixed
     */
    public function get_revenue_list($s_date,$e_date,$offset,$page_size){
        $where  = sprintf("a.ymd >= %s and a.ymd <= %s",$s_date,$e_date);
        $sql = "select a.ymd as ymd,if(ISNULL(b.total_income),0,b.total_income) as total_income,if(ISNULL(a.total_cost),0,a.total_cost)  as total_cost from {$this->_realTableName} a  LEFT  JOIN  income b on b.ymd=a.ymd where {$where}  order BY ymd desc limit {$offset},{$page_size}";
        return $this->query($sql);
    }

    /**
     * 拿到统计条数和统计信息
     * @param $s_date
     * @param $e_date
     * @param $offset
     * @param $page_size
     * @return mixed
     */
    public function get_revenue_count($s_date,$e_date){
        $where  = sprintf("a.ymd >= %s and a.ymd <= %s",$s_date,$e_date);
        $sql = "select count(1) as num ,sum(b.total_income) as total_income ,sum(a.total_cost) as total_cost  from {$this->_realTableName} a LEFT  JOIN  income b on b.ymd=a.ymd where {$where}";
        $ret = $this->query($sql);
        return $ret[0] ? $ret[0]:[];
    }

    /**
     * 更新线下成本
     * @param $ymd
     * @param $offline_incomde
     * @return int|mixed
     */
    public function add_offline_pay($ymd,$offline_pay){
        $sql = "INSERT INTO {$this->_realTableName} (ymd,total_cost,offline_pay) VALUES ({$ymd},{$offline_pay},{$offline_pay}) ON DUPLICATE KEY UPDATE offline_pay = {$offline_pay},
                  total_cost = salary+equally+expense+sem+sms+offline_pay+online_pay+other_pay";
        return  $this->exec($sql);
    }

    /**
     * 更新工资社保
     * @param $ym
     * @param $salary
     * @return int
     */
    public function update_salary($ym,$salary){
        $salary = number_format($salary / intval(date('t', strtotime((string)$ym))), 2, '.', '');
        $s_date = (string)$ym . "01";
        $e_date = (string)$ym . "31";
        $sql = "update {$this->_realTableName} set salary = {$salary},total_cost = salary+equally+expense+sem+sms+offline_pay+online_pay+other_pay
                        where ymd>={$s_date} and ymd <={$e_date}";
        return  $this->exec($sql);
    }

    /**
     * 更新报销
     * @param $ym
     * @param $equally
     * @return int
     */
    public function update_equally($ym,$equally){
        $equally = number_format($equally / intval(date('t', strtotime((string)$ym))), 2, '.', '');
        $s_date = (string)$ym."01";
        $e_date = (string)$ym . "31";
        $sql = "update {$this->_realTableName} set equally = {$equally},total_cost = salary+equally+expense+sem+sms+offline_pay+online_pay+other_pay
                  where ymd>={$s_date} and ymd <={$e_date}";
        return $this->exec($sql);
    }

    /**
     * @param $ym
     * @param $expense
     * @return int
     */
    public function update_expense($ym,$expense){
        $expense = number_format($expense / intval(date('t', strtotime((string)$ym))), 2, '.', '');
        $s_date = (string)$ym . "01";
        $e_date = (string)$ym . "31";
        $sql = "update {$this->_realTableName} set expense = {$expense},total_cost = salary+equally+expense+sem+sms+offline_pay+online_pay+other_pay
                    where ymd>={$s_date} and ymd <={$e_date}";
        return  $this->exec($sql);
    }

    /**
     * 更新sem成本
     * @param $ymd
     * @param $sem
     * @return int
     */
    public function update_sem($ymd,$sem){
        $sql = "update {$this->_realTableName} set sem = {$sem},total_cost = salary+equally+expense+sem+sms+offline_pay+online_pay+other_pay where ymd={$ymd} limit 1";
        return $this->exec($sql);
    }

    /**
     * 更新短信成本
     * @param $ymd
     * @param $sms
     * @return int
     */
    public function update_sms($ymd,$sms){
        $sql = "update {$this->_realTableName} set sms = {$sms},total_cost = salary+equally+expense+sem+sms+offline_pay+online_pay+other_pay where ymd={$ymd} limit 1";
        return $this->exec($sql);
    }

    /**
     * 更新线上成本
     * @param $ymd
     * @param $online_pay
     * @return int
     */
    public function update_online_pay($ymd,$online_pay){
        $sql = "update {$this->_realTableName} set online_pay = {$online_pay},total_cost = salary+equally+expense+sem+sms+offline_pay+online_pay+other_pay where ymd={$ymd} limit 1";
        return  $this->exec($sql);
    }

    /**
     * 程序其他的成本
     * @param $ymd
     * @param $other_pay
     * @return int
     */
    public function update_other_pay($ymd,$other_pay,$other_desc){
        $sql = "update {$this->_realTableName} set other_pay = {$other_pay},other_desc = '{$other_desc}',
          total_cost = salary+equally+expense+sem+sms+offline_pay+online_pay+other_pay where ymd={$ymd} limit 1";
        return $this->exec($sql);
    }

    /**
     * 月初建表专用
     * @param $ymd
     * @param $salary
     * @param $equally
     * @param $expense
     */
    public function init_cost($ymd,$salary,$equally,$expense){
        $total_cost = $salary+$equally+$expense;
        $sql = "INSERT INTO {$this->_realTableName} (ymd,salary,equally,expense,total_cost) VALUES ({$ymd},{$salary},{$equally},{$expense},{$total_cost}) ON DUPLICATE KEY UPDATE
                      salary ={$salary},equally = {$equally},expense = {$expense}, total_cost = salary+equally+expense+sem+sms+offline_pay+online_pay+other_pay";
        return  $this->exec($sql);
    }
}
