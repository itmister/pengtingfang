<?php
/**
 * Created by JetBrains PhpStorm.
 * User: caolei
 * Date: 15-5-23
 * Time: 下午4:00
 * To change this template use File | Settings | File Templates.
 * 7654官网改版个人中心=》我的帐户
 */
namespace Union\Grade;
use \Dao\Union\User_ext;
use \Dao\Union\Config_grade;
use \Dao\Union\Org_empirical;
class Empirical extends Base{

    protected static $_instance = null;

    /**
     * @return \Union\Grade\Empirical
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /*
     * 添加经验值
     *
     *  $array = array(
         *    'uid' '用户id',
              'dateline' '时间戳',
              'type' '类型,2:推广软件,3:任务,4:论坛,10：活动',
              'empirical'  '经验值',
              'name'  '名称 见 empirical_name_config 和 promotion 表',
              'ymd'  '年月日',
              'ip_count' '有效量除推广软件 外都为0'
          );
     * */
    public function add_emp($array){
        if(empty($array) || !is_array($array)){
            return false;
        }
        
        //用户扩展信息
        $params = ["where" => "uid = {$array['uid']}"];
        $user_ext = User_ext::get_instance()->find($params);
        
        //经验值
        if($array['type']==2&&($array['empirical']==0&&$array['ip_count']>0)){
            $array['empirical'] = floor($array['ip_count']*$user_ext['multiple']*10);
        }
        if($array['empirical']<=0){
            return false;
        }
        $empirical = $user_ext['empirical'] + $array['empirical'];
        $set_data = [
            'empirical' => $empirical
        ];
        
//        $params = [
//            "where"   => "min_emp < {$empirical}",
//            "orderby" => "min_emp DESC"
//        ];
//        $grade_info  = Config_grade::get_instance()->find($params);
//        if($grade_info){
//            if($user_ext['empirical'] <= $grade_info['min_emp'] && $user_ext['lock_grade']==0){
//                $set_data['lihua'] = 1;
//            }
//        }
        
        //添加经验明细
        Org_empirical::get_instance()->add($array);
        
        //更新用户经验值
        User_ext::get_instance()->update("uid = {$array['uid']}", $set_data);
        
        return true;

    }
}
