<?php
namespace Activity\Limit;

/**
 * Class JoinInfo
 * @package Activity\Limit
 */
class JoinInfo extends LimitBase {
    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }

    public function check_limit(\Activity\Engine $engine) {
        $rule_info = $engine->get_rule_info();
        $uid = $engine->get_uid();
        $this->limit_info = \Dao\Union\Act_Mod_Join_Info::get_instance()->get_join_info($uid,$rule_info['rid']);
        return true;
    }

    public function update_limit(\Activity\Engine $engine){
        $user_info = $engine->get_user_info();
        $rule_info = $engine->get_rule_info();
        if (empty($this->limit_info)){
            $ret = \Dao\Union\Act_Mod_Join_Info::get_instance()->add_join_info([
                'uid'=>$user_info['id'],
                'rid'=>$rule_info['rid'],
                'aid'=>$rule_info['aid'],
                'username'=>$user_info['name'],
                'ctime' => date('Y-m-d H:i:s'),
                'join_times'=>1,
                'utime'=> date('Y-m-d H:i:s')
            ]);
            if($ret){
                return true;
            }else{
                $this->coverError("-204","add limit error");
                return false;
            }
        }else{
            $data = [
                'join_times'=>$this->limit_info['join_times'] + 1,
                'utime'=> date('Y-m-d H:i:s')
            ];
            $ret = \Dao\Union\Act_Mod_Join_Info::get_instance()->update_join_info($user_info['id'],$rule_info['rid'],$data);
            if($ret){
                return true;
            }else{
                $this->coverError("-204","update limit error");
                return false;
            }
        }
    }
}