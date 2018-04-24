<?php
namespace VendorData\DownloadFile;
class Promotion_360 extends Base {
    private $_map = ['360杀毒'=>'360sd','360安全卫士'=>'360safe','360安全浏览器'=>'360se'];
    private $_split_num = 95000; //95000之后为v2

    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }

    public function __construct(){
        $this->_remote_file_path ="http://youqian.360.cn/interface/ProxyStat?user_id=6526&auth=0431e6aed96ab25420f69a6edf1f4a51&date=%s";
      //  $this->_save_path = "c:/test/%s.txt";
        $this->_save_path = "/app/www/jf7654/Public/upload/PerUpload/360se/%s.csv";
    }

    public function get_data($date = ''){
        if (!$date){
            return false;
        }
        $ymd = intval(date("Ymd",strtotime($date)));
        $remote_file_path = sprintf($this->_remote_file_path ,$ymd);
        $save_path = sprintf($this->_save_path ,$ymd);
        if($this->get_remote_file($remote_file_path,$save_path)){
            $data = file($save_path);
            return $this->fiter_data($data);
        }else{
            @unlink($save_path);
            return false;
        }
    }

    public function fiter_data($data){
        $result = ['360sd'=>[],'360safe'=>[],'360se'=>[],'360safev2'=>[],'360sev2'=>[]];
        foreach ($data as $item ) {
            list($org_id,$promotions) = explode("-",$item);
            $promotions_arr = explode(",",$promotions);
            foreach($promotions_arr as $v){
                list($promotion,$count) = explode(":",$v);
                $s_name = intval($org_id) > $this->_split_num ? $this->_map[$promotion].'v2' : $this->_map[$promotion];
                $result[$s_name][] = ['org_id'=>$org_id,'count'=>intval($count)];
            }
        }
        return $result;
    }
}