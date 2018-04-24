<?php
namespace Dao\Wh7654_log;
use \Dao;
class Browser extends Wh7654_log {
    protected static $_instance = null;
    /**
     * @return Dao\Wh7654_log\Browser
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * 获取信息 渠道数据的导航信息
     * @return array
     */
    public function get_all_browser_qid($ymd){
        $sql = "SELECT {$ymd} as ymd,QID as qid,count(DISTINCT UID) as all_num FROM `{$this->_realTableName}{$ymd}` group by QID";
        return $this->query($sql);
    }

    /**
     * 获取信息 渠道数据的导航信息
     * @return array
     */
    public function get_hao123_browser_qid($ymd,$url){
        $sql = "SELECT {$ymd} as ymd,QID as qid,count(DISTINCT UID) as to_hao123_num FROM `{$this->_realTableName}{$ymd}` where HomePage LIKE '%{$url}%'group by QID";
        return $this->query($sql);
    }

    /**
     * 获取信息 渠道数据的导航信息
     * @return array
     */
    public function get_360dh_browser_qid($ymd,$url){
        $sql = "SELECT {$ymd} as ymd,QID as qid,count(DISTINCT UID) as to_360dh_num FROM `{$this->_realTableName}{$ymd}` where HomePage LIKE '%{$url}%'group by QID";
        return $this->query($sql);
    }

    /**
     * 获取信息 渠道数据的导航信息
     * @return array
     */
    public function get_other_browser_qid($ymd,$url){
        $w = implode("%' AND HomePage NOT LIKE '%",$url);
        $w = "HomePage NOT LIKE '%".$w."%'";
        $sql = "SELECT {$ymd} as ymd,QID as qid,count(DISTINCT UID) as to_other_num FROM `{$this->_realTableName}{$ymd}` where HomePage<>'' and {$w} group by QID";
        return $this->query($sql);
    }

    /**
     * 获取信息 渠道数据的导航信息
     * @return array
     */
    public function get_hao123_ip_qid($ymd,$url){
        $sql = "SELECT {$ymd} as ymd,QID as qid,count(DISTINCT IP) as to_hao123_ip_num FROM `{$this->_realTableName}{$ymd}` where HomePage LIKE '%{$url}%'group by QID";
        return $this->query($sql);
    }

    /**
     * 获取信息 渠道数据的导航信息
     * @return array
     */
    public function get_360dh_ip_qid($ymd,$url){
        $sql = "SELECT {$ymd} as ymd,QID as qid,count(DISTINCT IP) as to_360dh_ip_num FROM `{$this->_realTableName}{$ymd}` where HomePage LIKE '%{$url}%'group by QID";
        return $this->query($sql);
    }


    /*
     * 获取tn的访问数和ip数 去重后的
     * */
    public function get_tn_data_in_temp($ymd){
        $sql = "SELECT {$ymd} as ymd,substring_index(rtrim(HomePage),'=',-1) as tn,count(DISTINCT UID) as to_num,count(DISTINCT IP) as to_ip_num,
                case when LOCATE('www.hao123.com',HomePage)>0 THEN 'hao123' when LOCATE('hao.360.cn',HomePage)>0 then '360dh' else 'other' END as soft_id
                FROM `{$this->_realTableName}{$ymd}` WHERE LOWER(HomePage) not like '%www.duba.com%' GROUP BY substring_index(rtrim(HomePage),'=',-1);";
        return $this->query($sql);

    }

    /*
     * 金山导航
     * */
    public function get_tn_data_in_jsdh_temp($ymd){
         $sql = "SELECT {$ymd} as ymd,substring_index(rtrim(HomePage),'_',-1) as tn,count(DISTINCT UID) as to_num,count(DISTINCT IP) as to_ip_num,
                case when LOCATE('www.duba.com',HomePage)>0 THEN 'jsdh' END as soft_id
                FROM `{$this->_realTableName}{$ymd}` WHERE LOWER(HomePage) like '%www.duba.com%' GROUP BY substring_index(rtrim(HomePage),'_',-1);";
        return $this->query($sql);
    }

