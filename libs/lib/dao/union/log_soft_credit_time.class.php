<?php

namespace Dao\Union;
use \Dao;
class Log_soft_credit_time extends Union {
    protected static $_instance = null;
    /**
     * @return Log_soft_credit_time
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_ymd($softStr){
        $sql = "SELECT soft_id,start,`end` FROM `{$this->_realTableName}` WHERE soft_id in ({$softStr}) and status=1";
        return $this->query($sql);
    }

}
