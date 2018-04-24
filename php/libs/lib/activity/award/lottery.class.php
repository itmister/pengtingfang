<?php
namespace Activity\Award;
/**
 * 抽奖
 * @package Activity\Award
 */
class Lottery extends AwardBase {
    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }

    /**
     * 抽奖类活动生成奖励
     * @param \Activity\Engine $engine
     * @return bool
     */
    public function build_award(\Activity\Engine $engine){
        $rule_info = $engine->get_rule_info();
        $uid = $engine->get_uid();
        $package_info  = \Lottery\lottery::get_instance()->lotteryDraw($rule_info['rid'],$uid);
        if (!$package_info){
            $this->setErrorInfo(\Lottery\lottery::get_instance()->getErrorInfo());
            return false;
        }
        // 检查库存
        if($package_info['quantity']<1){
            //没有库存了
        	$this->setErrorInfo(array('errno'=>'-3', 'error'=>'奖励 '.$package_info['pid'].' 没有库存！'));
        	return false;
        }
        $this->award_info = $package_info;
        return true;
    }

    /**
     * 抽奖类活动奖励发放
     * @param \Activity\Engine $engine
     * @return int|string
     */
    public function send(\Activity\Engine $engine) {
        $user_info = $engine->get_user_info();
        $rule_info  = $engine->get_rule_info();
        $rid = $rule_info['rid'];

        // 扣库存
        $where = "pid = ".$this->award_info['pid'];
        $arr_data['quantity'] = $this->award_info['quantity'] - 1;
        if(!\Dao\Union\Act_Mod_Lottery_Package::get_instance()->update($where,$arr_data)){
        	return false;
        }
        
        return \Dao\Union\Act_Mod_User_Award::get_instance()->add([
            'seq'=>"JL".time().mt_rand(1, 1000),
            'uid'=>$engine->get_uid(),
            'rid'=>$rid,
            'pid'=>$this->award_info['pid'],
            'username'=>$user_info['name'],
            'pname'=>$this->award_info['pname'],
            'ctime' => date('Y-m-d H:i:s')
        ]);
    }
}