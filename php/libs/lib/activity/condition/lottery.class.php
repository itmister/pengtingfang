<?php
namespace Activity\Condition;

class Lottery extends ConditionBase {

    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }

    public function check_condition(\Activity\Engine $engine) {
        $uid = $engine->get_uid();
        $rule_info = $engine->get_rule_info();
        $limit_info  = $engine->limit_model->get_limit_info();
        $ipcount =8;/* \Dao\Union\Credit_wait_confirm::get_instance()->get_user_soft_effective($uid,
                            ['bdbrowserv2','bdsdv2','bdwsv2'],$rule_info['stime'],$rule_info['etime']);*/
        $do_time = intval($ipcount /3 );
        if ($do_time > $limit_info['join_times']){
            return true;
        }else{
            $this->coverError("-360","您暂时不满足抽奖资格，继续努力吧！");
            return false;
        }
        return true;
    }
}