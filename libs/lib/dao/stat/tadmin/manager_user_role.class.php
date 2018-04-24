<?php
/**
 * 统计-渠道主管-我的业绩
 * 渠道主管各项业绩
 */
namespace Dao\Stat\Tadmin;
use \Dao\Stat\Stat;
use Union\User\Account;

class Manager_user_role extends Stat {
    /**
     * @return Manager_user_role
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table() {
        $this->query( "drop table if exists `tadmin_manager_user_role`;");
        $sql = "
            CREATE TABLE `tadmin_manager_user_role` (
              `uid` int(11) NOT NULL DEFAULT '0' COMMENT '市场经理id',
              `user_name` varchar(50) DEFAULT NULL COMMENT '市场经理账号',
              `puid` int(11) DEFAULT NULL COMMENT '市场经理上级id',
              `pname` varchar(50) DEFAULT NULL COMMENT '市场经理上级用户名',
              `ppuid` int(11) DEFAULT NULL COMMENT '市场 经理上上级id',
              `ppname` varchar(50) DEFAULT NULL COMMENT '市场 经理上上级用户名',
              `director_uid` int(11) DEFAULT NULL COMMENT '渠道主管id',
              `director_name` varchar(50) DEFAULT NULL COMMENT '渠道主管名',
              `areaid` int(11) DEFAULT NULL COMMENT '区域ID',
              `level` tinyint(1) DEFAULT '1' COMMENT '等级',
              `reg_dateline` int(11) DEFAULT NULL COMMENT '注册时间',
               KEY `uid_level` (`uid`,`director_name`,`level`) USING BTREE
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
        ";
        $this->query( $sql );
        return $this->affected_rows();
    }

    public function sync_ymd($ymd_start, $ymd_end ) {
        
    }

    public function sync_all() {
        $this->_sync();
    }
    
    protected function _sync( $where = '') {
        $sql = "
            INSERT INTO `tadmin_manager_user_role` (uid, user_name, puid, pname, ppuid, ppname, director_uid, director_name, areaid, level, reg_dateline)
            SELECT
                r.userid AS uid,
                r.username AS user_name,
                r.pid AS puid,
                r.pname,
                '' AS ppuid,
                '' AS ppname,
                r.uid AS director_uid,
                r.uname AS director_name,
                r.areaid,
                1 AS level,
                r.reg_dateline
            FROM
                `channel_7654`.`ch_qd7654_user_role_new` r
            UNION
            SELECT
                l.nextuserId AS uid,
                l.nextname AS user_name,
                r.userid AS puid,
                r.username AS pname,
                r.pid AS ppuid,
                r.pname AS ppname,
                r.uid AS director_uid,
                r.uname AS director_name,
                r.areaid,
                2 AS level,
                l.next_reg_dateline AS reg_dateline
            FROM 
                `channel_7654`.`ch_qd7654_user_role_new` r
            INNER JOIN
                `channel_7654`.`ch_qd7654_user_role_lv2_new` l ON r.userid = l.userId
        ";
        return $this->exec( $sql );
    }
    
    /**
     * 登录技术员数
     * @param string $director_name
     * @param integer $strat_ymd
     * @param integer $end_ymd
     * @param integer $level
     * @return boolean|multitype:NULL \Dao\mixed
     */
    public function get_login_list($director_name,$strat_ymd,$end_ymd,$level = "")
    {
        if(!$director_name || !$strat_ymd || !$end_ymd)
        {
            return false;
        }
        
        $where = "WHERE u.director_name = '{$director_name}' AND l.ymd BETWEEN {$strat_ymd} AND {$end_ymd}";
        if(in_array($level,array(1,2)))
        {
           $where .= " AND u.level = {$level}"; 
        }
        
        //市场经理账号登录列表（时间段内）
        $sql = "SELECT COUNT(DISTINCT u.uid) AS num,l.ymd as dateline FROM `tadmin_manager_user_role` AS u 
                INNER JOIN `union`.`login_log` AS l ON u.uid = l.uid {$where} GROUP BY l.ymd ORDER BY l.ymd DESC";
        $query_result = $this->query($sql);
        
        //登录总数(去重)
        $count_sql = "SELECT COUNT(DISTINCT u.uid) AS num FROM `tadmin_manager_user_role` AS u INNER JOIN `union`.`login_log` AS l ON u.uid = l.uid {$where}";
        $query_count = $this->query($count_sql);
        
        return [
            $query_count[0]['num'],$query_result
        ];
    }
    
    /**
     * 新增注册技术员数
     * @param string $director_name
     * @param integer $strat_ymd
     * @param integer $end_ymd
     * @param integer $level
     * @return boolean|multitype:NULL \Dao\mixed
     */
    public function get_register_list($director_name,$strat_ymd,$end_ymd,$level = "")
    {
        if(!$director_name || !$strat_ymd || !$end_ymd)
        {
            return false;
        }
        
        $where = "WHERE director_name = '{$director_name}' AND reg_dateline BETWEEN {$strat_ymd} AND {$end_ymd}";
        if(in_array($level,array(1,2)))
        {
            $where .= " AND level = {$level}";
        }
        
        $sql = "SELECT COUNT(DISTINCT uid) AS num,date_format(from_unixtime(reg_dateline),'%Y%m%d') AS dateline 
                FROM `tadmin_manager_user_role` {$where} GROUP BY dateline ORDER BY dateline DESC";
        $query_result = $this->query($sql);

        $count_sql = "SELECT COUNT(DISTINCT uid) AS num FROM `tadmin_manager_user_role` {$where}";
        $query_count = $this->query($count_sql);
        
        return [
            $query_count[0]['num'],$query_result
        ];
    }
    
    /**
     * 有业绩技术员列表
     * @param integer $director_uid 渠道主管uid
     * @param integer $start_ymd    开始日期
     * @param integer $end_ymd      结束日期
     * @return array
     */
    public function get_performance_list($director_name,$start_ymd,$end_ymd,$level="") 
    {
        if(!$director_name || !$start_ymd || !$end_ymd)
        {
            return false;
        }
        $where = "WHERE m.promotion_type = 2 AND m.ymd BETWEEN {$start_ymd} AND {$end_ymd} AND u.director_name = '{$director_name}'";
        if(in_array($level,array(1,2)))
        {
            $where .= " AND level = {$level}";
        }
    
        //有业绩技术员数（按照时间分组）
        $sql = "SELECT COUNT(DISTINCT m.uid) AS num,m.ymd FROM `tadmin_manager_user_role` AS u
                INNER JOIN `manager_performance` AS m ON u.uid = m.uid {$where} GROUP BY m.ymd ORDER BY m.ymd DESC";
        $query_list = $this->query($sql);

        //有业绩技术员数（按用户分组）
        $count_sql = "SELECT COUNT(DISTINCT m.uid) AS num FROM `tadmin_manager_user_role` AS u
                      INNER JOIN `manager_performance` AS m ON u.uid = m.uid {$where}";
        $query_count = $this->query($count_sql);

        return [$query_count['num'],$query_list];
    }
}