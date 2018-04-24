<?php
/**
 * 7654厂商原始返回业绩表
 */
namespace Dao\Stat\Union;
use \Dao\Stat\Stat;
use \Dao\Orm;

class Performance_original extends Stat {
    use Orm;

    /**
     * @return Performance_original
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create() {
        $sql = <<<eot
CREATE TABLE `union_performance_original` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `tn` varchar(255) DEFAULT NULL COMMENT '原始渠道号',
  `ip_count` int(10) unsigned DEFAULT NULL COMMENT '有效量',
  `is_other` tinyint(4) DEFAULT '0' COMMENT '是否已经分配到其它平台(不包括7654本身)，0:否，1:是',
  `platform` mediumint(9) DEFAULT '1' COMMENT '平台,1:未知,2:7654,3:51lm,4:线上收量平台',
  `ymd` int(10) unsigned DEFAULT NULL COMMENT '业绩年月日',
  `software` varchar(255) DEFAULT NULL COMMENT '软件标识',
  `dateline` int(10) unsigned DEFAULT NULL COMMENT '录入时间',
  PRIMARY KEY (`id`),
  KEY `software` (`software`,`ymd`,`platform`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='7654厂商原始返回业绩表';
eot;
        return $this->exec( $sql );
    }

    /**
     * 取指定软件时间范围内原始发放量
     * @param $software
     * @param $ymd_start
     * @param $ymd_end
     * @return array
     * [
     *      ymd : ip_count
     * ]
     */
    public function get_performance_original( $software, $ymd_start, $ymd_end ) {
        $sql = <<<eot
select
    ymd,
    sum(ip_count) as ip_count
from
    {$this->_get_table_name()}
where
    software='{$software}' and ymd between {$ymd_start} and {$ymd_end}
    and `status`=1
    and `is_other`=0
group by
    ymd
eot;
        $result = [];
        foreach ( $this->query( $sql ) as $row ) $result[$row['ymd']] = $row['ip_count'];
        return $result;
    }
}