<?php
namespace Revenue_7654;

use Dao\Revenue_7654\Income;
use \Dao\Revenue_7654\Month_Equally;
use \Dao\Revenue_7654\Cost;
use \Dao\Revenue_7654\Offline_Income;
use \Dao\Revenue_7654\Offline_Income_Info;
use \Dao\Revenue_7654\Vendor;
class Revenue {
    protected static $_instance = null;

    /**
     * @return \Revenue_7654\Revenue
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 设置月度工资
     * @param $ym
     * @param $salary
     * @return bool
     */
    public function set_salary($ym,$salary){
        Month_Equally::get_instance()->begin_transaction();
        $ret1 = Month_Equally::get_instance()->set_salary($ym,$salary);
        $ret2  = Cost::get_instance()->update_salary($ym,$salary);
        if($ret1 === false || $ret2 === false){
            Month_Equally::get_instance()->rollback();
            return false;
        }else{
            Month_Equally::get_instance()->commit();
            return true;
        }
    }

    /**
     * 设置月底公摊
     * @param $ym
     * @param $equally
     * @return bool
     */
    public function set_equally($ym,$equally){
        Month_Equally::get_instance()->begin_transaction();
        $ret1 = Month_Equally::get_instance()->set_equally($ym,$equally);
        $ret2  = Cost::get_instance()->update_equally($ym,$equally);
        if($ret1 === false || $ret2 === false){
            Month_Equally::get_instance()->rollback();
            return false;
        }else{
            Month_Equally::get_instance()->commit();
            return true;
        }
    }

    /**
     * 设置月度报销
     * @param $ym
     * @param $expense
     * @return bool
     */
    public function set_expense($ym,$expense){
        Month_Equally::get_instance()->begin_transaction();
        $ret1 = Month_Equally::get_instance()->set_expense($ym,$expense);
        $ret2  = Cost::get_instance()->update_expense($ym,$expense);
        if($ret1 === false || $ret2 === false){
            Month_Equally::get_instance()->rollback();
            return false;
        }else{
            Month_Equally::get_instance()->commit();
            return true;
        }
    }

    /**
     * 线下产品数据展示
     * @param $s_date
     * @param $e_date
     * @param $offset
     * @param $page_size
     * @return array
     */
    public function offline_income($s_date,$e_date,$offset,$page_size){
        $ret = Offline_Income::get_instance()->get_list($s_date,$e_date,$offset,$page_size);
        $count = count($ret);
        if($count == 1){
            $e_date_1 = $ret['0']['ymd'];
            $s_date_1 = $ret['0']['ymd'];
        }elseif ($count > 1){
            $e_date_1 = $ret['0']['ymd'];
            $s_date_1 = $ret[$count - 1]['ymd'];
        }
        $list  = Offline_Income_Info::get_instance()->get_list($s_date,$e_date_1);
        $ymd_soft = [];
        $soft_arr  = Vendor::get_instance()->get_softs();
        $soft_ids = [];
        foreach($soft_arr as $val){
            $soft_ids[] = $val['soft_id'];
        }
        foreach ($list as $val){
            if (in_array($val['soft_id'],$soft_ids)){  //不显示当前暂停合作的
                $ymd_soft[$val['ymd']][$val['soft_id']] = ['org_num'=>$val['org_num'],'price'=>$val['price']];
            }
        }
        $max_soft_counts = 0;//单日统计到的软件最多的数作为title
        $soft_title = [];
        foreach ($ret as &$v) {
            if (!$max_soft_counts) {
                $max_soft_counts = count($ymd_soft[$v['ymd']]);
                $soft_title = array_keys($ymd_soft[$v['ymd']]);
            }else{
                if (count($ymd_soft[$v['ymd']]) >  $max_soft_counts){
                    $max_soft_counts = count($ymd_soft[$v['ymd']]);
                    $soft_title = array_keys($ymd_soft[$v['ymd']]);
                }
            }
            $v = array_merge($v,$ymd_soft[$v['ymd']]);
        }
        return ['data'=>$ret,'title'=>$soft_title];
    }

    /**
     * 更新线下软件的单日总收益
     * @param $ymd
     * @param $softs
     */
    public function update_offline_income($ymd,$softs){
        $num = Offline_Income_Info::get_instance()->get_sum($ymd,$softs);
        Offline_Income::get_instance()->begin_transaction();
        $ret1 = Offline_Income::get_instance()->add(['ymd'=>$ymd,'total_income'=>$num],true);
        $ret2 = Income::get_instance()->add_offline_income($ymd,$num);
        if ($ret1 === false || $ret2 === false){
            Offline_Income::get_instance()->rollback();
            return false;
        }else{
            Offline_Income::get_instance()->commit();
        }
        return true;
    }
}