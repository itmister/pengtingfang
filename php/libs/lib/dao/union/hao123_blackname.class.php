<?php

namespace Dao\Union;
use \Dao;
class Hao123_blackname extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Hao123_blackname
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function add_black($data){
        return $this->add($data);
    }

    public function remove_blackname($uid,$short_name){
        if (!$uid || !$short_name) return false;
        $this->update("uid = {$uid} and name ='{$short_name}'",['delete_flag'=>1]);
    }

}
