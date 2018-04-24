<?php
namespace Dao\Union;
use \Dao;
class User_info_ext extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\User_info_ext
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * 设置备注
     * @param int $uid
     * @param string $remark
     * @return boolean
     */
    public function set_remark(  $uid,  $remark ) {
        $this->add_if_not_exist('uid', $uid );
        $uid    = intval( $uid );
        $remark = trim( $remark );
        if ( empty($uid) || empty($remark))  return false;
        $this->update("uid={$uid}", array(
            'remark' => $remark,
        ));
    }
    
    /**
     * 根据备注查询
     * @param string $remark
     * @param string $field
     * @return $field
     */
    public function get_field_by_remark( $where, $field = "*" ) {
    	$user_info = $this->query("SELECT {$field} FROM {$this->_get_table_name()} WHERE {$where}");
    	return $user_info ? $user_info : array();
    }
    
    /**
     * 根据id查询
     * @param type $arr_uid
     * @param string $field
     * @return $field
     */
    public function get_user_info_by_uids( $arr_uid, $field = "*") {
        if (empty($arr_uid)) return array();
        $uids = is_array($arr_uid) ? implode(',', $arr_uid) : $arr_uid;
        $table_name = $this->_get_table_name();
        $sql = "select {$field} from {$table_name} where uid IN ({$uids})";
        $data = $this->query( $sql );
        return $data ? $data : array();
    }


    /**
     * @param $uid
     * @return array
     */
    public function get_ext_info($uid){
        $sql = "SELECT * FROM `{$this->_realTableName}` WHERE uid = {$uid}";
        $ret = $this->query($sql);
        return $ret[0] ? $ret[0] :[];
    }

    /**
     * 取用户weixin_open_id
     * @param $uid
     * @return Dao\mix
     */
    public function get_weixin_open_id($uid) {
        $uid = intval( $uid );
        $weixin_open_id = $this->get_one( 'weixin_open_id', "uid={$uid}");
        return $weixin_open_id;
    }

    /**
     * 判断微信open_id是否已经存在
     * @param $weixin_open_id
     * @return bool
     */
    public function weixin_open_id_is_exist( $weixin_open_id ) {
        $weixin_open_id = trim( $weixin_open_id );
        $uid            = $this->get_one( 'uid', "weixin_open_id={$weixin_open_id}");
        return !empty($uid);
    }

    /**
     * 根据weinxi_open_id取用户信息
     * @param $weixin_open_id
     * @return []
     */
    public function get_user_info_by_weixin_open_id( $weixin_open_id ) {
        $result = $this->get_row( "weixin_open_id='{$weixin_open_id}'" );
        if ( !empty($result) ) $result['weixin_open_id'] = $weixin_open_id;
        return !empty( $result ) ? $result : [];

    }

    /**
     * 设置微信open_id
     * @param $uid
     * @param $weixin_open_id
     * @return bool|int|string
     */
    public function set_weixin_open_id( $uid, $weixin_open_id ) {
        $sql = "
            insert into
              `user_info_ext` (`uid`,`weixin_open_id`)
            values
              ({$uid}, '{$weixin_open_id}')
           on duplicate key UPDATE
              `weixin_open_id`= values(`weixin_open_id`)
        ";
        return $this->exec( $sql );
    }
}
