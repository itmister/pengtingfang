<?php
namespace Dao\Udashi_admin\Stat;
use \Dao\Udashi_admin;
class Channel_data extends \Dao\Udashi_admin\Udashi_admin {

    /**
     * @return Channel_data
     */
    public static function get_instance(){
        return parent::get_instance();
    }

    public function in_sub_channel_data($ymd){
        $time =time();
        $sql = " insert into `{$this->_realTableName}` (ymd,qid,install,install_total,dateline)(SELECT $ymd,QID as qid,count(case when Ymd = {$ymd} then Ymd end) as `install`,count(case when Ymd <= {$ymd} then Ymd end) as `install_total`,$time FROM stat_install_uid_channel_qid_only GROUP BY QID) on duplicate key update install=values(install),install_total=values(install_total)";
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

    public function sem_count($array){
        $sql = " SELECT sum(a.num) as num from (
                SELECT count(*) as num FROM stat_channel_data where {$array['where']}
                UNION ALL
                SELECT count(*) as num FROM stat_channel_data_uefi where {$array['where']}) as a;";
        $result = current($this->query($sql));
        return $result['num'] ? $result['num'] : 0;
    }
    
    public function sem_sum_all($array){
      
        $sql ="select SUM(trl) trl,SUM(zxl) zxl,SUM(djl) djl,SUM(install) install,SUM(online) online,SUM(active) active,SUM(install_uninstall) install_uninstall from 
 (SELECT ymd,qid,original_install,`install`,install_total,`online`,active,active_total,`uninstall`,uninstall_total
,install_uninstall,dateline,trl,zxl,djl FROM stat_channel_data where {$array['where']}
UNION ALL
                 SELECT ymd,qid,original_install,`install`,install_total,`online`,active,active_total
,`uninstall`,uninstall_total,install_uninstall,dateline,trl,zxl,djl FROM
 stat_channel_data_uefi where {$array['where']} ) a";
        $result = $this->query($sql);
        return $result[0];
    }
    

    public function sem_select($array){
        $sql = " SELECT ymd,qid,original_install,`install`,install_total,`online`,active,active_total,`uninstall`,uninstall_total,install_uninstall,dateline,trl,zxl,djl FROM stat_channel_data where {$array['where']}
UNION ALL
                 SELECT ymd,qid,original_install,`install`,install_total,`online`,active,active_total,`uninstall`,uninstall_total,install_uninstall,dateline,trl,zxl,djl FROM stat_channel_data_uefi where {$array['where']} ORDER BY {$array['orderby']} limit {$array['limit']};";

        return $this->query($sql);
    }

