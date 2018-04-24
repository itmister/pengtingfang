<?php
namespace VendorData\DownloadFile;
class Promotion_602gm extends Base {

    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }

    public function __construct(){
        $this->_remote_file_path = "http://tongji.602.com/index.php?r=Api/index&action=box_7654&uid=7654wd&datetime=%s&sign=%s";
        //$this->_save_path = "c:/test/%s.csv";
        $this->_save_path = "/app/www/jf7654/Public/upload/PerUpload/602gm/%s.csv";
    }

    public function get_data($date = ''){
        $date  = $date ? $date:date("Y-m-d",strtotime("-1 days"));
        $ymd = intval(date("Ymd",strtotime($date)));
        $remote_file_path =  sprintf($this->_remote_file_path,$ymd,md5(md5($ymd).'rkIquUIdV3M1xeht'));
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