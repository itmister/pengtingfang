<?php
namespace Dao\Huayangnianhua_admin\Stat;
class Mininews2_time_qid_data extends \Dao\Huayangnianhua_admin\Huayangnianhua_admin {

    protected static $_instance = null;
    /**
     * @return Mininews2_time_qid_data
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}
