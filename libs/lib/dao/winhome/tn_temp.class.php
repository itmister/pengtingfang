<?php
namespace Dao\Winhome;
use \Dao;
class Tn_temp extends Winhome{

    protected static $_instance = null;
    /**
     * @return Dao\Winhome\Tn_temp
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    #标记出那些使我们的tn
    public function tn_update_is_me(){
        $sql="UPDATE `{$this->_realTableName}` as t LEFT JOIN
        (SELECT soft_id,tn FROM wh_sub_channel_info GROUP BY tn,soft_id) as tp on t.soft_id=tp.soft_id and t.tn=tp.tn
        set t.is_me=1 where tp.tn is not NULL;";
        return $this->query($sql);
    }

    public function get_tn_data($ymd){
        $sql = "SELECT {$ymd} as ymd,a.soft_id as soft_id,a.tn as tn,to_num,to_ip_num,UNIX_TIMESTAMP() as dateline
                FROM wh_sub_channel_info as a LEFT JOIN `{$this->_realTableName}` as b on a.soft_id=b.soft_id and a.tn=b.tn GROUP BY a.soft_id,a.tn";
        return $this->query($sql);
    }

    public function get_other_dh_num($soft_id){
        $sql = "select sum(to_num) as to_num,sum(to_ip_num) as to_ip_num from `{$this->_realTableName}` where soft_id='{$soft_id}' and is_me=0";
        return $this->query($sql);
    }

}
