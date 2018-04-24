<?php
namespace Dao\Huayangnianhua_admin\Gs;
use \Dao\Huayangnianhua_admin\Huayangnianhua_admin;

class User_channel_data extends  Huayangnianhua_admin{

    protected static $_instance = null;

    /**
     * @return User_channel_data
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    public function qid_list($where,$limit){
        if(!$where){
            return false;
        }
        $sql = "SELECT id,qid FROM {$this->_get_table_name()} WHERE ";
        $sql .= $where." GROUP BY qid ORDER BY ymd DESC";
        $sql .=" LIMIT ".$limit;
        $qid_list = $this->query($sql);
        return $qid_list;
    }

}
