<?php
namespace Dao\Kpzip_admin\Stat;
use \Dao;
class Country_data extends \Dao\Kpzip_admin\Kpzip_admin {

    protected static $_instance = null;
    /**
     * @return Dao\Kpzip_admin\Stat\Country_data
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    public function add_country_data($ymd){
        $time =time();
        //新增安装量、累计安装总量
        $sql = "
            INSERT INTO `{$this->_realTableName}` (ymd,qid,install,install_total,country,dateline)
            (
                SELECT $ymd AS ymd,QID AS `qid`,COUNT(CASE WHEN Ymd = {$ymd} THEN Ymd END) AS `install`,COUNT(*) AS `install_total`,country,$time
                FROM `stat_install_uid_channel_qid_only` GROUP BY country,QID
            ) 
            ON DUPLICATE KEY UPDATE `install`=VALUES(`install`),`install_total`=VALUES(`install_total`)
        ";
        $this->query($sql);
        
        
        //当天卸载量、累计卸载总量
        $sql = "
            INSERT INTO `{$this->_realTableName}` (ymd,qid,uninstall,uninstall_total,country)
                (
                    SELECT $ymd AS ymd,QID AS `qid`,COUNT(CASE WHEN Ymd = {$ymd} THEN Ymd END) AS `uninstall`,COUNT(*) AS `uninstall_total`,country
                    FROM `stat_uninstall_uid_channel_qid_only` GROUP BY country,QID
                )
            ON DUPLICATE KEY UPDATE `uninstall`=VALUES(`uninstall`),`uninstall_total`=VALUES(`uninstall_total`)
        ";
        $this->query($sql);
        
        //当天启动量
        $sql = "
           INSERT INTO `{$this->_realTableName}` (ymd,qid,online,country)
            (
                SELECT $ymd AS ymd,`qid`,COUNT(*) AS `online`,country
                FROM `stat_online_uid_channel_qid_temp` GROUP BY country,QID
            )
            ON DUPLICATE KEY UPDATE `online`=VALUES(`online`)
        ";
        $this->query($sql);
        
        
        //当天使用量
        $sql = "
            INSERT INTO `{$this->_realTableName}` (ymd,qid,online,country)
            (
                SELECT $ymd AS ymd,`qid`,COUNT(*) AS `online`,country
                FROM `stat_online_uid_channel_qid_temp` GROUP BY country,QID
            )
            ON DUPLICATE KEY UPDATE `online`=VALUES(`online`)
        ";
        $this->query($sql);
        
        //当天安装且当天卸载数
        $sql = "
            INSERT INTO `{$this->_realTableName}` (ymd,qid,install_uninstall,country)
            (
                SELECT a.Ymd AS ymd,a.QID AS qid,count(*) AS install_uninstall,a.country
                FROM `stat_install_uid_channel_qid_only` AS a LEFT JOIN `stat_uninstall_uid_channel_qid_only` AS b 
                ON a.uid=b.uid AND a.QID=b.QID AND a.Ymd=b.Ymd AND a.country = b.country
                WHERE a.Ymd={$ymd} AND b.Ymd={$ymd} GROUP BY a.country,a.QID
            )
            ON DUPLICATE KEY UPDATE `install_uninstall`=VALUES(`install_uninstall`)
        ";
        $this->query($sql);
        
        
        //当天启动量
        $sql = "
            INSERT INTO `{$this->_realTableName}` (ymd,qid,active,country)
            (
                SELECT $ymd AS ymd,QID AS qid,COUNT(*) AS `active`,country
                FROM `stat_active_uid_channel_qid_temp` GROUP BY country,QID
            )
            ON DUPLICATE KEY UPDATE `active`=VALUES(`active`)
        ";
        $this->query($sql);
    }
    
}
