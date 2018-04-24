<?php
namespace Dao\Kuaizip;

class User extends Kuaizip {

    public function create_table() {
        $sql = "
CREATE TABLE `user` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户自增id',
  `phone` varchar(15) NOT NULL,
  `user_name` varchar(40) NOT NULL,
  `money` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '可用余额',
  `money_total` int(11) NOT NULL DEFAULT '0' COMMENT '总收入',
  `reg_ip` varchar(15) DEFAULT NULL,
  `login_ip` varchar(15) DEFAULT NULL,
  `reg_datetime` datetime DEFAULT NULL,
  `reg_ymd` int(10) unsigned DEFAULT NULL COMMENT '年注册年月日',
  `login_datetime` datetime DEFAULT NULL,
  `from` varchar(40) NOT NULL DEFAULT '' COMMENT '注册来源',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `user_name` (`user_name`) USING BTREE,
  UNIQUE KEY `phone` (`phone`),
  KEY `reg_ymd` (`reg_ymd`)
) ENGINE=InnoDB AUTO_INCREMENT=2000000 DEFAULT CHARSET=utf8 COMMENT='用户表';
        ";
    }

    /**
     * @return \Dao\Kuaizip\User
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function login( $data ) {
        if ( empty($data['phone']) ) return false;

        if ( $this->is_exist( $data['phone']) ) {
            $where = 'phone=' . $data['phone'];
            return $this->update( $where, [
                'login_ip' => $data['reg_ip'],
                'login_datetime' => date('Y-m-d H:i:s')
            ]);
        }

        $data['user_name'] = $data['phone'];
        $data['reg_datetime'] = date('Y-m-d H:i:s');
        $data['reg_ymd'] = date('Ymd');
        $data['login_datetime'] = date('Y-m-d H:i:s');
        $data['login_ip'] = $data['reg_ip'];
        $sql = "
            insert into `user` (user_name,phone,reg_ip,login_ip,reg_datetime, reg_ymd,login_datetime,`from`) values
            (
            '{$data['user_name']}',
            '{$data['phone']}',
            '{$data['reg_ip']}',
            '{$data['login_ip']}',
            '{$data['reg_datetime']}',
            '{$data['reg_ymd']}',
            '{$data['login_datetime']}',
            '{$data['from']}'
            )
            on duplicate key update login_ip=values(login_ip),login_datetime=values(login_datetime)
        ";
        return $this->exec( $sql );
    }

    public function get_user_info($phone) {
        return $this->get_row( "phone='{$phone}'");
    }


    public function is_exist($phone) {
        return !empty($this->get_user_info($phone));
    }


    /**
     * 检查指定的uid是否有效
     * @param $uid_list
     * @return mixed
     */
    public function uid_available_check( $uid_list ) {
        $uids = $this->_field_to_str($uid_list);
        $table = $this->_get_table_name();
        $sql = "
            select
                uid
            from
              {$table}
            WHERE
              uid in ({$uids})
        ";
        return $this->query( $sql );
    }

    /**
     * @param array $tn_list
     */
    public function user_list_by_tn_list( $tn_list = [], $fields = '*' ) {
        $uids = $this->_field_to_str($tn_list);
        $table = $this->_get_table_name();
        $sql = "
            select
                $fields
            from
              {$table}
            WHERE
              uid in ({$uids})
        ";
        return $this->query( $sql );
    }

    /**
     * 同步用户帐号收入，余额，可取资金
     * @param $uid
     * @return boolean
     */
    public function sync_money( $uid, $money_total = null , $money = null , $money_available = null ) {
        $uid = intval($uid);
        if ( empty($uid) ) return false;
        $arr_update = [];
        if (isset( $money_total)) $arr_update['money_total'] = intval( $money_total);
        if (isset( $money )) $arr_update['money'] = intval( $money);
        if ( isset( $money_available ) )  $arr_update['money_available'] = intval( $money_available);

        if ( empty($arr_update) ) return false;
        return $this->update("uid='{$uid}'", $arr_update);
    }

}