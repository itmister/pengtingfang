<?php
/**
 * 软件模型
 * @author huxiaowei1238
 *
 */
namespace Union\WebSite;
class Promote{
    /**
     * 软件表模型层
     */
    protected $promoteModel;
    
    public function __construct(){
        $this->promoteModel  = \Dao\Union\Promotion::get_instance();
    }
    
    /**
     * 获取产品列表
     */
    public function get_list($params){
        $list = $this->promoteModel->select($params);
        if(empty($list)){
            return false;
        }
        $promote_array = array();
        foreach($list as $value) {
            $group = $value['type'];
            $promote_array[$group][] = $value;
        }
        return $promote_array;
    }
}