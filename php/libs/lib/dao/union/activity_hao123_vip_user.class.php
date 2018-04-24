<?php
namespace Dao\Union;
use \Dao;
/**
 * hao123导航申请
 */
class Activity_Hao123_Vip_User extends Union
{
    protected static $_instance = null;

    /**
     * @return Dao\Union\Activity_Hao123_Vip_User
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * @param $uid
     * @param $expect_range
     * @param $status
     * @param $offset
     * @param $page_size
     * @return mixed
     */
    public function get_list($uid,$expect_range,$status,$offset,$page_size,$order = 'dh_ip_count desc'){
        $where  = "where 1";
        if ($uid){
            $where .=" and uid = {$uid}";
        }
        if ($expect_range){
            $where .=" and expect_range in ({$expect_range})";
        }
        if ($status !==''){
            $where .=" and status in ({$status})";
        }
        $sql = "select * from {$this->_realTableName} $where ORDER  by {$order} ,addtime desc limit {$offset},{$page_size}";
        $ret =  $this->query($sql);
        return $ret;
    }

    public function get_count($uid,$expect_range,$status){
        $where  = "where 1";
        if ($uid){
            $where .=" and uid = {$uid}";
        }
        if ($expect_range){
            $where .=" and expect_range in ({$expect_range})";
        }
        if ($status !==''){
            $where .=" and status in ({$status})";
        }
        $sql = "select count(1) as num  from {$this->_realTableName} $where ";
        $ret =  $this->query($sql);
        return $ret[0]['num']?$ret[0]['num']:0;
    }

    /**
     * @param $uid
     * @param $username
     * @param $expect_range
     * @param $phone
     * @return bool|int|\mysqli_result|string
     */
    public function add_user($uid,$username,$expect_range,$phone,$soft_ip_count,$dh_ip_count){
        $d = [
            'uid'=>$uid,
            'username'=>$username,
            'expect_range'=>$expect_range,
            'phone'=>$phone,
            'soft_ip_count'=>$soft_ip_count,
            'dh_ip_count'=>$dh_ip_count,
            'addtime'=>time(),
            'status'=>0
        ];
        return $this->add($d);
    }

    /**
     * @param $uid
     * @return array
     */
    public function get_user_info($uid){
        $sql = "select * from {$this->_realTableName} where uid={$uid} limit 1";
        $ret =  $this->query($sql);
        return $ret[0]?$ret[0]:[];
    }

    public function get_info_by_tn($tn){
        $sql = "select * from {$this->_realTableName} where tn='{$tn}' limit 1";
        $ret =  $this->query($sql);
        return $ret[0]?$ret[0]:[];
    }

    public function get_info_by_id($id){
        $sql = "select * from {$this->_realTableName} where id={$id} limit 1";
        $ret =  $this->query($sql);
        return $ret[0]?$ret[0]:[];
    }


    /**
     * @param $id
     * @param $status
     * @return bool
     */
    public function change_status($id,$status){
        return $this->update('id='.$id,['status'=>$status]);
    }
}
