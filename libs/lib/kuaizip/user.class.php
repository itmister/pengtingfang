<?php
namespace Kuaizip;

class User {

    /**
     * 提现记录信息
     * @param $uid
     * @return array
     */
    public function info( $uid ) {
        $uid = intval( $uid );
        $info = \Dao\Kuaizip\User::get_instance()->get_row('where uid=' . $uid);
        return $info;
    }
}