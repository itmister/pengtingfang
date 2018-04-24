<?php
namespace Dao\Huayangnianhua_admin\Stat;
use \Dao\Huayangnianhua_admin;
class Channel_data extends \Dao\Huayangnianhua_admin\Huayangnianhua_admin {

    /**
     * @return Channel_data
     */
    public static function get_instance(){
        return parent::get_instance();
    }

    public function in_sub_channel_data($ymd){
        $time =time();
        $sql = "insert into `{$this->_realTableName}` (ymd,qid,install,is_wb,install_total,install360all,install360,installno360all,installno360,install_qqgj,install_jsdb,dateline)(SELECT $ymd,QID as qid,count(case when Ymd = {$ymd} then Ymd end) as `install`,count(case when is_wb = 1 and Ymd = {$ymd} then Ymd end) as `is_wb`,count(*) as `install_total`,count(case when is_360 = 1 and Ymd>=20161118 then Ymd end) as `install360all`,count(case when is_360 = 1 and Ymd = {$ymd} then Ymd end) as `install360`,count(case when is_360 = 0 and Ymd>=20161118 then Ymd end) as `installno360all`,count(case when is_360 = 0 and Ymd = {$ymd} then Ymd end) as `installno360`,count(case when is_qqgj = 1 and is_360 = 0 and Ymd = {$ymd} then Ymd end) as `install_qqgj`,count(case when is_jsdb = 1 and is_360 = 0 and Ymd = {$ymd} then Ymd end) as `install_jsdb`,$time FROM stat_install_uid_channel_qid_only GROUP BY QID) on duplicate key update install=values(install),is_wb=values(is_wb),install_total=values(install_total),install360all=values(install360all),install360=values(install360),installno360all=values(installno360all),installno360=values(installno360),install_qqgj=values(install_qqgj),install_jsdb=values(install_jsdb)";
        return $this->query($sql);
    }

    public function in_sub_channel_data_360($ymd){
        $time =time();
        $sql = " insert into `{$this->_realTableName}` (ymd,qid,install360all,install360,installno360all,installno360,dateline)(SELECT $ymd,QID as qid,count(case when is_360 = 1 and Ymd>=20161118 and Ymd<={$ymd} then Ymd end) as `install360all`,count(case when is_360 = 1 and Ymd = {$ymd} then Ymd end) as `install360`,count(case when is_360 = 0 and Ymd>=20161118 and Ymd<={$ymd} then Ymd end) as `installno360all`,count(case when is_360 = 0 and Ymd = {$ymd} then Ymd end) as `installno360`,$time FROM stat_install_uid_channel_qid_only GROUP BY QID) on duplicate key update install360all=values(install360all),install360=values(install360),installno360all=values(installno360all),installno360=values(installno360)";
        return $this->query($sql);
    }

    public function get_uninstall_count($ymd){
        $sql = "create table temp_channel_qid_uninstall as
SELECT QID as qid,count(*) as `uninstall_total`,count(case when Ymd = {$ymd} then Ymd end) as `uninstall` FROM stat_uninstall_uid_channel_qid_only GROUP BY QID;";
        $this->query($sql);

        $up_sql = "UPDATE `{$this->_realTableName}` as a LEFT JOIN temp_channel_qid_uninstall as b on a.qid=b.qid set a.`uninstall`=b.`uninstall`,a.uninstall_total=b.uninstall_total WHERE a.ymd={$ymd};";
        $this->query($up_sql);
        $drop_sql = "DROP TABLE temp_channel_qid_uninstall;";
        return $this->query($drop_sql);
    }

    public function get_online_count($ymd){
        $sql = "create table temp_channel_qid_online as
                SELECT b.num,b.qid FROM `{$this->_realTableName}` as a LEFT JOIN (
                 SELECT count(*) as num , qid FROM stat_online_uid_channel_qid_temp GROUP BY qid
                ) as b on a.qid=b.qid WHERE a.ymd={$ymd} and b.num is not NULL
        ";
        $this->query($sql);

        $up_sql = "UPDATE `{$this->_realTableName}` as a LEFT JOIN temp_channel_qid_online as b on a.qid=b.qid set a.`online`=b.`num` WHERE a.ymd={$ymd};";
        $this->query($up_sql);

        $drop_sql = "DROP TABLE temp_channel_qid_online;";
        return $this->query($drop_sql);
    }
    
    public function select_count($params){
        $sql = "SELECT COUNT(DISTINCT substring_index(trim(d.qid),'_',1),d.ymd) AS num FROM `stat_sub_channel_data` AS d LEFT JOIN `gs_channel` AS c ON d.qid = c.qid";
        if($params['where']){
            $sql .= " WHERE ".$params['where'];
        }
        $result = current($this->query($sql));
        return $result['num'] ? $result['num'] : 0;
    }
    
    public function select_list($params){
        $field = 'substring_index(trim(d.qid),"_",1) AS mainqid,d.ymd,SUM(d.original_install) AS original_install,SUM(d.install) AS install,SUM(d.is_wb) AS is_wb,SUM(d.install_total) AS install_total,SUM(d.online) AS online,SUM(d.active) AS active,SUM(d.uninstall) AS uninstall,SUM(d.uninstall_total) AS uninstall_total,SUM(d.install_uninstall) AS install_uninstall,SUM(d.uninstall1) AS uninstall1,SUM(d.uninstall3) AS uninstall3,SUM(d.uninstall5) AS uninstall5,SUM(d.uninstall7) AS uninstall7,SUM(d.online1) AS online1,SUM(d.online7) AS online7,SUM(d.online15) AS online15,SUM(d.online30) AS online30,SUM(d.online60) AS online60,SUM(d.kpzip_install) AS kpzip_install,SUM(d.updateinstall) AS updateinstall,SUM(d.install360all) AS install360all,SUM(d.install360) AS install360,SUM(d.installno360all) AS installno360all,SUM(d.installno360) AS installno360,SUM(d.install_qqgj) AS install_qqgj,SUM(d.install_jsdb) AS install_jsdb,SUM(d.original_mini_click) AS original_mini_click,SUM(d.mini_click) AS mini_click,SUM(d.original_mini_show) AS original_mini_show,SUM(d.mini_show) AS mini_show';
        $sql = "SELECT {$field} FROM `stat_sub_channel_data` AS d LEFT JOIN `gs_channel` AS c ON d.qid = c.qid";
        if($params['where']){
            $sql .= " WHERE ".$params['where'];
        }
        $sql .=" GROUP BY mainqid,d.ymd";
        if($params['orderby']){
            $sql .= " ORDER BY ".$params['orderby'];
        }
        if($params['limit']){
            $sql .= " LIMIT ".$params['limit'];
        }		
        return $this->query($sql);
    }

}
