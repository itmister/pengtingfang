<?php
/**
 * 统计-市场经理-下属技术员
 */
namespace Dao\Stat\Manager;
use \Dao\Stat\Stat;

class Technician extends Stat {
    /**
     * @return Technician
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table() {
        $this->query("drop table if exists `manager_technician`");
        $sql = "
            create table `manager_technician` (
                `uid` int(11) not null default '0' comment '技术员uid',
                `manager_uid` int(11) not null DEFAULT '0' comment '市场经理uid',
                `director_uid` int(11) not null default '0' comment '渠道主管uid',
                `bind_ymd` int(11) not null default '0' comment '绑定市场经理年月日',
                `bind_ym` int(11) not null default '0' comment '绑定市场经理年月',
                `reg_ymd` int(11) not null DEFAULT 0 comment '注册年月日',
                `reg_ym` int(11) not null DEFAULT  0 comment '注册年月',
                `info_is_complete` int(11) not null DEFAULT 0 comment '资料是否完整',
                primary key(`uid`),
                key `director_uid` (`director_uid`),
                key `manager_uid` (`manager_uid`),
                key `bind_ymd` (`bind_ymd`),
                key `bind_ym` (`bind_ym`)
            )ENGINE=InnoDB DEFAULT CHARSET=utf8 comment '市场经理-下属技术员';
        ";
        $this->query( $sql );
        return $this->affected_rows();
    }

    public function sync_ymd( $ymd_start, $ymd_end ) {
        $this->sync_all();
    }

    public function sync_all() {
        $this->query("truncate TABLE `manager_technician`;");
        $sql = "
            insert into `manager_technician` ( uid,manager_uid, director_uid, bind_ymd, bind_ym, reg_ymd, reg_ym, info_is_complete )
            select
                bu.uid as uid,
                bu.puid as manager_uid,
                m.director_uid as director_uid,
                bu.bind_ymd as bind_ymd,
                floor( (bu.bind_ymd) / 100 ) as bind_ym,
                reg_ymd,
                reg_ym,
                bu.info_is_complete as info_is_complete
            from
                `stat`.`manager_manager` m
                inner JOIN `stat`.`base_user` bu on m.manager_uid = bu.puid and bu.bind_ymd>= m.ymd_start and bu.bind_ymd<=m.ymd_end;
        ";
        $this->exec( $sql );
        $this->sync_manager_technician_count();
    }

    public function sync_manager_technician_count() {
        $sql = "
            insert into `manager_manager` (manager_uid, technician)
            select
                manager_uid,
                count(*) as technician_count
            from
                manager_technician
            GROUP BY
                manager_uid
            on duplicate key update technician = values( technician )
        ";
        return $this->exec( $sql );
    }

    /**
     * 取市场经理下属技术员数量数组
     * @return
        [
            [
                manager_uid : 市场经理uid,
                technician_count : 下属技术员数量
            ]
        ]
     */
    public function get_technician_count_list() {
        $sql = "
            select
                manager_uid,
                count(*) as technician_count
            from
                manager_technician
            GROUP BY
                manager_uid;
        ";
        return $this->query( $sql );
    }
}