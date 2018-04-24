<?php

namespace Dao\Union;
use \Dao;
class Log_Soft_Apply extends Union {
    protected static $_instance = null;
    /**
     * @return Log_Soft_Apply
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    //算上自己的位置
    public function get_num_before($name,$dateline){
        $sql = "select count(1) as num from log_soft_apply where status  = 0 and name ='{$name}' and  dateline <= {$dateline}";
        $data = $this->query($sql);
        return $data[0]['num'] ? $data[0]['num'] :0;
    }

    /**
     * 计算用户软件申请排队的位置
     * @param $uid
     * @param string $soft_names
     * @return array
     */
    public function user_apply_queue($uid,$soft_names = "'360dh','sgdh','bdbrowserv2','qqpcmgr','pahy','duba','mtll','goal','360sd','360safe','duba','qqbrowserv2'"){
        #TODO 360sd,360safe,duba 暂停新用户推广 排队效果 增加 5000个虚拟用户 每天更新 1到5个 虚拟用户 代码如下 曹磊 20160225
        $day = date("Ymd");
        $sql_js = "select uid  from {$this->_get_table_name()} where status = 1 and updateline={$day} and name in ('360sd','360safe','duba','qqbrowserv2','qqpcmgr','360dh')";
        $data_js = $this->query($sql_js);
        if(empty($data_js)){
            $num_limit = rand(1,5);
            $id_arr = array();
            foreach(array('360sd','360safe','duba','qqbrowserv2','qqpcmgr','360dh') as $v){
                $list_id_arr = $this->query("select id from {$this->_get_table_name()} where name='{$v}' and status=0 and uid>1000000 limit {$num_limit}");
                foreach($list_id_arr as $vv){
                    $id_arr[] = $vv['id'];
                }
            }
            if($id_arr){
                $id_str = implode(',',$id_arr);
                $this->query("update {$this->_get_table_name()} set status=1,updateline={$day} where id in ({$id_str}) ");
            }
        }
        #TODO end

        $sql = "select name,dateline  from log_soft_apply where status = 0 and name in ({$soft_names}) and uid = {$uid}";
        $data = $this->query($sql);
        $apply_queue_map = [];
        if (!empty($data)){
            foreach($data as $val){
                $apply_queue_map[$val['name']] = $this->get_num_before($val['name'],$val['dateline']);
            }
        }
        return $apply_queue_map;
    }

    /**
     * 获取各软件排除人数
     * @return array
        software : num
     * )
     */
    public function apply_count() {
        $sql = <<<eot
select
    count(*) as num,
    `name` as software
from
    {$this->_get_table_name()}
where
    `status` = 0
group by
    `name`
eot;
        $result = [];
        foreach ($this->query( $sql ) as $row ) $result[$row['software']] = intval( $row['num']);
        return $result;

    }
}
