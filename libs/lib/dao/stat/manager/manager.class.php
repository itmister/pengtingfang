<?php
/**
 * 统计-市场经理
 */
namespace Dao\Stat\Manager;
use \Dao\Stat\Stat;

class Manager extends Stat {
    /**
     * @return Manager
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table() {
        $this->query( "drop table if exists `manager_manager`;");
        $sql = "
            create table `manager_manager` (
                `manager_uid` int (11) not null default '0' comment '市场经理uid',
                `director_uid` int(11) not null default '0' comment '渠道主管uid',
                `type` int(11) not null default '0' comment '市场经理状态，2:见习, 3:作业中, 4:正式',
                `ymd_start` int(11) not null default '0' comment '有效开始时间，影响下属技术员与下属技术员业线',
                `ymd_end` int(11) not null default '0' comment '有效结束时间,影响下属技术员与下属技术员业线',
                `city_id` int(11) not null default '0' comment '所在市场id',
                `bind_ymd` int(11) not null default '0' comment '绑定城市年月日',
                `bind_ym` int(11) not null default '0' comment '绑定城市年月',
                primary key(`manager_uid`),
                key `director_uid` (`director_uid`),
                key `ymd_start` (`ymd_start`),
                key `ymd_end` (`ymd_end`),
                key `bind_ymd` (`bind_ymd`),
                key `bind_ym` (`bind_ym`)
            )ENGINE=InnoDB DEFAULT CHARSET=utf8 comment '市场经理列表';
        ";
        $this->query( $sql );
        return $this->affected_rows();
    }

    public function sync_ymd( $ymd_start, $ymd_end ) {
        $this->sync_all();//@todo 市场经理按日期更新待处理
    }

    /**
     * 市场经理uid
     * @param $uid
     */
    public function sync_uid($uid) {
        $this->sync_all( $uid );
    }

    public function sync_all( $uid = null ) {
        $uid        = intval( $uid );
        $where      = !empty($uid) ? " WHERE bu.uid={$uid} " : '';
        $ymd_now    = date("Ymd");
        $sql = "
            #set @ymd_now=CONVERT(FROM_UNIXTIME(  unix_timestamp(NOW()), '%Y%m%d'), SIGNED);
            replace into `manager_manager` (manager_uid, type, city_id, director_uid, ymd_start, ymd_end, bind_ymd, bind_ym )
            #EXPLAIN
            select
                bu.uid as manager_uid,
                bu.type as type,
                um.areaid as city_id,
                aa.admin_id as director_uid,
                (case when mw.ymd_start is null then FROM_UNIXTIME(um.choose_area_dateline, '%Y%m%d') else mw.ymd_start end ) as ymd_start,#有作业取作业开始时间取作业开始时间否则取绑定城市时间
                (case when mw.ymd_end is null or bu.type=4 then {$ymd_now} else mw.ymd_end end ) as ymd_end, #正式或没有作业结束时间取当前时间否则取结束时间
								FROM_UNIXTIME(um.choose_area_dateline, '%Y%m%d') as bind_ymd,
								FROM_UNIXTIME(um.choose_area_dateline, '%Y%m%d') as bind_ym
            from
                `stat`.`base_user` bu
                inner JOIN `channel_7654`.user_marketer um on bu.uid=um.userid and bu.type >= 2 and bu.type <= 4 and um.areaid > 0
                LEFT JOIN `channel_7654`.area_admin aa on um.areaid = aa.area_id
                LEFT JOIN `manager_working` mw on um.userid= mw.manager_uid
            {$where}
            #select * from `manager_manager`;
        ";
        $this->query( $sql );
        return $this->affected_rows();
    }

    /**
     * 取所有市场经理列表
     */
    public function get_list() {
        $sql = "
            select
                mm.manager_uid as manager_uid,
                ct.province_name,
                ct.city_name,
                bu.user_name,
                bu.real_name,
                bu.remark,
                bu.reg_ymd
            from
                manager_manager mm
                left JOIN base_user bu on mm.manager_uid = bu.uid
                left JOIN manager_city ct on mm.city_id=ct.city_id
        ";
        $result = $this->query( $sql );
        return $result;
    }

    /**
     * 市场经理邀请数量列表
     */
    public function get_manager_invite_list() {
        $sql = "
            select
                manager_uid,
              count(*) as technician_invite_count
            from
                manager_technician
            GROUP BY
                manager_uid
        ";
        return $this->query( $sql );
    }


    /**
     * 市场经理备注、是否流失等信息
     * @param $manager_uids
     * @param array $arr_fields
     * @return array
     */
    public function get_ext_info( $manager_uids, $arr_fields = ['remark', 'is_out']) {
        if (!is_array( $manager_uids) ) $manager_uids = [$manager_uids];
        $arr    = [];
        foreach ( $manager_uids as $uid ) $arr[] = intval( $uid );
        $uids   = implode(',', $arr);
        $result = [];

        if ( in_array('remark', $arr_fields) ) {
            $sql = "
                select
                    uid,
                    remark
                from
                    `union`.`user_info_ext`
                WHERE
                    uid in ({$uids})
            ";
            foreach ( $this->yield_result( $sql ) as $row ) $result[$row['uid']]['remark'] = trim( $row['remark']);
        }

        if ( in_array('is_out', $arr_fields) ) {
            $sql = "
                select
                    userid,
                    is_out
                from
                    `channel_7654`.`user_marketer`
                WHERE
                    userid in ({$uids})
            ";
            foreach ( $this->yield_result( $sql ) as $row ) $result[$row['userid']]['is_out'] = $row['is_out'];
        }
        return $result;
    }
}