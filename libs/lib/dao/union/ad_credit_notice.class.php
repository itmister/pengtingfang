<?php
/**
 * 业绩更新说明
 */
namespace Dao\Union;
use \Dao;
class Ad_credit_Notice extends Union{

    protected static $_instance = null;
    /**
     * @return \Dao\Union\Ad_credit_Notice
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     *  获取当天通过
     * @param $date
     * @return integer
     */
    public function get_notice($date){
        return $this->get_one('content', "date='{$date}'");
    }

    /**
     *  获取当天通过
     * @param $date
     * @return integer
     */
    public function get_notice_two($start,$end){
        $sql  = "select date,content from {$this->_realTableName} WHERE `date` >= '{$start}' and `date` <='{$end}' order by date desc";
        return $this->query($sql);
    }

    public function get_list($start_date,$end_date,$offset,$pre_page){
        $sql  = "select * from {$this->_realTableName} WHERE `date` >= '{$start_date}' and `date` <='{$end_date}' order by date desc limit $offset,$pre_page";
        return $this->query($sql);
    }

    public function get_count($start_date,$end_date){
        $sql  = "select count(1) as num from {$this->_realTableName} WHERE `date` >= '{$start_date}' and `date` <='{$end_date}'";
        $ret = $this->query($sql);
        return $ret['0']['num']?$ret['0']['num'] : 0;
    }


    /**
     * 添加公告
     * @param $date
     * @param $content
     * @return bool|int|\mysqli_result|string
     */
    public function add_notice($date, $content){
        $data =['date'=>$date,'content'=>$content];
        return $this->add($data);
    }

    /**
     * 修改公告
     * @param $date
     * @param $content
     * @return bool
     */
    public function modify_notice($date,$content){
        $data =['content'=>$content];
        return $this->update("date='".$date."'",$data);
    }

}
