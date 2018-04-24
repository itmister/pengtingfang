<?php
/**
 * 区域管理与区域业绩明细
 */

namespace Dao\Stat\Tadmin;
use \Dao\Stat\Stat;

class Manager_area_performance extends Stat {

    protected static $_instance = null;

    /**
     * @return Manager_area_performance
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    public function create_table() {
        $this->drop();
        $sql = "
            CREATE TABLE `tadmin_manager_area_performance` (
              `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
              `city_id` int(11) NOT NULL COMMENT '城市id;F',
              `province_id` int(11) DEFAULT NULL COMMENT '省id;F',
              `channel_master_name` varchar(40) DEFAULT NULL COMMENT '渠道主管帐号名;F',
              `channel_master_id` int(11) DEFAULT NULL COMMENT '渠道主管id;F',
              `channel_master_real_name` varchar(40) DEFAULT NULL COMMENT '渠道主管姓名;F',
              `ym` int(11) NOT NULL COMMENT '业绩产生时间，6位年月,如201507',
              `total_manager` int(11) DEFAULT NULL COMMENT '市场经理总人数，截止到本月',
              `total_trainee_manager` int(11) DEFAULT NULL COMMENT '见习市场经理总人数，截止到本月',
              `total_technician` int(11) DEFAULT NULL COMMENT '当月有业绩的技术员总人数',
              `total_soft` int(11) DEFAULT NULL COMMENT '软件安装总量，截止到本月',
              `ip_count_total` int(11) DEFAULT NULL COMMENT '有效量',
              `dateline` int(11) DEFAULT NULL COMMENT '创建时间',
              PRIMARY KEY (`id`),
              UNIQUE KEY `ym` (`city_id`,`ym`) USING BTREE
            ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='区域管理与区域业绩明细';
        ";
        $this->exec( $sql );
        return $this->affected_rows();
    }
}
