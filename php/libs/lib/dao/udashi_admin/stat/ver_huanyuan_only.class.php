<?php
namespace Dao\Udashi_admin\Stat;
use \Dao;
class Ver_huanyuan_only extends \Dao\Udashi_admin\Udashi_admin {

    protected static $_instance = null;
    /**
     * @return Ver_huanyuan_only

     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * 获取信息 还原的 数量
     * @return array
     */
    public function get_ver_huanyuan_count($ymd){
       // $sql = "select ver,$ymd Ymd , count(*) from stat_ver_kuaiya_only GROUP BY ver";
       // return $this->query($sql);
    }
    public function get_ver_all_huanyuan($ymd){
        $sql = "select ver,$ymd as Ymd , count(*) as huanyuanliang from `{$this->_realTableName}` where Ymd={$ymd} GROUP BY ver";
        return $this->query($sql);
    }

}
