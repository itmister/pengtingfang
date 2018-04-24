<?php
namespace Dao\Union;
use \Dao;
class Vendor_Data_History extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Vendor_Data_History
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function get_history($promotion,$ymd) {
        $sql = "select * from {$this->_realTableName} where promotion='{$promotion}' and ymd={$ymd} limit 1";
        $ret =  $this->query($sql);
        return $ret[0] ? $ret[0] : [];
    }

    /**
     * 拉取一定时间内成功的日期
     * @param $promotion
     * @param $start_date
     * @param $end_date
     * @return mixed
     */
    public function get_success_date($promotion,$start_date,$end_date){
        $sql = "select ymd from {$this->_realTableName} where promotion='{$promotion}' and status =1 and ymd >= {$start_date} and ymd <= {$end_date}";
        $ret =  $this->query($sql);
        $arr = [];
        if (is_array($ret) && $ret){
            foreach ($ret as $v){
                $arr[] = $v['ymd'];
            }
        }
        return $arr;
    }

    public function add_history($promotion,$ymd,$times,$md5sum,$mtime,$status = 0){
        $data = [
            "promotion"=>$promotion,
            'ymd'=>$ymd,
            'times'=>$times,
            'md5sum'=>$md5sum,
            'mtime'=>$mtime,
            'status'=>$status
        ];
       return $this->add($data,true);
    }


    public function update_data($promotion,$ymd,$data){
        return $this->update("promotion='{$promotion}' and ymd={$ymd}",$data);
    }

    public function change_status($promotion,$ymd,$status){
       return $this->update("promotion='{$promotion}' and ymd={$ymd}",['status'=>$status,'mtime'=>date("Y-m-d H:i:s")]);
    }
}
