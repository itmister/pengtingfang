<?php
namespace Activity\Condition;

class Qglottery extends ConditionBase {

    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }

    public function check_condition(\Activity\Engine $engine) {
        $uid = $engine->get_uid();
        $rule_info = $engine->get_rule_info();
        $limit_info  = $engine->limit_model->get_limit_info();
        $ipcount = \Dao\Union\Credit_wait_confirm::get_instance()->get_user_soft_effective($uid,['qqpcmgr'],$rule_info['sdate'],$rule_info['edate']);
        $do_time = intval($ipcount);
        if ($do_time > $limit_info['join_times']){
            return true;
        }else{
            $this->coverError("-3","您还没有抽奖机会，赶紧去推广QQ管家吧！");
            return false;
        }
        return true;
    }
}