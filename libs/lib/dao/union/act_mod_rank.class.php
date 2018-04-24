<?php
namespace Dao\Union;
use \Dao;

/**
 * 经验月度排行模型
 * @package Dao\Union
 */
class Act_Mod_Rank extends Union {
    protected static $_instance = null;
    private  $_rank_hist_table = 'act_mod_rank_hist';
    private  $_rank_table = 'act_mod_rank';

    /**
     * @return Dao\Union\Act_Mod_Rank
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function update_rank($rid,$month){
        $month_s = intval($month.'01');
        $month_e = intval($month.'31');
        $sql = "select sum(login) as login,sum(posts) as posts,sum(threads) as threads,sum(new_slave) as new_slave,sum(good_slave) as good_slave,
                    sum(install) as install ,sum(dhcount) as dh_count,uid,name from {$this->_rank_hist_table} where date>={$month_s} and date <={$month_e} and  rid ={$rid} and
                    uid not in (56040,56041,56042,56043,56044,56045) GROUP by uid";
        echo $sql;
        $data = $this->query($sql);
        if (empty($data)) return false;
        $d = [];
        $date = date("Y-m-d");
       // $a_s = time()<strtotime('2015-05-05 19:00:00') ? false:true ; //活动已经开始正常按天跑，之前为统计跑
        echo "a_s".$a_s."\n";
        $days = cal_days_in_month(CAL_GREGORIAN,intval(substr($month,4)) , intval(substr($month,0,4))); //每个月的天数
        foreach($data as $v){
            $row =[];
            $post_data =  $this->getPostsData($v['uid'],$month,$rid);
            $row['uid'] = $v['uid'];
            $row['rid'] = $rid;
            $row['name'] = $v['name'];
            $row['month'] = $month;
            $row['udate'] = $date;
            $row['login'] = min($v['login'] * 100,100 * $days);
            $row['posts'] =   min( $v['posts'] * 30,$post_data['posts'] + 300);
            $row['threads'] =  min( $v['threads'] *10,$post_data['threads'] + 300);
            $row['new_slave'] = min($v['new_slave'] * 500,5000); //每月5千最多
            $row['good_slave'] = $v['good_slave']*300 ;
            $row['install'] = $v['install']*100;
            $row['dhcount'] = $v['dh_count']*200;
            $row['total'] = $row['login']+$row['posts']+$row['threads']+ $row['new_slave']+$row['good_slave']+$row['install']+$row['dhcount'];
            $d[] = $row;
        }
        return $this->add_all($d,true);
    }

    function getPostsData($uid,$month,$rid){
          $sql = "select posts,threads from {$this->_rank_table} where uid = {$uid}  and month = {$month} and rid={$rid} limit 1";
          echo $sql;
          $data = $this->query($sql);
          var_dump($data);
         return $data[0];
    }
}
