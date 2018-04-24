<?php
/**
 * 统计-市场经理-推广信息
 */
namespace Dao\Stat\Manager;
use \Dao\Stat\Stat;

class City extends Stat {

    /**
     * @return City
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table() {
        $this->drop();
        $sql = "
            CREATE TABLE `manager_city` (
              `city_id` int(11) NOT NULL DEFAULT '0' COMMENT '城市id',
              `city_name` varchar(40) DEFAULT NULL COMMENT '城市名',
              `province_id` int(11) DEFAULT NULL COMMENT '省id',
              `province_name` varchar(40) DEFAULT NULL COMMENT '省名',
              `director_id` int(11) DEFAULT NULL COMMENT '渠道主管id',
              `director_user_name` varchar(40) DEFAULT NULL COMMENT '渠道主管名称',
              PRIMARY KEY (`city_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='城市信息表';
        ";
        $this->exec( $sql );
        return $this->affected_rows();

    }

    public function sync_ymd() {
        $this->sync_all();
    }

    /**
     * @return int
     */
    public function sync_all() {
        $this->delete_all();
        $sql = "
            insert into  `manager_city` (city_id, city_name, province_id, province_name, director_uid, director_user_name )
            select
                a1.id as city_id,
                a1.`name` as city_name,
                a2.id as province_id,
                a2.`name` as province_name,
				aa.admin_id as director_uid,
				admin.realname as director_user_name
            from
                channel_7654.area a1
                LEFT JOIN channel_7654.area a2 on a1.parentid = a2.id
                left join channel_7654.`area_admin` aa on a1.id= aa.area_id
				left join channel_7654.`admin` admin on aa.admin_id=admin.userid
            where a1.parentid > 0;
        ";
        $this->exec( $sql );
        return $this->affected_rows();
    }
}