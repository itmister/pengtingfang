<?php
namespace Union\Point_grant;
use Util\Tool;
/**
 * Created by JetBrains PhpStorm.
 * User: caolei
 * Date: 16-4-7
 * Time: 上午10:28
 * To change this template use File | Settings | File Templates.
 * 原始渠道号转换uid
 */
class Privilege{

    protected static $_instance = null;

    /**
     * @return \Union\Point_grant\Privilege
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * @param $array= array(
     *               'name'=>'dongfang',//软件id
     *               'ymd'=> 20150325,//推广日期
     * )
     * @return $data = array(
     *               'uid_arr' => array('所有有特权的uid'),
     *               'privilege' => array(
     *                       'uid' => '单价',
     *                ) ,
     * )
     */
    public function get_user_soft_config($array){
        if(empty($array)||empty($array['name'])||empty($array['ymd'])){
            return false;
        }
        //$user_soft_list = M('user_soft_config')->field('uid,price')->where("name='{$array['name']}' and start_ymd<={$array['ymd']} and end_ymd>={$array['ymd']}")->select();
        $user_soft_list = \Dao\Union\User_vip::get_instance()->query(
            "select uid,price from user_soft_config where name='{$array['name']}' and start_ymd<={$array['ymd']} and end_ymd>={$array['ymd']};"
        );
        if(empty($user_soft_list)){
            return false;
        }
        $data = array();
        foreach($user_soft_list as $v){
            if(intval($v['uid'])<=0 || intval($v['price'])<=0){
                continue;
            }
            $data['privilege'][$v['uid']] = $v['price'];
        }
        if(empty($data['privilege'])){
            return false;
        }
        $data['uid_arr'] = array_keys($data['privilege']);
        if(empty($data['uid_arr'])){
            return false;
        }
        return $data;

    }
}
