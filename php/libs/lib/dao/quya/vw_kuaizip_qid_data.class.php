<?php
namespace Dao\Quya;
class Vw_kuaizip_qid_data extends Quya {

    protected static $_instance = null;

    /**
     * @return Vw_kuaizip_qid_data
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}
