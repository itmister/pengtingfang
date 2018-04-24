<?php
namespace Dao\Udashi_admin\Stat;
use \Dao;
class Ver_kuaiya_only extends \Dao\Udashi_admin\Udashi_admin {

    protected static $_instance = null;
    /**
     * @return Ver_kuaiya_only

     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * 获取信息 快压的 数量
     * @return array
     */
    public function get_ver_kuaiya_count($ymd){
       // $sql = "select ver,$ymd Ymd , count(*) from stat_ver_kuaiya_only GROUP BY ver";
       // return $this->query($sql);
    }
    public function get_ver_all_kuaiya($ymd){
        $sql = "select ver,$ymd Ymd , count(*) kuaiyaliang from `{$this->_realTableName}` where Ymd={$ymd} GROUP BY ver";
        return $this->query($sql);
    }

}
