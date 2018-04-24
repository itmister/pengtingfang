<?php
namespace Activity\Award;
/**
 * 积分奖励
 * Class Credit
 * @package Activity\Award
 */
class Credit extends AwardBase {

    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }

    public function build_award(\Activity\Engine $engine){
        $rule_info = $engine->get_rule_info();
        $uid  = $engine->get_uid();
        $this->award_info = ['credit'=>20];
        return true;
    }

    public function send(\Activity\Engine $engine) {
        $uid = $engine->get_uid();
        return true;
    }
}