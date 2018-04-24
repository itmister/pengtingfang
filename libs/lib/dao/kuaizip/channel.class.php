<?php
/**
 * 用户资金流水
 */
namespace Dao\Kuaizip;
use \Util\Datetime;

class Channel extends Kuaizip {
    /**
     * @return \Dao\Kuaizip\Channel
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table() {
        $sql = "
CREATE TABLE `channel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `tn` varchar(40) DEFAULT NULL,
  `tn_url` varchar(255) DEFAULT NULL,
  `status` tinyint(3) unsigned DEFAULT NULL COMMENT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tn` (`tn`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='渠道列表';
        ";
    }

    /**
     * 取得所有渠道列表
     */
    public function get_list() {
        $result = [];
        foreach ( $this->select() as $row ) $result[$row['tn']] = $row;
        return $result;
    }
}