<?php
namespace Dao\Wplayer_admin\Stat;
class Sub_channel_list extends \Dao\Wplayer_admin\Wplayer_admin {

    protected static $_instance = null;
    /**
     * @return Sub_channel_list
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_nav_tn(){
        $sql = "SELECT channel,GROUP_CONCAT(DISTINCT nav_tn) as nav_tn FROM wh_sub_channel_list GROUP BY channel;";
        $list = $this->query($sql);
        $data = array();
        if(empty($list)) return false;
        foreach($list as $v){
            $data[$v['channel']] = $v['nav_tn'];
        }
        return $data;
    }
}
