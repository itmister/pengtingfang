<?php
/**
 * 统计-市场经理-下属业绩排名
 */
namespace Dao\Stat\Tadmin;
use \Dao\Stat\Stat;

class Manager_information extends Stat {
    /**
     * @return Manager_information
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table() {
        $this->drop();
        $sql = "
        create table tadmin_manager_information (
            manager_uid int(11) not null default 0 comment '市场经理uid',
            director_uid int(11) not null default 0 comment '渠道主管uid',
            bind_ymd int(11) not null default 0 comment '绑定城市年月日',
            sex int(11) not null default 0 comment '性别',
            `type` int(11) not null default 0 comment '类型, 2：见习市场经，3：见习作业中,4:正式',
            working_status int(11) not null DEFAULT 0 comment '作业状态,作业状态 1:未开始,2:进行中,3:已完成，4:已失败,5:手动完成',
            technician int(11) not null default 0 comment '下属技术员数理',
            is_out int(11) not null default 0 comment '是否流失',
            register_time datetime not null comment '注册时间',
            city_id int(11) not null default 0 comment '城市id',
            city_name VARCHAR(40) not null default '' comment '城市名',
            manager_user_name VARCHAR(40) not null default '' comment '市场经理帐号名',
            phone VARCHAR(40) not null default '' comment '电话号码',
            qq VARCHAR(40) not null default '' comment 'qq',
            real_name VARCHAR(40) not null default '' comment '真实姓名',
            director_user_name VARCHAR(40) not null default '' comment '城市名',
            reg_ip VARCHAR(40) not null default '' comment '注册ip',
            address VARCHAR(255) not null default '' comment '地址',
            remark VARCHAR(255) not null default '' comment '备注',
            PRIMARY key (`manager_uid`),
            KEY (`bind_ymd`,`director_uid`,`type`,`is_out`),
            key (`manager_user_name`, `phone`)
        ) ENGINE=INNODB default charset utf8 comment '市场经理-详细资料';
        ";
        $this->exec( $sql );
    }

    /**
     * 市场经理uid
     * @param $uid
     */
    public function sync_uid($uid) {
    }

    public function sync_ymd($ymd_start, $ymd_end ) {
        $this->sync_all();
    }

    public function sync_ym( $ym = 0 ) {
        $this->sync_all();
    }

    public function sync_all() {
        $sql = "
            INSERT into `tadmin_manager_information`(manager_uid,bind_ymd,sex,`type`, working_status, technician,register_time,
            city_id,city_name, manager_user_name, phone,qq,real_name,director_user_name,reg_ip,address
            ,remark, is_out)
            SELECT
                mm.manager_uid,
                bu.bind_ymd,
                bu.sex,
                mm.type,
                (
                case
                  when mw.working_status = 0 then 2#进行中
                  when mw.working_status = 1 then 3#完成
                  when mw.working_status = 2 then 4#失败
                  when mw.working_status =3 then 5#手动完成
                  when mw.working_status is null then 1 #未开始
                 end ) as working_status,
                mm.technician,

                FROM_UNIXTIME(bu.reg_dateline,'%Y-%m-%d %H:%i:%s') as register_time,
                mm.city_id,
                mc.city_name,
                bu.user_name,
                bu.phone,
                bu.qq,
                bu.real_name,
                mc.director_user_name,
                bu.reg_ip,
                bu.address,
                bu.remark,
                um.is_out
            FROM
                manager_manager mm
                LEFT JOIN base_user bu on mm.manager_uid = bu.uid
                LEFT  JOIN manager_working mw on mm.manager_uid=mw.manager_uid
                LEFT JOIN manager_city mc on mm.city_id = mc.city_id
                left JOIN `channel_7654`.`user_marketer` um on mm.manager_uid=um.userid
            on DUPLICATE key update
                type=values(type),working_status=values(working_status),technician=values(technician),city_id=values(city_id),
								city_name=values(city_name),phone=values(phone), qq=values(qq)
                , real_name=values(real_name), director_user_name=values(director_user_name),address=values(address)
        ";
        return $this->exec( $sql );
    }

    public function get_list() {
        $table_name = $this->_get_table_name();

        $sql = "
            select
                *
            from
              {$table_name}
            limit 10
        ";
        return $this->query( $sql);

    }

    public function page_get($sql, $sql_total, $start = 0, $num = 10) {
        $table_name = $this->_get_table_name();
        $where = '';
        $sql_total = "
            select count(*) from {$table_name} {$where}
        ";
        $sql_list = "
            select
              *
            from
              {$table_name}
            {$where}
            ORDER BY
              bind_ymd desc
        ";
        return parent::page_get($sql_list, $sql_total);
    }

    public function set_is_out( $manager_uid, $is_out ) {
        $manager_uid = intval( $manager_uid );
        $is_out = intval( $is_out );
        return $this->update("manager_uid={$manager_uid}", ['is_out' => $is_out]);
    }

    public function set_remark( $manager_uid, $remark) {
        $manager_uid    = intval( $manager_uid );
        $remark         = trim( $remark );
        return $this->update("manager_uid={$manager_uid}", ['remark' => $remark]);
    }
}