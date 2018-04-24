<?php
/**
 * 签到引导
 */
namespace Dao\Union;
use \Dao;
class Sign_Guide extends Union {

    protected static $_instance = null;

    /**
     * @return Act_credit
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_info($uid,$type) {
        return  $this->get_one('uid', "uid={$uid} and type = {$type}");
    }

    public function add_user($uid,$type){
        $data =  ['uid'=>$uid,'type'=>$type];
        return $this->add($data);
    }

}
