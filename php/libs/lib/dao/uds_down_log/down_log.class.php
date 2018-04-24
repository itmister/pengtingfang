<?php
namespace Dao\Uds_down_log;
use \Dao;
class Down_log extends Uds_down_log{

    /**
     * @return Dao\Uds_down_log\Down_log
     */
    public static function get_instance(){
        return parent::get_instance();
    }

}
