<?php
/**
 * 统计-市场经理-市场经理最后作业信息表,只保存最后作业
 */
namespace Dao\Stat\Manager;
use \Dao\Stat\Stat;

class Working extends Stat {

    /**
     * @return Working
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table() {
        $this->query( "drop table if exists `manager_working`;");
        $sql = "
            CREATE TABLE `manager_working`(
              `id` int(11) not null default '0' comment '自增id',
              `manager_uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
              `ymd_start` int(11) not null DEFAULT '0' COMMENT '作业开始年月日',
              `ymd_end` int(11)not null DEFAULT '0' COMMENT '作业结束开始年月日',
              `working_status` int(11) not null DEFAULT  0 COMMENT '作业状态 0 进行中 1完成 2未完成 3手动完成',
              PRIMARY KEY (`manager_uid`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='市场经理最后作业信息表,只保存最后作业';
        ";
        $this->query( $sql );
        return $this->affected_rows();
    }

    public function sync_ymd( $ymd_start, $ymd_end ) {
        return $this->sync_all();
    }

    public function sync_all() {
        //作业状态:0 进行中 1完成 2未完成 3手动完成
        $this->delete_all();
        $sql = "
            replace into `manager_working`( `id`,manager_uid, ymd_start, ymd_end , `working_status`)
            select
                `id`,
                userid as manager_uid,
                max(ymd_start) as ymd_start,
                max(ymd_end) as ymd_end,
                `status`
            from
                `channel_7654`.user_marketer_working
            GROUP BY userid
        ";
        $this->query( $sql );
        return $this->affected_rows();
    }

    public function sync_user($uid) {
        $uid = intval($uid);
        $sql = "
            replace into `manager_working`( `id`,manager_uid, ymd_start, ymd_end , `working_status`)
            select
                `id`,
                userid as manager_uid,
                max(ymd_start) as ymd_start,
                max(ymd_end) as ymd_end,
                `status`
            from
                `channel_7654`.user_marketer_working

            GROUP BY userid
            WHERE userid={$uid}
        ";
        $this->query( $sql );
        return $this->affected_rows();
    }
}
