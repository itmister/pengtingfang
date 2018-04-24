<?php
/**
 * Created by JetBrains PhpStorm.
 * User: caolei
 * Date: 15-5-23
 * Time: 下午4:00
 * To change this template use File | Settings | File Templates.
 * 7654官网改版个人中心=》我的帐户
 */
namespace Union\User;

class Account{

    protected static $_instance = null;

    /**
     * @return \Union\User\Account
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /*
     * 当天推广安装数
     * */
    public function today_ext_detail($uid,$today){
        $install = $this->getSoftInstallNum($uid,$today);
        if(empty($install['data'])){
            return array();
        }
        return $install;
    }

    /*
     * 某一天的推广明细以及总积分
     */
    public function day_income_detail($uid,$today){
        if(empty($uid)||empty($today)) return false;
        $list = \Dao\Union\Credit_wait_confirm::get_instance()->day_income_detail($uid,$today);
        return $list;
    }

    /*
     *某个月的推广曲线
     */
    public function month_count_detail($uid,$month){
        if(empty($uid)||empty($month)) return false;
        $list = \Dao\Union\Credit_wait_confirm::get_instance()->month_count_detail($uid,$month);
        return $list;
    }

    /**
    *某个技术员某个月里每天的推广积分
     */
    public function get_month_day_detail($uid,$month){
        if(empty($uid)||empty($month)) return false;
        $list = \Dao\Union\Credit_wait_confirm::get_instance()->get_month_day_detail($uid,$month);
        return $list;
    }

    /*
     * *去某个用户某个月积分更新的最后时间
     * */
    public function get_month_end_time($uid,$month){
        if(empty($uid)||empty($month)) return false;
        $info = \Dao\Union\Credit_wait_confirm::get_instance()->get_month_end_time($uid,$month);
        foreach ($info as $k => $item) {
            $list['dateline'] = $item['dateline'];
        }
        return $list;
    }

    /*
     * *取某天的软件安装量
     * */
    public function get_install_count($uid,$ymd,$softID){
        if(empty($uid)||empty($ymd)||empty($softID)) return false;
        $info = \Dao\Union\Rt_7654_log_install::get_instance()->get_user_install_count($uid,$ymd,$softID);
        foreach ($info as $k => $item) {
            $num = $item['onlyInstall'];
        }
        return $num;
    }
    /*
       * *取某天的软件安装量
       * */
    public function get_install_count_all($uid,$start,$end,$softID){
        if(empty($uid)||empty($start)||empty($softID)) return false;
        $info = \Dao\Union\Rt_7654_log_install::get_instance()->get_user_install_count_all($uid,$start,$end,$softID);
        foreach ($info as $k => $item) {
            $num = $item['onlyInstall'];
        }
        return $num;
    }
    /*
     * 更新我的帐户礼花
     * */
    public function account_grade_lihua($uid){
        if(empty($uid)) return false;
        \Dao\Union\User_ext::get_instance()->set_lihua($uid);
    }
    /**
     *
     * @param array("jzbrowser:8226DF9C681FA56F24311CB8F354B900"=>10,
     *              "jzbrowser:8226DF9C68GHGHRF24311CB8FGGEASAQ"=>1,
     *              "jzbrowser:8226DF9C681FA56F24311CB8F354B900"=>2,
     *              "bdws:8226DF9C681FA56F24311CB8F354B900"=>2,
     *              "bdws:8226DF9C681FA56F24311CB8yyyyB900"=>1,
     *  )
     * @return array
     *
     */
    private function getSoftInstallNum($uid,$today){
        $softNum = array();
        //$r_arr = \Lib\Core::config('redis');
        try{
            //$redis = new Redis();
            $redis = \Union\Service\Redis\Main::get_instance();
            //$redis->pconnect($r_arr['host'],$r_arr['port']);
            $field = '7654time:'.$today.':'.$uid;
            $redis->select(0);
            $installArr = $redis->hgetall($field);
            $fieldUp = '7654uptime:'.$today.':'.$uid;
            $time = $redis->get($fieldUp);
            $time = explode(':',$time);
            $time = $time[1];
        }catch (Exception $e){

        }
        if($installArr){
            foreach($installArr as $install=>$vv){
                $softIdArr = explode(':',$install);
                $softId = $softIdArr[0];
                $softNum[$softId][] = $softIdArr[1];
                $count_num[] = $softIdArr[1];
            }
        }
        $count_num = array_unique($count_num);

        return array('data'=>$softNum,'time'=>$time,'count_num'=>count($count_num));
    }

