<?php
namespace VendorData\DownloadFile;
class Promotion_storm extends Base {

    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }

    public function __construct(){
        $this->_remote_file_path = "http://union.baofeng.com/sub_channel/3c6db5dbaa4ce9d2ac8927931cdcaaec689e/%s.csv";
        //$this->_save_path = "c:/test/%s.csv";
        $this->_save_path = "/app/www/jf7654/Public/upload/PerUpload/storm/%s.csv";
    }

    public function get_data($date = ''){
        $date  = $date ? $date:date("Y-m-d",strtotime("-1 days"));
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
        array_shift($data);
        $arr = [];
        foreach ($data as $item ) {
            $item = trim(str_replace('"',"",$item));
            $d = explode(",",$item);
            $temp = [];
            $temp['org_id'] = trim($d[1]);
            $temp['count']  = intval($d[2]);
            $temp['active_count']  = intval($d[3]);
            $arr [] = $temp;
        }
        return $arr;
    }
}