<?php
namespace Dao\Huayangnianhua_admin\Stat;
use \Dao;
class Os_data extends \Dao\Huayangnianhua_admin\Huayangnianhua_admin {

    protected static $_instance = null;
    /**
     * @return Dao\Kpzip_admin\Stat\Os_data
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    public function add_os_data($ymd){
        $time =time();
        //新增安装量
        $sql = "
            INSERT INTO `{$this->_realTableName}` (ymd,qid,install,os,ver,dateline)
            (
                SELECT $ymd AS ymd,QID AS `qid`,COUNT(Ymd) AS `install`,os,ver,$time
                FROM `stat_install_uid_channel_qid_only` WHERE Ymd = {$ymd}  GROUP BY os,QID
            )
            ON DUPLICATE KEY UPDATE `install`=VALUES(`install`)
        ";
        $this->query($sql);
        
        //当天使用量
        $sql = "
            INSERT INTO `{$this->_realTableName}` (ymd,qid,online,os,ver,dateline)
            (
                SELECT $ymd AS ymd,`qid`,COUNT(*) AS `online`,os,ver,$time
                FROM `stat_online_uid_channel_qid_temp` GROUP BY os,QID
            )
            ON DUPLICATE KEY UPDATE `online`=VALUES(`online`)
        ";
        $this->query($sql);
        
        return true;
    }
}
