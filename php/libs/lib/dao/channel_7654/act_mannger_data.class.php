<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/2/21
 * Time: 11:08
 */
namespace Dao\Channel_7654;
use \Dao\Union;
class Act_mannger_data extends Channel_7654{
    protected static $_instance = null;

    /**
     * @return Act_mannger_data
     */
    public static function  get_instance() {
        return parent::get_instance();
    }

    /**
     * @param $marketer_uid
     * 取市场经理获得的黄金贡献点和白银贡献点
     */

    public function get_gold_white($marketer_uid ,$act_start_time ,$act_end_time){
        $sql = "SELECT a.id,a.reg_dateline FROM `union`.`user` as a LEFT JOIN channel_7654.user_marketer AS b ON a.invitecode = b.idcode WHERE b.userid = {$marketer_uid} AND a.reg_dateline BETWEEN UNIX_TIMESTAMP('{$act_start_time}') AND UNIX_TIMESTAMP('{$act_end_time}')";
        $technician_reg = $this->query( $sql );

        $marketer_list = array();
        if(empty($technician_reg)){
            return false;
        }else{
            $technician_num = count($technician_reg);
        }
        foreach($technician_reg as $key=>$item){
            $week_date = strtotime(' + 7 day',$item['reg_dateline']);

            $gold_credit_sql = "SELECT SUM(ip_count) as num FROM `union`.credit_wait_confirm WHERE type = 2 AND `name` IN ('qqpcmgr','qqpcmgrdz','qqpcmgr_dh') AND is_get <> 2 AND dateline BETWEEN {$item['reg_dateline']} AND {$week_date} AND uid = {$item['id']}";
            $gold = $this->query($gold_credit_sql);

            if($gold[0]['num'] < 2){
                $white_credit_sql = "SELECT SUM(ip_count) as num FROM `union`.credit_wait_confirm WHERE type = 2 AND `name` NOT IN ('qqpcmgr','qqpcmgrdz','qqpcmgr_dh') AND is_get <> 2 AND dateline BETWEEN {$item['reg_dateline']} AND {$week_date} AND uid = {$item['id']}";

//                echo $white_credit_sql;exit;
                $white = $this->query($white_credit_sql);
                if($white[0]['num'] < 2){

                }else{
                    $marketer_list[$key]['white'] = 1;
                }
            }else{
                $marketer_list[$key]['gold'] = 1;
            }
        }

//        echo '<pre>';print_r($marketer_list);exit;

        $i = 0;
        $j = 0;
        if(!empty($marketer_list)){
            foreach($marketer_list as $k=>$v){
                $k_key = array_keys($v);
                if($k_key[0] == 'gold'){
                    $i++;
                }elseif($k_key[0] == "white"){
                    $j++;
                }
            }
            $marketer_price['technician_num'] = $technician_num;
            $marketer_price['gold'] = $i;
            $marketer_price['white'] = $j;
//            echo '<pre>';print_r($marketer_price);exit;
            return $marketer_price;
        }else{
            $marketer_price['gold'] = $i;
            $marketer_price['white'] = $j;
            $marketer_price['technician_num'] = $technician_num;
            return $marketer_price;
        }
    }


    /**
     * 市场经理兑换记录
     */

    public function marketer_exchange($uid){
        $sql = "SELECT * FROM channel_7654.act_mannger_data WHERE uid = {$uid} ORDER BY datetime DESC";
        $marketer_exchange = $this->query($sql);
        foreach($marketer_exchange as $key=>$value){
            if($value['prize_type'] == 7){
                $list[$key]['prize_name'] = $value['prize_name']."积分";
            }else{
                $list[$key]['prize_name'] = $value['prize_name'];
            }
            if(empty($value['gold'])){
                $list[$key]['log'] = $value['white']."个白银贡献点";
            }else{
                $list[$key]['log'] = $value['gold']."个黄金贡献点";
            }
            $list[$key]['datetime'] = $value['datetime'];
        }
        return $list;
    }

    /**
     *  新技术员列表
     * @return 列表
     */

    public function technician_list(){
        $sql = "SELECT b.`name` FROM `union`.act_admin as a LEFT JOIN `union`.`user` as b ON a.uid = b.id WHERE a.act_id = 3 ORDER BY a.datetime";
        $technician = $this->query($sql);
        $technician_count = count($technician);
        if($technician_count%2==1){
            $data = array_slice($technician,0,$technician_count - 1);
        }else{
            $data = $technician;
        }
        return $data;
    }

}