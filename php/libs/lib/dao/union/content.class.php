<?php
namespace Dao\Union;
use \Dao;

class Content extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Content
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    /**
     * 公告列表
     * @param string $field
     * @param integer $page_start
     * @param integer $page_end
     * @param string $orderby
     * @return \Dao\mixed
     */
    public function select($where,$field,$page_start,$page_end,$orderby){
        $query_sql      = "SELECT {$field} FROM {$this->_get_table_name()} WHERE {$where}";
        if($orderby){
            $query_sql.=" ORDER BY {$orderby}";
        }
        if(is_int($page_start) && $page_end > 0){
            $query_sql.=" LIMIT {$page_start},$page_end";
        }
        $query_result   = $this->query($query_sql);
        return $query_result;
    }
    
    
    /**
     * 获取总记录数
     * @param string $where
     * @return \Dao\mixed
     */
    public function count($where){
        $query_sql      = "SELECT COUNT(*) AS tp_count FROM {$this->_get_table_name()} WHERE {$where}";
        $query_result   = $this->query($query_sql);
        return current($query_result);
    }
    
    /**
    * 获取公告列表
    * @param integert $catid  公告类型
    * @param integert $num    数量
    * @param integert $typeid 栏目类型
    * @param string   $orderby 排序
    * @return \Dao\mixed
    */
    public function fetch_content($catid,$num,$typeid,$style,$orderby){
        
        $where = "`status`=99";
        if(!empty($catid) && is_array($catid)){
            $catid = implode(',', $catid);
            $where = " AND `catid` IN({$catid})";
        }else{
            $where .=" AND `catid` = ".$catid;
        }
        if($typeid) {
            $where .= " AND `typeid` = ".intval($typeid);
        }
        if($style) {
            $where .= " AND `style` = $style";
        }
        $query_sql    = "SELECT * FROM `{$this->_get_table_name()}` WHERE {$where} ORDER BY {$orderby} LIMIT 0,{$num}";
        $query_result = $this->query( $query_sql );
    
        return $query_result;
    }
    
    /**
     * 通过id返回公告
     * @param integer $id
     */
    public function find($id){
        $query_sql    = "SELECT * FROM `{$this->_get_table_name()}` WHERE `id` = ".$id;
        $query_result = $this->query( $query_sql );
        
        return current($query_result);
    }
    
    /**
     * 更新点击量
     * @param integer $id 
     * @param integer $hit
     * @return \Dao\mixed
     */
    public function update_hits($id,$hit){
        $query_sql    = "UPDATE `{$this->_get_table_name()}` SET `hits` = `hits`+{$hit} WHERE `id` = ".$id;
        $query_result = $this->query( $query_sql );
        return $query_result;
    }
}
