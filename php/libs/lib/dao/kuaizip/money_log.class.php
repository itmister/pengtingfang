<?php
/**
 * 用户资金流水
 */
namespace Dao\Kuaizip;
use \Util\Datetime;

class Money_log extends Kuaizip {
    /**
     * @return \Dao\Kuaizip\Money_log
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table() {
        $sql = "
CREATE TABLE `money_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uid` int(10) unsigned NOT NULL COMMENT '用户uid',
  `user_name` varchar(40) DEFAULT NULL COMMENT '用户名',
  `money` int(11) NOT NULL COMMENT '钱，单位分',
  `type` tinyint(3) unsigned NOT NULL COMMENT '类型,1:推广业绩,101:提现,201:提现驳回',
  `from_id` int(11) NOT NULL COMMENT '来源id',
  `datetime` datetime NOT NULL COMMENT '记录产生时间',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态,1:正常，2:未可用',
  `ymd` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '业绩发放年月日',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `ymd` (`ymd`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='帐户资金流水';
        ";
    }

    /**
     * 提现申请
     */
    public function withdraw_apply( $uid, $user_name, $money, $from_id = null ) {
        $from_id = intval( $from_id );
        $data = [
            'type' => 101,
            'uid' => $uid,
            'user_name' => $user_name,
            'money' => $money,
            'from_id' => $from_id,
            'datetime' => Datetime::now(),
            'ymd'   => Datetime::ymd_now(),
            'status' => 1,
        ];
        return $this->add( $data );
    }

    /**
     * 提现驳回
     */
    public function withdraw_deny(  $uid, $user_name, $money, $from_id ) {

        $from_id = intval( $from_id );
        $data = [
            'type' => 201,
            'uid' => $uid,
            'user_name' => $user_name,
            'money' => $money,
            'from_id' => $from_id,
            'datetime' => Datetime::now(),
            'ymd'   => Datetime::ymd_now(),
            'status' => 1
        ];
        return $this->add( $data );
    }

    /**
     * 业绩增加
     */
    public function performance_add(  $uid, $user_name, $money, $from_id, $ymd ) {
        $from_id = intval( $from_id );
        $data = [
            'type' => 1,
            'uid' => $uid,
            'user_name' => $user_name,
            'money' => $money,
            'from_id' => $from_id,
            'datetime' => Datetime::now(),
            'ymd'   => $ymd,
            'status' => 2
        ];
        return $this->add( $data );
    }


    /**
     * 取未可提现业绩列表
     * @param $ymd_end
     */
    public function get_performance_unavailable_list( $ymd_end ) {
        $table_name = $this->_get_table_name();
        $sql = "
            select
                *
            from
                {$table_name}
            WHERE
                ymd<={$ymd_end}
                and `status`=2
          ";
        return $this->query( $sql );
    }
}