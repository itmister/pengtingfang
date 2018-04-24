<?php
namespace Dao\Wplayer_admin\Stat;
use \Dao;
class Ver_data_detail extends \Dao\Wplayer_admin\Wplayer_admin {

    protected static $_instance = null;
    /**
     * @return Ver_data_detail
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function add_ver_detail($ymd,$version,$sd360,$qqgj,$jsdb){
        $sql = "INSERT INTO stat_ver_data_detail SET ymd='$ymd',ver='$version',360sd='$sd360',qqgj='$qqgj',jsdb='$jsdb'";
        $this->query($sql);
    }

}
