<?php
namespace Union\Vendordata;
/**
 * @package Union\Vendordata
 */
use \Dao\Union\Vendor_Data_History;
use \Dao\Union\Vendor_Org_Data;
class Original_Data {
    protected static $_instance = null;
    private $_360 = ['360safe','360se','602gm','360safev2','360sev2'];
    private $_log_path = "/app/vendordata";
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    /**
     * 从数据库吐出确认可用数据
     * @param $promotion
     * @param $ymd
     */
    public function get_vendor_data($promotion,$ymd = ''){
        $ymd = $ymd ? $ymd : intval(date('Ymd',strtotime("-1 days")));
        if(in_array($promotion ,$this->_360)){//360的软件 在vendor_data_history  存的360 在data里头存的是拆分后的软件数值
            $info  = Vendor_Data_History::get_instance()->get_history("360",$ymd);
        }else{
            $info  = Vendor_Data_History::get_instance()->get_history($promotion,$ymd);
        }
        if (!$info['status']){  // 如果数据没有或者还未确认则返回false
            return false;
        }
        return Vendor_Org_Data::get_instance()->get_data($promotion,$ymd);
    }

    /**
     * 后台自动抓取抓取厂商原始数据
     * @param $promotion
     * @param string $ymd
     * @return bool
     */
    public function fetch_vendor_data($promotion,$ymd = '',$type = "AutoCrawl"){
        if (empty($ymd)){
            return false;
        }
        $date =  date('Y-m-d',strtotime($ymd));
        $class_name = 'VendorData\\'.$type.'\\Promotion_'.$promotion;
        if (!class_exists($class_name)){
            return false;
        }
        $object = $class_name::get_instance();
        $data  = $object->get_data($date);
        return $data;
    }

    /**
     * crontab job 后台自动抓取专用方法
     * @param $promotion
     * @param string $ymd
     */
    public function autocrawl_job_run($promotion,$ymd = ''){
        $ymd = $ymd ? $ymd : intval(date('Ymd',strtotime("-1 days")));
        //判断数据是否已经可用
        $info  = Vendor_Data_History::get_instance()->get_history($promotion,$ymd);
        if ($info['status']){
            return ;
        }
        //抓取数据
        $data = $this->fetch_vendor_data($promotion,$ymd,'AutoCrawl');
        if (is_array($data) && $data){
            $md5 = md5(serialize($data));
            if ($info){ //非第一次抓取
                if ($md5 ==  $info['md5sum']){ //和上次md5值一样 就标示数据可用 不再抓取
                    $ret = Vendor_Data_History::get_instance()->change_status($promotion,$ymd,1);
                    if($ret){
                        $this->_log($promotion,$promotion." - ".$info['ymd']."数据确认完毕");
                    }else{
                        $this->_log($promotion,$promotion." - ".$info['ymd']."标示数据确认完毕失败");
                    }
                    return ;
                }else{ //和上次不一样 覆盖上次记录 留待下次再确认
                    Vendor_Data_History::get_instance()->begin_transaction();
                    $d['times'] = $info['times'] + 1;
                    $d['md5sum'] = $md5;
                    $d['mtime'] = date("Y-m-d H:i:s");
                    $ret1 = Vendor_Data_History::get_instance()->update_data($promotion,$ymd,$d);
                    $ret2 = Vendor_Org_Data::get_instance()->add_data($promotion,$ymd,$data);
                    if($ret1 && $ret2){
                        Vendor_Data_History::get_instance()->commit();
                        $this->_log($promotion,$promotion." - ".$info['ymd']."覆盖上次数据成功 ");
                    }else{
                        Vendor_Data_History::get_instance()->rollback();
                        $this->_log($promotion,$promotion." - ".$info['ymd']."覆盖上次数据失败");
                    }
                    return ;
                }
            }else{ //第一次抓取
                Vendor_Data_History::get_instance()->begin_transaction();
                $ret1 = Vendor_Data_History::get_instance()->add_history($promotion,$ymd,1,$md5,date('Y-m-d H:i:s'));
                $ret2 = Vendor_Org_Data::get_instance()->add_data($promotion,$ymd,$data);
                if($ret1 && $ret2){
                    Vendor_Data_History::get_instance()->commit();
                    $this->_log($promotion,$promotion." - ".$info['ymd']."第一抓取数据成功");
                }else{
                    Vendor_Data_History::get_instance()->rollback();
                    $this->_log($promotion,$promotion." - ".$info['ymd']."第一抓取数据失败");
                }
                return ;
            }
        }
    }