    public function sem_select_new($array,$limit = ''){
        $sql = "SELECT a.ymd,a.trl,a.zxl,a.djl,a.qid,sum(a.`install`) as `install`,sum(a.`online`) as `online`,sum(a.active) as active ,sum(a.haiyuan) as haiyuan,sum(a.install_uninstall) as install_uninstall,sum(a.uninstall1) as uninstall1,sum(a.uninstall7) as uninstall7,sum(a.online1) as online1,sum(a.online7) as online7,sum(a.online15) as online15,sum(a.online30) as online30 FROM (
                    SELECT ymd,trl,zxl,djl,'up.fenhao.me' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem_old_new']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'qidong.fenhao.me' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem1_old_new']}) and {$array['where']} GROUP BY ymd
                     UNION ALL
                    SELECT ymd,trl,zxl,djl,'qidong.fenhao.me_001' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem001']}) and {$array['where']} GROUP BY ymd
                     UNION ALL
                    SELECT ymd,trl,zxl,djl,'qidong.fenhao.me_002' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem002']}) and {$array['where']} GROUP BY ymd
                     UNION ALL
                    SELECT ymd,trl,zxl,djl,'qidong.fenhao.me_003' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem003']}) and {$array['where']} GROUP BY ymd
                     UNION ALL
                    SELECT ymd,trl,zxl,djl,'qidong.fenhao.me_004' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem004']}) and {$array['where']} GROUP BY ymd
                     UNION ALL
                    SELECT ymd,trl,zxl,djl,'qidong.fenhao.me_005' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem007']}) and {$array['where']} GROUP BY ymd
                     UNION ALL
                    SELECT ymd,trl,zxl,djl,'up.fenhao.me_qj' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem005']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'qidong.fenhao.me_006' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem008']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'qidong.fenhao.me_007' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem009']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'qidong.fenhao.me_008' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem010']}) and {$array['where']} GROUP BY ymd


                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'up.fenhao.me' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi_old_new']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'qidong.fenhao.me' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi1_old_new']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'qidong.fenhao.me_001' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi001']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'qidong.fenhao.me_002' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi002']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'qidong.fenhao.me_003' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi003']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'qidong.fenhao.me_004' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi004']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'qidong.fenhao.me_005' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi007']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'up.fenhao.me_qj' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi005']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'qidong.fenhao.me_006' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi008']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'qidong.fenhao.me_007' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi009']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'qidong.fenhao.me_008' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi010']}) and {$array['where']} GROUP BY ymd

                ) as a GROUP BY a.ymd,a.qid order by {$array['orderby']}";
        if($limit){
            $sql .= " limit {$array['limit']}";
        }
        return $this->query($sql);
    }

    public function sem_select_new_360($array,$limit = ''){
        $sql = "SELECT a.ymd,a.trl,a.zxl,a.djl,a.qid,sum(a.`install`) as `install`,sum(a.`online`) as `online`,sum(a.active) as active ,sum(a.haiyuan) as haiyuan,sum(a.install_uninstall) as install_uninstall,sum(a.uninstall1) as uninstall1,sum(a.uninstall7) as uninstall7,sum(a.online1) as online1,sum(a.online7) as online7,sum(a.online15) as online15,sum(a.online30) as online30 FROM (
                    SELECT ymd,trl,zxl,djl,'www.udashi.ren_100' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem360_100']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.ren_101' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem360_101']}) and {$array['where']} GROUP BY ymd
                     UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.ren_102' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem360_102']}) and {$array['where']} GROUP BY ymd
                     UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.ren_103' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem360_103']}) and {$array['where']} GROUP BY ymd
                     UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.ren_104' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem360_104']}) and {$array['where']} GROUP BY ymd
                     UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.ren_105' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem360_105']}) and {$array['where']} GROUP BY ymd
                     UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.ren' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem360']}) and {$array['where']} GROUP BY ymd
                     UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.ren_106' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem360_106']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.ren_107' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem360_107']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.ren_108' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem360_108']}) and {$array['where']} GROUP BY ymd


                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.ren_100' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi360_100']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.ren_101' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi360_101']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.ren_102' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi360_102']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.ren_103' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi360_103']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.ren_104' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi360_104']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.ren_105' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi360_105']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.ren' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi360']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.ren_106' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi360_106']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.ren_107' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi360_107']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.ren_108' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi360_108']}) and {$array['where']} GROUP BY ymd

                ) as a GROUP BY a.ymd,a.qid order by {$array['orderby']}";
        if($limit){
            $sql .= " limit {$array['limit']}";
        }
        return $this->query($sql);
    }

    public function sem_select_new_sogou($array,$limit = ''){
        $sql = "SELECT a.ymd,a.trl,a.zxl,a.djl,a.qid,sum(a.`install`) as `install`,sum(a.`online`) as `online`,sum(a.active) as active ,sum(a.haiyuan) as haiyuan,sum(a.install_uninstall) as install_uninstall,sum(a.uninstall1) as uninstall1,sum(a.uninstall7) as uninstall7,sum(a.online1) as online1,sum(a.online7) as online7,sum(a.online15) as online15,sum(a.online30) as online30 FROM (
                    SELECT ymd,trl,zxl,djl,'www.udashi.pro_200' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30 FROM stat_channel_data  where qid in ({$array['semsogou_200']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.pro_201' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data  where qid in ({$array['semsogou_201']}) and {$array['where']} GROUP BY ymd
                     UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.pro_202' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data  where qid in ({$array['semsogou_202']}) and {$array['where']} GROUP BY ymd
                     UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.pro_203' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data  where qid in ({$array['semsogou_203']}) and {$array['where']} GROUP BY ymd
                     UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.pro_204' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data  where qid in ({$array['semsogou_204']}) and {$array['where']} GROUP BY ymd
                     UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.pro_205' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data  where qid in ({$array['semsogou_205']}) and {$array['where']} GROUP BY ymd
                     UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.pro' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data  where qid in ({$array['semsogou']}) and {$array['where']} GROUP BY ymd
                     UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.pro_206' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data  where qid in ({$array['semsogou_206']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.pro_207' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data  where qid in ({$array['semsogou_207']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.pro_208' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data  where qid in ({$array['semsogou_208']}) and {$array['where']} GROUP BY ymd


                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.pro_200' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefisogou_200']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.pro_201' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefisogou_201']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.pro_202' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefisogou_202']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.pro_203' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefisogou_203']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.pro_204' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefisogou_204']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.pro_205' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefisogou_205']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.pro' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefisogou']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.pro_206' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefisogou_206']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.pro_207' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefisogou_207']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'www.udashi.pro_208' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefisogou_208']}) and {$array['where']} GROUP BY ymd

                ) as a GROUP BY a.ymd,a.qid order by {$array['orderby']}";
        if($limit){
            $sql .= " limit {$array['limit']}";
        }
        return $this->query($sql);
    }

    public function sem_select_new_360_news($array,$limit = ''){
        $sql = "SELECT a.ymd,a.trl,a.zxl,a.djl,a.qid,sum(a.`install`) as `install`,sum(a.`online`) as `online`,sum(a.active) as active ,sum(a.haiyuan) as haiyuan,sum(a.install_uninstall) as install_uninstall,sum(a.uninstall1) as uninstall1,sum(a.uninstall7) as uninstall7,sum(a.online1) as online1,sum(a.online7) as online7,sum(a.online15) as online15,sum(a.online30) as online30 FROM (
                    SELECT ymd,trl,zxl,djl,'uds.liaolewang.com_100' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem360_100']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'uds.liaolewang.com_101' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem360_101']}) and {$array['where']} GROUP BY ymd
                     UNION ALL
                    SELECT ymd,trl,zxl,djl,'uds.liaolewang.com_102' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem360_102']}) and {$array['where']} GROUP BY ymd
                     UNION ALL
                    SELECT ymd,trl,zxl,djl,'uds.liaolewang.com_103' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem360_103']}) and {$array['where']} GROUP BY ymd
                     UNION ALL
                    SELECT ymd,trl,zxl,djl,'uds.liaolewang.com_104' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem360_104']}) and {$array['where']} GROUP BY ymd
                     UNION ALL
                    SELECT ymd,trl,zxl,djl,'uds.liaolewang.com_105' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem360_105']}) and {$array['where']} GROUP BY ymd
                     UNION ALL
                    SELECT ymd,trl,zxl,djl,'uds.liaolewang.com' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem360']}) and {$array['where']} GROUP BY ymd
                     UNION ALL
                    SELECT ymd,trl,zxl,djl,'uds.liaolewang.com_106' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem360_106']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'uds.liaolewang.com_107' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem360_107']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'uds.liaolewang.com_108' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data where qid in ({$array['sem360_108']}) and {$array['where']} GROUP BY ymd


                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'uds.liaolewang.com_100' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi360_100']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'uds.liaolewang.com_101' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi360_101']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'uds.liaolewang.com_102' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi360_102']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'uds.liaolewang.com_103' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi360_103']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'uds.liaolewang.com_104' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi360_104']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'uds.liaolewang.com_105' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi360_105']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'uds.liaolewang.com' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi360']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'uds.liaolewang.com_106' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi360_106']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'uds.liaolewang.com_107' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi360_107']}) and {$array['where']} GROUP BY ymd
                    UNION ALL
                    SELECT ymd,trl,zxl,djl,'uds.liaolewang.com_108' as qid,sum(`install`) as `install`,sum(`online`) as `online`,sum(`active`) as `active`,sum(`haiyuan`) as `haiyuan`,sum(install_uninstall) as install_uninstall ,sum(uninstall1) as uninstall1,sum(uninstall7) as uninstall7,sum(online1) as online1,sum(online7) as online7,sum(online15) as online15,sum(online30) as online30  FROM stat_channel_data_uefi where qid in ({$array['semuefi360_108']}) and {$array['where']} GROUP BY ymd

                ) as a GROUP BY a.ymd,a.qid order by {$array['orderby']}";
        if($limit){
            $sql .= " limit {$array['limit']}";
        }
        return $this->query($sql);
    }
    /**
     * 获取信息
     * @return array
     */
    public function get_all($where=true,$field='*'){
        $sql = "SELECT {$field} FROM `{$this->_realTableName}` WHERE {$where}";
        return $this->query($sql);
    }
    /**
     * 获取总数
     * @param string $where
     * @return array
     */
    public function get_count($where){
        $sql = "SELECT count(*) as count FROM `{$this->_realTableName}` WHERE {$where}";
        $result = $this->query($sql);
        return $result[0]['count'];
    }

    public function change_qid($ymd){
        $sql = "UPDATE `{$this->_realTableName}` as a LEFT JOIN stat_active_channel_allot as b on a.qid=b.qid set a.qidname=b.qidname where a.ymd={$ymd} and b.qid is not NULL;";
        return $this->query($sql);
    }

    public function set_qid($ymd){
        $sql = "UPDATE `{$this->_realTableName}` set qidname=qid where ymd={$ymd} and qidname is NULL;";
        return $this->query($sql);
    }

}
