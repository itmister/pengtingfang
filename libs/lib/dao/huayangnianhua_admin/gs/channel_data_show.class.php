<?php
namespace Dao\Huayangnianhua_admin\Gs;
use \Dao\Huayangnianhua_admin\Huayangnianhua_admin;

class Channel_data_show extends Huayangnianhua_admin {
    protected static $_instance = null;

    /**
     * @return Channel_data_show
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

	public function lists($qid = "",$limit = ""){
        $sql = "SELECT c.qid,d.show_price,d.show_amount,d.dateline FROM stat_channel_data AS c LEFT JOIN gs_channel_data_show AS d ON c.qid = d.qid";
        if($qid){
            $sql .= " WHERE c.qid = '{$qid}'";
        }else{
            $sql .= " WHERE c.qid <> ''";
        }
        $sql .=" GROUP BY c.qid";
        if($limit){
            $sql .= " LIMIT {$limit}";
        }
        $query_result = $this->query($sql);
        return $query_result;
	}
    
}