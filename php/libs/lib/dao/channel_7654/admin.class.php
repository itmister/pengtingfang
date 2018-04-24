<?php
namespace Dao\Channel_7654;
use \Dao;
class Admin extends Channel_7654 {

    protected static $_instance = null;

    /**
     * @return Dao\Channel_7654\Admin
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * 取渠道主管列表
     * @return array
     */
    public function get_channel_master_list() {
        $sql = "select userid as uid, username as user_name from admin where roleid=7";
        $arr_data = $this->query($sql);
        return $arr_data;
    }

}
