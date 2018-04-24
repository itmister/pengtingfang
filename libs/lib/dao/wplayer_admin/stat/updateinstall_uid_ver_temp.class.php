<?php
namespace Dao\Wplayer_admin\Stat;
use \Dao;
class Updateinstall_uid_ver_temp extends \Dao\Wplayer_admin\Wplayer_admin{

    protected static $_instance = null;
    /**
     * @return Updateinstall_uid_ver_temp
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    public function get_kpzip_updateinstall($ymd){
        $sql = "
            SELECT
            	u.ver,
            	u.ymd,
            	count(DISTINCT u.uid) AS kpzip_updateinstall
            FROM
            	`{$this->_get_table_name()}` AS u
            INNER JOIN `stat_jingpin_uid_ky_temp` AS j ON u.uid = j.uid
            WHERE
            	u.ymd = {$ymd}
            GROUP BY
            	u.ver
        ";
        return $this->query($sql);
        
    }
}
