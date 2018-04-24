<?php
namespace Dao\Union;
use \Dao;
class Credit_Stat_Detail extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Credit_Stat_Detail
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function add_stat($data){
        return $this->add($data);
    }

}
