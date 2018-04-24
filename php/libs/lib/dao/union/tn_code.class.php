<?php
namespace Dao\Union;
use \Dao;

/**
 * TN_分配模型
 * @package Dao\Union
 */
class Tn_Code extends Union
{
    protected static $_instance = null;

    /**
     * @return Dao\Union\Tn_Code
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function  betch_add($tn_array, $soft_id){
        $data = [];
        foreach ($tn_array as $v) {
            $temp['tn'] = is_array($v)? $v['tn']:$v;
            $temp['soft_id'] = $soft_id;
            $temp['from'] = isset($v['from'])?$v['from']:'';
            $temp['price_desc'] = isset($v['price_desc'])?$v['price_desc']:'';
            $temp['ctime'] = date('Y-m-d H:i:s');
            $data[] = $temp;
        }
        return $this->add_all($data);
    }

    public function  add_tn($tn, $soft_id,$from  = '',$price_desc = ''){
        $data['tn'] = $tn;
        $data['soft_id'] = $soft_id;
        $data['price_desc'] = $price_desc;
        $data['from'] = $from;
        $data['ctime'] = date('Y-m-d H:i:s');
        return $this->add($data);
    }

    public function get_list($coditions, $offset, $page){
        $where = '1';
        foreach ($coditions as $k => $v) {
                $where .= " AND {$k} = '{$v}'";
        }
        $sql = "select * from {$this->_realTableName}  where {$where} order by id desc limit {$offset},{$page}";
       // echo $sql;
        return $this->query($sql);
    }

    public function getlist($conditions,$offset,$prepage,$bconditions = []){
        $where = '1';
        $on = '';
        foreach ($conditions as $k => $v) {
            $where .= " AND a.{$k} = '{$v}'";
        }
        if (!empty($bconditions)){
            $on = " AND (b.ymd >= {$bconditions['s']} AND b.ymd <={$bconditions['e']})";
        }
        $sql = "select a.*,SUM(b.ip_count) as t_count from {$this->_realTableName} a LEFT JOIN  activity_hao123_vip_num_new b on b.tn = a.tn
                  and b.name = a.soft_id {$on} where {$where} GROUP BY a.tn  ORDER BY t_count desc limit {$offset},{$prepage}";
        return $this->query($sql);
    }

    /**
     * @param $coditions
     * @return mixed
     */
    public function get_count($coditions){
        $where = '1';
        foreach ($coditions as $k => $v) {
            $where .= " AND {$k} = '{$v}'";
        }
        $sql = "select count(1) as num from {$this->_realTableName} where {$where}";
        $ret = $this->query($sql);
        return $ret[0]['num'];
    }

    /**
     * 统计一段时间对tn的有销量
     * @param $coditions
     * @param $sdate
     * @param $edate
     * @return bool|int
     */
    public function tn_ip_count($coditions,$sdate,$edate){
        if (!is_array($coditions) || empty($sdate) || empty($sdate)){
            return false;
        }
        $where = '1';
        foreach ($coditions as $k => $v) {
            $where .= " AND a.{$k} = '{$v}'";
        }
        $where .= " AND b.ymd >= $sdate";
        $where .= " AND b.ymd <= $edate";
        $sql = "select sum(b.ip_count) as total from {$this->_realTableName} a LEFT JOIN  activity_hao123_vip_num_new b on b.tn=a.tn  where {$where}";
        $ret = $this->query($sql);
        return $ret[0]['total'] ? $ret[0]['total']  : 0;
    }


    /**
     * 判断tn是否已经存在
     * @param $tn
     * @param $soft_id
     * @return mixed
     */
    public function  is_exist($tn, $soft_id){
        $sql = "select tn from {$this->_realTableName} where  soft_id='{$soft_id}' and tn in({$tn})";
        return $this->query($sql);
    }

    /**
     * @param $tn
     * @return mixed
     */
    public function tn_info($tn,$soft_id){
        $sql = "select * from {$this->_realTableName} where tn = '{$tn}' and soft_id = '{$soft_id}' limit  1";
        $ret = $this->query($sql);
        return $ret[0];
    }

    public function tn_recycle($tn,$soft_id){
        $data =['status'=>1,'type'=>0,'uid'=>NULL,'manager_id'=>NULL,'admin_id'=>NULL,'dtime'=>NULL];
        $ret = $this->modify_tn($tn,$soft_id,$data);
        return $ret;
    }

    /**
     * 修改tn
     * @param $tn
     * @param $data
     * @return bool
     */
    public function modify_tn($tn,$soft_id,$data){
        $ret = $this->update("tn='{$tn}' and soft_id ='{$soft_id}'",$data);
       // echo $this->get_last_sql();
        return $ret;
    }

    /**
     * 360导航分配tn
     * @param $data
     */
    public function  distribute_tn_to_user($soft_id, $uid, $tn,$marketer_id,$admin_id){
        //更新TN状态
        $dt = date('Y-m-d H:i:s');
        $d = ['type'=>9,'status'=>2,'dtime'=>$dt,'uid'=>$uid,'manager_id'=>$marketer_id,'admin_id'=>$admin_id,'auto'=>0];
        $w = "tn='{$tn}' and soft_id='{$soft_id}'";
        return $this->update($w,$d);
    }

    /**
     * 获取用户现在的tn信息
     * @param $uid
     * @param $soft_id
     * @return mixed
     */
    public function user_tn_info($uid,$soft_id){
        $sql = "select * from {$this->_realTableName} where uid = '{$uid}' and soft_id='{$soft_id}' limit 1";
        $ret = $this->query($sql);
        return $ret[0];
    }


    /**
     * tn号更换用户
     * @param $tn
     * @param $uid
     * @param $marketer_id
     * @param $admin_id
     * @return bool
     */
    public function change_tn_user( $tn, $soft_id,$uid, $marketer_id,$admin_id){
        $d = ['uid'=>$uid,'manager_id'=>$marketer_id,'admin_id'=>$admin_id,'type'=>9,'status'=>2,'auto'=>1];
        return  $this->modify_tn($tn,$soft_id,$d);
    }

    /**
     * 官方绑定
     * @param $soft_id
     * @param $tn
     * @return bool
     */
    public function distribute_360dh_tn_to_vendor($soft_id,$tn,$type){
        $dt = date('Y-m-d H:i:s');
        $data = ['type'=>$type,'status'=>2,'dtime'=>$dt];
        $where ="tn='{$tn}' and soft_id='{$soft_id}'";
        $ret  = $this->update($where,$data);
        return $ret;
    }

    /**
     * 获取一个软件的所有tn
     * @param $soft_id
     * @return mixed
     */
    public function get_codes($soft_ids,$offset=0,$limit=2000){
        $soft_ids =  implode(',', array_map(function($str){return sprintf("'%s'", $str);}, $soft_ids ));
        $sql = "select * from {$this->_realTableName} where soft_id IN ({$soft_ids})  order by id ASC limit {$offset},{$limit}";
        return $this->query($sql);
    }
}
