<?php
namespace Union\WebSite;

/**
 * 
 * Class message_question
 * @package Union\WebSite
 */

class Notice {
    
    /**
     * 公告表模型层
     * @var ContentModel
     */
    protected $contentModel;
    
    /**
     * 新闻表模型层
     * @var unknown
     */
    protected $newsModel;
    
    public  $types = [
        '1'=>'公告',   '2'=>'系统通知',
        '3'=>'活动通知','4'=>'常见问题',
        '5'=>'推广技巧','6'=>'礼品兑换',
        '7'=>'账号相关','8'=>'软件产品',
        '9'=>'行业研究'
    ];
    public function __construct(){
        $this->contentModel  = \Dao\Union\Content::get_instance();
        $this->newsModel     = \Dao\Union\News::get_instance();
    }
    
    /**
     * 获取公告列表
     * @param string $where         查询条件
     * @param string $field         查询字段
     * @param number $page_start    开始记录数
     * @param number $page_end      结束记录数
     * @param string $orderby       排序方式
     * @return unknown
     */
    public function get_list($where ="1=1",$field ="*",$page_start = 0,$page_end = 10,$orderby = "updatetime desc"){
        $cocntent_list = $this->contentModel->select($where,$field,$page_start,$page_end,$orderby);
        return $cocntent_list;
    }
    
    /**
     * 获取全部公告
     * @param string $where     查询条件
     * @param string $field     查询字段
     * @param string $orderby   排序方式
     * @return array
     */
    public function get_all($where = "1=1",$field ="*",$orderby = "updatetime desc"){
        $cocntent_list = $this->contentModel->select($where,$field,0,0,$orderby);
        return $cocntent_list;
    }

    /**
     * 获取总记录数
     * @param string $where 查询条件
     * @return integer
     */
    public function get_count($where = "1=1"){
        $count = $this->contentModel->count($where);
        return $count['tp_count'];
    }
    
    /**
     * 公告
     * @param integert $catid   公告类型
     * @param integert $num     数量
     * @param integert $typeid  栏目类型
     * @param string   $style   样式
     * @param string   $orderby 排序
     * @return unknown
     */
    public function top($catid='',$num=10,$typeid="",$style="",$orderby = "updatetime desc"){
        //联盟公告按照更新时间排序
        if($catid == 1){
            $orderby = "inputtime DESC";
        }
        $list = $this->contentModel->fetch_content($catid,$num,$typeid,$style,$orderby);
        return $list;
    }
    
    /**
     * @param string $catid
     * @param int $num
     * @return mixed
     */
    public function top_style($catid='',$num=10,$styleid=1){
        if($catid) {
            $where = "status=99 AND catid=$catid";
        } else {
            $where = "status=99 ";
        }
        if($styleid) {
            $where .= " AND style = $styleid";
        }
        $lists = M('content')->where($where)->order("listorder asc,inputtime desc")->limit(0,$num)->select();
        return $lists;
    }
    
    /**
     * 公告详情
     * @param integer $id
     * @return array
     */
    public function detail($id){
        if(!$id) return false;
        
        $content_info = $this->contentModel->find($id);
        return $content_info;
    }
    
    /**
     * 更新点击量
     * @param integer $id
     * @param integer $hit
     * @return \Dao\mixed
     */
    public function update_hits($id,$hit = 1){
        if(!$id) return false;
        $result = $this->contentModel->update_hits($id,$hit);
        return $result;
    }
    
    /**
     * 新闻
     * @param string $where         查询条件
     * @param string $field         查询字段
     * @param number $page_start    开始记录数 
     * @param number $page_end      结束记录数
     * @param string $orderby       排序
     * @return array
     */
    public function news($where = "1=1",$field ="*",$page_start = 0,$page_end = 10,$orderby = "datetime desc"){
        $new_list = $this->newsModel->lists($where,$field,$page_start,$page_end,$orderby);
        
        if(!$new_list){
            return false;
        }
        foreach ($new_list as $key => $new) {
            $new_list[$key]['datetime'] = date('m-d', $new['datetime']);
            $new_list[$key]['newslist'] = 0;
            if($new_list[$key]['datetime'] != date('m-d')){
                $new_list[$key]['newslist'] = 1;
            }
        }
        return $new_list;
    }

    /**
     * 各类别数据
     */
    public function class_list($catid=''){
        if($catid) {
            $where = "status=99 AND catid=$catid";
        } else {
            $where = "status=99 ";
        }
        $lists = M('content')->where($where)->order("listorder asc,inputtime desc")->field('id,title,inputtime')->select();
        return $lists;
    }

    public function top_pv($catid='',$page=1,$num=10){
        if($catid) {
            $where = "status=99 AND catid=$catid";
        } else {
            $where = "status=99 ";
        }
        $page_start = $page==1?0:($page - 1)*$num;
        $lists = M('content')->where($where)->order("hits desc")->limit($page_start,$num)->field('id,title,inputtime')->select();
        return $lists;
    }
}