    /**
     *个人中心 推广业绩 统计记录数
    */
    public function get_user_credit_count($array){
        return \Dao\Union\Credit_wait_confirm::get_instance()->get_user_credit_count($array);
    }

    public function get_user_dh_credit_count($array){
        return \Dao\Union\Activity_Hao123_Vip_Num_New::get_instance()->get_user_credit_count($array);
    }
    /**
     *个人中心 推广业绩 取分页数据
     */
    public function get_user_credit_list($array,$page_arr){
        return \Dao\Union\Credit_wait_confirm::get_instance()->get_user_credit_list($array,$page_arr);
    }

    /**
     *个人中心 推广业绩 取分页数据 新
     */
    public function get_user_credit_list_new($array,$page_arr){
        return \Dao\Union\Credit_wait_confirm::get_instance()->get_user_credit_list_new($array,$page_arr);
    }

    public function get_user_dh_credit_list_new($array,$page_arr){
        return \Dao\Union\Activity_Hao123_Vip_Num_New::get_instance()->get_user_credit_list_new($array,$page_arr);
    }

    /**
    *个人中心 推广业绩 每天的详细列表
     */
    public function get_user_credit_day($uid,$ymd){
        if(empty($uid)||empty($ymd)) return false;
        return \Dao\Union\Credit_wait_confirm::get_instance()->get_user_credit_day($uid,$ymd);
    }

    /**
     *个人中心 推广业绩 每天的详细列表
     */
    public function get_user_dh_credit_day($uid,$ymd){
        if(empty($uid)||empty($ymd)) return false;
        return \Dao\Union\Credit_wait_confirm_no::get_instance()->get_user_dh_credit_day($uid,$ymd);
    }
    /**
     *个人中心 推广业绩 每天的详细列表
     */
    public function get_user_no_credit_day($uid,$ymd){
        if(empty($uid)||empty($ymd)) return false;
        $list = \Dao\Union\Credit_wait_confirm_no::get_instance()->get_user_credit_day($uid,$ymd);
        foreach($list as $v){
            $data[$v['name']] = $v['credit'];
        }
        return $data;
    }

    /**
     *个人中心 导航推广业绩 一段时间内的记录数
     */
    public function get_user_nav_link_count($array){
        return \Dao\Union\Activity_Hao123_Vip_Num_New::get_instance()->get_user_nav_link_count($array);
    }

    /**
     *个人中心 导航推广业绩 一段时间内的有效量的总数
     */
    public function get_user_nav_link_ip_count($array){
        return \Dao\Union\Activity_Hao123_Vip_Num_New::get_instance()->get_user_nav_link_ip_count($array);
    }

    /**
     *个人中心 导航推广业绩 一段时间内的数据
     */
    public function get_user_nav_link_list($array,$page){
        return \Dao\Union\Activity_Hao123_Vip_Num_New::get_instance()->get_user_nav_link_list($array,$page);
    }

    /**
     *个人中心 产品有效量查询 取数据
     */
    public function get_user_effective_list($array){
        return \Dao\Union\Credit_wait_confirm::get_instance()->get_user_effective_list($array);
    }
    /**
     *个人中心 产品有效量查询 取数据
     */
    public function get_user_effective($array){
        return \Dao\Union\Credit_wait_confirm::get_instance()->get_user_effective($array);
    }

    /**
     *个人中心 月实际充入积分明细 取数据
     */
    public function get_user_income($array){
        return \Dao\Union\Credit_wait_confirm::get_instance()->get_user_income($array);
    }
}
