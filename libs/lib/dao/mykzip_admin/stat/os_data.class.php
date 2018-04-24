<?php
namespace Dao\Mykzip_admin\Stat;
use \Dao;
class Os_data extends \Dao\Mykzip_admin\Mykzip_admin {

    protected static $_instance = null;
    /**
     * @return Dao\Mykzip_admin\Stat\Os_data
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
            INSERT INTO `{$this->_realTableName}` (ymd,qid,install,os,dateline)
            (
                SELECT $ymd AS ymd,QID AS `qid`,COUNT(Ymd) AS `install`,os,$time
                FROM `stat_install_uid_channel_qid_only` WHERE Ymd = {$ymd} GROUP BY os,QID
            )
            ON DUPLICATE KEY UPDATE `install`=VALUES(`install`)
        ";
        $this->query($sql);
        
        //当天使用量
        $sql = "
            INSERT INTO `{$this->_realTableName}` (ymd,qid,online,os,dateline)
            (
                SELECT $ymd AS ymd,`qid`,COUNT(*) AS `online`,os,$time
                FROM `stat_online_uid_channel_qid_temp` GROUP BY os,QID
            )
            ON DUPLICATE KEY UPDATE `online`=VALUES(`online`)
        ";
        $this->query($sql);
        
        return true;
    }
}