    /**
     * crontab job 文件下载专用方法
     * @param $promotion
     * @param string $ymd
     */
    public function downloadfile_job_run($promotion,$ymd){
        //判断数据是否已经可用
        $info  = Vendor_Data_History::get_instance()->get_history($promotion,$ymd);
        if ($info['status']){
            return ;
        }
        //抓取数据
        $data = $this->fetch_vendor_data($promotion,$ymd,'DownloadFile');
        if (is_array($data)){ //只要文件存在，不考虑数据是否为空
            $md5 = md5(serialize($data));
            if ($info){ //非第一次抓取
                if ($md5 ==  $info['md5sum']){ //和上次md5值一样 就标示数据可用 不再抓取
                    $ret = Vendor_Data_History::get_instance()->change_status($promotion,$ymd,1);
                    if($ret){
                        $this->_log($promotion,$promotion." - ".$info['ymd']."数据确认完毕 ");
                    }else{
                        $this->_log($promotion,$promotion." - ".$info['ymd']."标示数据确认完毕失败");
                    }
                    return ;
                }else{ //和上次不一样 覆盖上次记录 留待下次再确认
                    Vendor_Data_History::get_instance()->begin_transaction();
                    $d['times'] = $info['times'] + 1;
                    $d['md5sum'] = $md5;
                    $d['mtime'] = date("Y-m-d H:i:s");
                    $ret1 = Vendor_Data_History::get_instance()->update_data($promotion,$ymd,$d);
                    if (!$ret1){
                        Vendor_Data_History::get_instance()->rollback();
                        $this->_log($promotion,$promotion." - ".$info['ymd']."覆盖上次数据失败");
                    }
                    //@todo 360五款软件 放在一起需要拆分
                    if($promotion == '360') {
                        foreach ($data as $key => $val) {
                            $ret = Vendor_Org_Data::get_instance()->add_data($key, $ymd, $val);
                            if (!$ret){
                                Vendor_Data_History::get_instance()->rollback();
                                $this->_log($promotion,$val . " - " . $info['ymd'] . "覆盖上次数据失败");
                                return ;
                            }
                        }
                    }else{
                        $ret = Vendor_Org_Data::get_instance()->add_data($promotion,$ymd,$data);
                        if( !$ret){
                            Vendor_Data_History::get_instance()->rollback();
                            $this->_log($promotion,$promotion." - ".$info['ymd']."覆盖上次数据失败");
                            return ;
                        }
                    }
                    Vendor_Data_History::get_instance()->commit();
                    $this->_log($promotion,$promotion." - ".$info['ymd']."覆盖上次数据成功");
                    return ;
                }
            }else{ //第一次抓取
                Vendor_Data_History::get_instance()->begin_transaction();
                $ret1 = Vendor_Data_History::get_instance()->add_history($promotion,$ymd,1,$md5,date('Y-m-d H:i:s'));
                if (!$ret1){
                    Vendor_Data_History::get_instance()->rollback();
                    $this->_log($promotion,$promotion." - ".$info['ymd']."第一抓取数据失败");
                }
                //360五款软件 放在一起需要拆分
                if($promotion == '360'){
                    foreach ($data as $key => $val){
                        $ret = Vendor_Org_Data::get_instance()->add_data($key,$ymd,$val);
                        if( !$ret){
                            Vendor_Data_History::get_instance()->rollback();
                            $this->_log($promotion,$val." - ".$info['ymd']."第一抓取数据失败");
                            return;
                        }
                    }
                }else{
                    $ret = Vendor_Org_Data::get_instance()->add_data($promotion,$ymd,$data);
                    if( !$ret){
                        Vendor_Data_History::get_instance()->rollback();
                        $this->_log($promotion,$promotion." - ".$info['ymd']."第一抓取数据失败");
                        return;
                    }
                }
                Vendor_Data_History::get_instance()->commit();
                $this->_log($promotion,$promotion." - ".$ymd."第一抓取数据成功");
                return ;
            }
        }
    }

    /**
     * crontab job 邮件附件专用方法
     * @param $promotion
     * @param string $ymd
     */
    public function attachment_job_run($promotion,$ymd = ''){
        $ymd = $ymd ? $ymd : intval(date('Ymd',strtotime("-1 days")));
        //判断数据是否已经可用
        $info  = Vendor_Data_History::get_instance()->get_history($promotion,$ymd);
        if ($info['status']){
            return ;
        }
        //抓取数据
        $data = $this->fetch_vendor_data($promotion,$ymd,'Attachment');
        if (is_array($data) && $data){
            Vendor_Data_History::get_instance()->begin_transaction();
            $ret1 = Vendor_Data_History::get_instance()->add_history($promotion,$ymd,1,'',date('Y-m-d H:i:s'),1);
            $ret2 = Vendor_Org_Data::get_instance()->add_data($promotion,$ymd,$data);
            if($ret1 && $ret2){
                Vendor_Data_History::get_instance()->commit();
                $this->_log($promotion,$promotion." - ".$info['ymd']."取数据成功");
            }else{
                Vendor_Data_History::get_instance()->rollback();
                $this->_log($promotion,$promotion." - ".$info['ymd']."取数据失败");
            }
            return ;
        }
    }

    /**
     * crontab job 存储过程专用方法
     * @param $promotion
     * @param string $ymd
     */
    public function db_job_run($promotion,$ymd = ''){
        $ymd = $ymd ? $ymd : intval(date('Ymd',strtotime("-1 days")));
        //判断数据是否已经可用
        $info  = Vendor_Data_History::get_instance()->get_history($promotion,$ymd);
        if ($info['status']){
            return ;
        }
        //抓取数据
        $data = $this->fetch_vendor_data($promotion,$ymd,'DB');
        if (is_array($data) && $data){
            Vendor_Data_History::get_instance()->begin_transaction();
            $ret1 = Vendor_Data_History::get_instance()->add_history($promotion, $ymd, 1, '', date('Y-m-d H:i:s'),1);
            $ret2 = Vendor_Org_Data::get_instance()->add_data($promotion, $ymd, $data);
            if ($ret1 && $ret2) {
                Vendor_Data_History::get_instance()->commit();
                 $this->_log($promotion,$promotion . " - " . $info['ymd'] . "取数据成功 ");
            } else {
                Vendor_Data_History::get_instance()->rollback();
                $this->_log($promotion,  $promotion . " - " . $info['ymd'] . "取数据失败");
            }
            return;
        }
    }

    private function _log($promotion,$msg){
        $date = date('d.m.Y h:i:s');
        $log = "Date:".$date." | ".$msg."\n";
        if (!is_dir($this->_log_path.'/'.$promotion.'/')){
            mkdir($this->_log_path.'/'.$promotion,0777);
        }
        error_log($log, 3, $this->_log_path.'/'.$promotion.'/'.date("Ymd").".log");
    }
}