    /*
    * 获取每个渠道的tn的访问数和ip数 去重后的
    * */
    public function get_tn_qid_data_in_temp($ymd){
        $sql = "SELECT {$ymd} as ymd,substring_index(ltrim(QID),'_',1) as qid,substring_index(rtrim(HomePage),'=',-1) as tn,count(DISTINCT UID) as to_num,
                count(DISTINCT IP) as to_ip_num,case when LOCATE('www.hao123.com',HomePage)>0 THEN 'hao123' when LOCATE('hao.360.cn',HomePage)>0 then '360dh' else 'other' END as soft_id
                FROM `{$this->_realTableName}{$ymd}` WHERE LOWER(HomePage) not like '%www.duba.com%' GROUP BY substring_index(rtrim(HomePage),'=',-1),substring_index(ltrim(QID),'_',1);";
        return $this->query($sql);
    }

    /*
     * 金山导航
     * */
    public function get_tn_qid_data_in_jsdh_temp($ymd){
        $sql = "SELECT {$ymd} as ymd,substring_index(ltrim(QID),'_',1) as qid,substring_index(rtrim(HomePage),'_',-1) as tn,count(DISTINCT UID) as to_num,
                count(DISTINCT IP) as to_ip_num,case when LOCATE('www.duba.com',HomePage)>0 THEN 'jsdh' END as soft_id
                FROM `{$this->_realTableName}{$ymd}` WHERE LOWER(HomePage) like '%www.duba.com%' GROUP BY substring_index(rtrim(HomePage),'_',-1),substring_index(ltrim(QID),'_',1);";
        return $this->query($sql);
    }



    public function get_tn_sub_qid_data_in_temp($ymd){
        $sql = "SELECT {$ymd} as ymd,QID as qid,substring_index(rtrim(HomePage),'=',-1) as tn,count(DISTINCT UID) as to_num,
                count(DISTINCT IP) as to_ip_num,case when LOCATE('www.hao123.com',HomePage)>0 THEN 'hao123' when LOCATE('hao.360.cn',HomePage)>0 then '360dh' else 'other' END as soft_id
                FROM `{$this->_realTableName}{$ymd}` WHERE LOWER(HomePage) not like '%www.duba.com%' GROUP BY substring_index(rtrim(HomePage),'=',-1),QID;";
        return $this->query($sql);
    }
    /*
        * 金山导航
        * */
    public function get_tn_sub_qid_data_in_jsdh_temp($ymd){
        $sql = "SELECT {$ymd} as ymd,QID as qid,substring_index(rtrim(HomePage),'_',-1) as tn,count(DISTINCT UID) as to_num,
                count(DISTINCT IP) as to_ip_num,case when LOCATE('www.duba.com',HomePage)>0 THEN 'jsdh' END as soft_id
                FROM `{$this->_realTableName}{$ymd}` WHERE LOWER(HomePage) like '%www.duba.com%' GROUP BY substring_index(rtrim(HomePage),'_',-1),QID;";
        return $this->query($sql);
    }

    /**
     * 获取信息 其他的导航信息
     * @return array
     */
    public function get_other_browser_num($ymd,$url){
        $sql = "SELECT count(distinct IP) as to_ip_num,count(DISTINCT UID) as to_num FROM `{$this->_realTableName}{$ymd}` where HomePage LIKE '%{$url}%'";
        return $this->query($sql);
    }


    public function get_all_online_competition($ymd,$softId,$v){
        $sql = "SELECT count(DISTINCT UUID) as InstallCount FROM `{$this->_realTableName}{$ymd}` WHERE Version in ({$v}) and LOWER(ProcessList) LIKE '%{$softId}%';";
        return $this->query($sql);
    }

    public function get_all_online_security($ymd,$softId,$v){
        $sql = "SELECT count(DISTINCT UUID) as InstallCount FROM `{$this->_realTableName}{$ymd}` WHERE Version in ({$v}) and LOWER(ProcessList) LIKE '%{$softId}%';";
        return $this->query($sql);
    }

    /**
     * 获取信息 产品数据的安装信息
     * @return array
     */
    public function get_online_uuid_count($ymd,$v){
        $sql = " SELECT count(*) as `online` from (SELECT UUID FROM `{$this->_realTableName}{$ymd}` where Version in ({$v}) group by UUID) as aa";
        return $this->query($sql);
    }
}
