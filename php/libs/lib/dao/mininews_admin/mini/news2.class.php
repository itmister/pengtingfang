<?php
namespace Dao\Mininews_admin\Mini;
use \Dao\Mininews_admin\Mininews_admin;

class News2 extends Mininews_admin{

    protected static $_instance = null;
    /**
     * @return News2
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    public function get_news_by_cid(){
        /* $sql = "
            SELECT * FROM (
                SELECT * FROM {$this->_get_table_name()} WHERE pos = 2 AND title <> '' ORDER BY id DESC
            ) t GROUP BY cid LIMIT 12 
        "; 
         $news_data = [];
         $cid_list = [1,2];
         foreach ($cid_list as $cid){
            $sql = "SELECT * FROM {$this->_get_table_name()} WHERE pos = 2 AND title <> '' AND cid = {$cid} ORDER BY id DESC LIMIT 6";
            $news = $this->query($sql);
            $news_data = array_merge_recursive($news_data,$news);
         }*/
        
        $sql = "SELECT * FROM {$this->_get_table_name()} WHERE pos = 2 AND title <> '' AND cid = 1 ORDER BY id DESC LIMIT 12";
        
        return $this->query($sql);
    }
}
