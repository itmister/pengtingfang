<?php
namespace Dao\Wplayer_admin\Stat;
use \Dao\Wplayer_admin;
class nav_data extends \Dao\Wplayer_admin\Wplayer_admin {

    /**
     * @return nav_data
     */
    public static function get_instance(){
        return parent::get_instance();
    }

    /*取导航的tn*/
    public function get_tn_list_ymd($soft_id,$ymd){
        $sql = "select tn from `{$this->_realTableName}` where ymd={$ymd} and soft_id='{$soft_id}'";
        $list = $this->query($sql);
        $data = array();
        if($list){
            foreach($list as $val){
                $data[] = $val['tn'];
            }
        }
        return $data;
    }

    public function get_count_ymd($where){
        $sql = "select count(*) as num from `{$this->_realTableName}` where {$where}";
        return $this->query($sql);
    }

    public function select_data($where,$limit){
        $sql = "select * from `{$this->_realTableName}` where {$where} limit {$limit}";
        return $this->query($sql);
    }
}
