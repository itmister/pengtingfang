<?php
namespace VendorData\AutoCrawl;
class Promotion_znysrf extends Base {
    public static function get_instance($class = __CLASS__){
        return parent::get_instance($class);
    }
    public function __construct(){
        $this->_login_url = "http://180.150.177.249/login/ajax_login";
        $this->_cookiejar_file = $this->make_cookie_jar("znysrf");
        $this->_login_params =[
            'account'=>'znyzxt7654',
            'pwd'=>'772853'
        ];
        $this->_remote_file_path = "http://180.150.177.249/index/lianmeng?day_start=%s&day_end=%s&csv=yes";
        //$this->_save_path = "E:/test/%s.csv";
        $this->_save_path = "/app/www/jf7654/Public/upload/PerUpload/znysrf/%s.csv";

    }

    public function get_data($date = ''){
        $date  = $date ? $date:date("Y-m-d",strtotime("-1 days"));
        $ymd = date("Y-m-d",strtotime($date));
        $this->login($this->_login_url,"POST",$this->_login_params,$this->_cookiejar_file);
        $save_path = sprintf($this->_save_path,$ymd);
        $remote_file_path = sprintf($this->_remote_file_path,$ymd,$ymd);
        if(file_exists($save_path)){
            $data = file($save_path);
            $leng = count($data);
            if($leng > 1){
                return $this->fiter_data($data);
            }else{
                @unlink($save_path);
                return false;
            }
        }else{
            if($this->get_remote_file($remote_file_path,$save_path,'',$this->_cookiejar_file)){
                $data = file($save_path);
                return $this->fiter_data($data);
            }else{
                @unlink($save_path);
                return false;
            }
        }
    }

    /**
     * 拿到表格数据
     * @param $data
     * @return array  [子渠道名,结算UV]
     */
    public function fiter_data($data){
        array_shift($data);
        $arr = [];
        $leng = count($data);
        if($leng > 0){
            foreach ($data as $item ) {
                $d = explode(",",$item);
                $org_id_str = explode("_",$d[1]);
                if($org_id_str[1] > 0){
                    $org_id = $org_id_str[1];
                    $temp = [];
                    $temp['org_id'] = trim($org_id);
                    $temp['count']  = intval($d[2]);
                    $arr [] = $temp;
                }else{
                    continue;
                }
            }
            return $arr;
        }else{
            return false;
        }

    }
}