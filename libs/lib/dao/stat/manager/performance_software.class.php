<?php
/**
 * 统计-市场经理-软件(包括导航)业绩
 */
namespace Dao\Stat\Manager;
use \Dao\Stat\Stat;

class Performance_software extends Stat {

    /**
     * @return Performance_software
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table() {
        $this->query( "DROP table if EXISTS `manager_performance_software`;");
        $sql = "
            CREATE TABLE `manager_performance_software` (
              `manager_uid` int(11) NOT NULL DEFAULT '0' COMMENT '市场经理uid',
              `director_uid` int(11) NOT NULL DEFAULT '0' COMMENT '渠道主管uid',
              `soft_id` varchar(40) NOT NULL DEFAULT '' COMMENT '软件标识',
              `credit` int(11) NOT NULL DEFAULT '0' COMMENT '积分',
              `ip_count` int(11) NOT NULL DEFAULT '0' COMMENT '有效量',
              `ip_count_org` int(11) NOT NULL DEFAULT '0' COMMENT '厂商返量',
              PRIMARY KEY (`manager_uid`,`soft_id`),
              KEY `director_uid` (`director_uid`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='市场经理-软件业绩汇总';
        ";
        $this->query( $sql );
        return $this->affected_rows();
//        return $this->affected_rows();
    }

    public function sync_all() {
        $this->query("truncate table manager_performance_software;");
        $sql = "
            INSERT into manager_performance_software (manager_uid,director_uid,soft_id,credit,ip_count,ip_count_org )
            select
                manager_uid,
                director_uid,
                soft_id,
                sum(credit),
                sum(ip_count),
                sum(ip_count_org)
            FROM
                manager_performance_ym_software
            GROUP BY
                manager_uid,soft_id
        ";
        $this->query( $sql );
        return $this->affected_rows();
    }


    /**
     * 取指定市场经理业绩
     * @param $manager_uid
     * @return mixed
     */
    public function get_list( $manager_uid ) {
        $table_name = $this->_get_table_name();
        $sql = "
            select
                manager_uid,
                soft_id,
                ip_count
            from
                {$table_name}
            where
                ym='{$manager_uid}'
        ";
        return $this->query( $sql );
    }
}