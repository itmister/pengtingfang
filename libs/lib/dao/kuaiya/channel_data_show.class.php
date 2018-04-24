<?php
namespace Dao\Kuaiya;
class Channel_data_show extends  Kuaiya{

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
        $sql = "SELECT c.main_qid,d.show_price,d.show_amount,d.dateline FROM channel AS c LEFT JOIN channel_data_show AS d ON c.main_qid = d.qid";
        if($qid){
            $sql .= " WHERE c.main_qid = '{$qid}'";
        }else{
            $sql .= " WHERE c.main_qid <> ''";
        }
        $sql .=" GROUP BY c.main_qid";
        if($limit){
            $sql .= " LIMIT {$limit}";
        }
        $query_result = $this->query($sql);
        return $query_result;
    }
}
