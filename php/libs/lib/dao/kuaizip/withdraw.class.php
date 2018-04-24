<?php
namespace Dao\Kuaizip;
class Withdraw extends Kuaizip {
    /**
     * @return \Dao\Kuaizip\Withdraw
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table() {
        $sql = "
CREATE TABLE `withdraw` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uid` int(11) DEFAULT NULL COMMENT '提现用户uid',
  `user_name` varchar(40) DEFAULT NULL COMMENT '提现帐号名',
  `alipay` varchar(40) DEFAULT NULL COMMENT '支付宝帐号',
  `money` int(10) unsigned DEFAULT NULL COMMENT '提现金额，单位：分',
  `datetime` datetime DEFAULT NULL COMMENT '提现时间',
  `ymd` int(10) unsigned DEFAULT NULL COMMENT '提现年月日',
  `status` tinyint(3) unsigned DEFAULT '1' COMMENT '状态 , 1:未处理,2:已支付,3:驳回',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `user_name` (`user_name`),
  KEY `ymd` (`ymd`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='提现记录表';
        ";
    }

    public function page_get( $start = 0, $num = 10, $where = '', $order = 'id desc' ) {
        $table_name = $this->_get_table_name();
        $sql_total  = "select count(*) from {$table_name}";
        $order      = $this->_parse_order( $order );
        $where      = $this->_parse_where( $where );
        $sql_list = "
            select
                *
            from
              {$table_name}
            {$where}
            {$order}
        ";

        return parent::page_get($sql_list, $sql_total,$start, $num);
    }

    public function get_list( $ymd_start, $ymd_end , $status = null ) {
        if ( !empty($status) ) $where = ' AND status=' . $status;
        $table_name = $this->_get_table_name();
        $sql_list = "
            select
                *
            from
              {$table_name}
            WHERE
               ymd BETWEEN  {$ymd_start} and {$ymd_end}
              {$where}
        ";
        return $this->query( $sql_list );
    }
}