<?php
/**
 * 兑现表
 */
namespace Dao\Union;
use \Dao;

class Exchange extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Exchange
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    /**
     * 获取兑现信息
     * @return array
     */
	public function select($where){
        $sql = "SELECT * FROM `{$this->_realTableName}` WHERE {$where}";
        return $this->query($sql);
    }

    /**
     * 获取累计发放工资
     */
    public function goods_money($goods_time){
        $sql = "SELECT FROM_UNIXTIME(datetime,'%Y%m%d'),SUM(goods) AS money FROM `{$this->_realTableName}` WHERE `status` = 1 AND type IN (2,3) AND FROM_UNIXTIME(datetime,'%Y%m%d') = {$goods_time}";
        $data = $this->query($sql);
        $coeff = rand(20,30);
        $goods_money = !empty($data[0]['money']) ? $data[0]['money'] * $coeff : 0;
        return $goods_money;
    }

    /**
     * 获取30累计发放工资
     */
    public function goods_money_last_month(){

        $sql = "SELECT SUM(credit) money FROM credit_wait_confirm WHERE type = 2 AND is_get <> 2 AND delete_flag = 0 and ymd BETWEEN DATE_FORMAT( date_sub(now(),interval 1 month) , '%Y%m01') AND DATE_FORMAT( date_sub(now(),interval 1 month) , '%Y%m31')";
        $data = $this->query($sql);
        $goods_money = !empty($data[0]['money']) ? $data[0]['money'] /1000 * 10 : 0;
        return $goods_money;
    }



    /**
     * 获取总数
     * @param string $where
     * @return array
     */
    public function count($where){
    	$sql = "SELECT count(*) as count FROM `{$this->_realTableName}` WHERE {$where}";
    	$result = $this->query($sql);
    	return $result[0]['count'];
    }

    /**
     * 获取一段时间内的积分
     * @param $s_time
     * @param $e_time
     */
    public function get_sum_credit($s_time,$e_time){
        $where  = "datetime >= {$s_time} and datetime <={$e_time}";
        $sql = "SELECT sum(credit) as num FROM `{$this->_realTableName}` WHERE {$where}";
        $result = $this->query($sql);
        return $result[0]['num'];
    }

    /**
     * 获取一段时间内的积分
     * @param $s_time
     * @param $e_time
     */
    public function get_user_credit_sum($where){
        $sql = "SELECT sum(credit) as num FROM `{$this->_realTableName}` WHERE {$where}";
        $result = $this->query($sql);
        return $result[0]['num']?$result[0]['num']:0;
    }

    /**
     * 扣积分是 检测用户提现积分
     * @param
     * @param
     */
    public function get_user_exchange_point_sum($uid){
        $sql = "SELECT sum(credit) as num FROM `{$this->_realTableName}` WHERE uid={$uid} and status=1 and dealstatus<>111";
        $result = $this->query($sql);
        return $result[0]['num']?$result[0]['num']:0;
    }

    /**
     * 取用户兑现记录
     * @param $uid
     * @return array
     */
    public function get_list( $uid ) {
        $sql = "
        select
            ce.*,
            e.status,
            e.phone,
            e.backtime,#打款时间
            e.account,
            e.type as exchange_type,
            e.fee_type,
            e.goodsid,
            e.id as eid,
            e.orderid,
            e.boxtype,
            e.dealstatus
        from
          credit_expense ce
          left JOIN exchange e on ce.uid=e.uid and ce.dateline=e.datetime
        where
          ce.uid={$uid}
        ORDER  by ce.dateline desc
        ";
        return $this->query( $sql );
    }
}
