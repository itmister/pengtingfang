<?php
namespace Dao\Union;
use \Dao;

/**
 * tn 分配统计
 * @package Dao\Union\Activity_Hao123_Vip_Num_New
 */
class Activity_Hao123_Vip_Num_New extends Union
{
    protected static $_instance = null;

    /**
     * @return Dao\Union\Activity_Hao123_Vip_Num_New
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }


    /**
     * 统计用户导航使用量
     * @param $uid
     * @param $soft_id
     * @param string $start_ymd
     * @param string $end_ymd
     * @return array
     */
    public function stati_user_ip_count($uid,$soft_id='',$start_ymd='',$end_ymd=''){
        $where ='1 ';
        $soft_id && $where .=" AND name in({$soft_id})";
        $uid && $where .=" AND uid ={$uid}";
        if( ($start_ymd && $end_ymd) && ($end_ymd >= $start_ymd))
        {
            if ($end_ymd == $start_ymd){
                $where .=" AND ymd ={$start_ymd}";
            }else{
                $where .=" AND (ymd >={$start_ymd} and ymd <={$end_ymd})";
            }
        }
        $sql  = "select sum(ip_count) as count from {$this->_realTableName} where {$where}";
        $ret = $this->query($sql);
        return $ret[0]['count']?$ret[0]['count']:0;
    }


    public function stati_ip_count($soft_id,$tn,$start_ymd='',$end_ymd='',$tns=false){
        $where ='1 ';
        $soft_id && $where .=" AND name ='{$soft_id}'";
        if ($tns){
            $tns && $where .= " AND tn IN($tn) ";
        }else{
            $tn && $where .=" AND tn ='{$tn}' ";
        }
        if( ($start_ymd && $end_ymd) && ($end_ymd >= $start_ymd))
        {
            if ($end_ymd == $start_ymd){
                $where .=" AND ymd ={$start_ymd}";
            }else{
                $where .=" AND (ymd >={$start_ymd} and ymd <={$end_ymd})";
            }
        }
        $sql  = "select sum(ip_count) as count from {$this->_realTableName} where {$where}";
        $ret = $this->query($sql);
        return $ret[0]?$ret[0]:[];
    }


    /**
     * 统计每天的的量 可根据渠道主管
     * @param $soft_id
     * @param string $start_ymd
     * @param string $end_ymd
     * @param string $admin_id
     * @return mixed
     */
    public function stati_ip_count_daily($soft_id,$ymd,$admin_id =''){
        if ($admin_id){
            $sql = "select sum(a.ip_count) as num,count(DISTINCT a.tn) AS tn_num from {$this->_realTableName} a LEFT JOIN tn_code b ON b.tn = a.tn where a.name ='{$soft_id}'
                      and a.ymd ={$ymd}  AND b.admin_id = {$admin_id}";
        }else{
            $sql = "select sum(ip_count) as num ,count(DISTINCT tn) AS tn_num  from {$this->_realTableName} where name ='{$soft_id}'  and ymd = {$ymd}";
        }
        $ret =  $this->query($sql);
        return $ret['0'];
    }

    /**
     * 个人中心 导航推广业绩查询 取一段时间的总记录数
    */
    public function get_user_nav_link_count($array){
        $sql = "select count(*) as num from {$this->_realTableName} where uid={$array['uid']} and ymd>={$array['start']} and ymd<={$array['end']} and name='{$array['name']}'";
        $count = $this->query($sql);
        return !empty($count[0]['num'])?$count[0]['num']:0;
    }

    /**
     *个人中心 导航推广业绩 一段时间内的有效量的总数
     */
    public function get_user_nav_link_ip_count($array){
        $sql = "select sum(ip_count) as ip_count from {$this->_realTableName} where uid={$array['uid']} and ymd>={$array['start']} and ymd<={$array['end']} and name='{$array['name']}' limit 1";
        $count = $this->query($sql);
        $data['ip_count'] = !empty($count[0]['ip_count'])?$count[0]['ip_count']:0;
        return $data;
    }

    /**
     * 个人中心 导航推广业绩查询 取一段时间的数据
     */
    public function get_user_nav_link_list($array,$page){
        $sql = "select ymd,name,dateline,sum(ip_count) as ip_count from {$this->_realTableName} where uid={$array['uid']} and ymd>={$array['start']} and ymd<={$array['end']} and name='{$array['name']}' group by ymd order by ymd desc limit {$page['limit_start']},{$page['limit_end']}";

        $list = $this->query($sql);
        return $list;
    }

    /**
     * 取限定发放日期推广总量列表 $promotion_short_name 空则按 $promotion_short_name 分组 uid 空则按uid 分组
     * @param string $promotion_short_name 推广软件short_name,如dgdh
     * @param integer $uid 用户uid
     * @param $dateline_start 发放开始时间戳
     * @param $dateline_end 发放结束时间戳
     * @param string $order 排序 desc:降序 asc:升序
     * @param integer $min 最小安装量
     * @param string $limit 数量限制
     * @return array
     */
    public function sum_install_list( $promotion_short_name = '', $uid = null, $dateline_start, $dateline_end, $order = 'desc', $min = 0, $limit = '' ) {
        $table_name         = $this->_get_table_name();
        $group_field        = empty( $promotion_short_name ) ? '`name`' : ( empty($uid) ? '`uid`' : '`name`,`uid`') ;
        $where_promotion    = !empty( $promotion_short_name ) ? " AND a.`name`='{$promotion_short_name}'" : '';
        $sql = "
            SELECT
                sum(a.ip_count) as num,#安装量
                a.uid,
                u.name,
                a.tn
            FROM
                {$table_name} a
            LEFT JOIN
              `user` u on a.uid=u.id
            WHERE
                dateline >= {$dateline_start}
                AND dateline <= {$dateline_end}
                {$where_promotion}

            GROUP BY
                {$group_field}
            HAVING
                sum(a.ip_count) >= {$min}
            ORDER BY
                num {$order}
        ";
        if (!empty($limit)) $sql .= " LIMIT {$limit} ";
        $result = $this->query( $sql );
        return $result;
    }

    /**
     * 取指定日期范围推广量
     * @param string $promotion_short_name
     * @param null $uid
     * @param $dateline_start
     * @param $dateline_end
     * @return integer
     */
    public function sum_install(  $promotion_short_name = '', $uid = null, $dateline_start, $dateline_end ) {
        $where = [
            "`name`='{$promotion_short_name}'",
            "uid='{$uid}'",
            "dateline >= '{$dateline_start}'",
            "dateline <= '{$dateline_end}'"
        ];
        return intval($this->get_one('sum(ip_count) as num', implode(' AND ', $where)));
    }

    /**
     * 获取软件推广增量日志
     */
    public function get_dh_add_recode_daily($s_time,$e_time){
        $sql = "select name,ymd,sum(ip_count) as num from {$this->_realTableName} where add_time >={$s_time} and add_time < {$e_time} and name <>'hao123' GROUP by name";
        return $this->query($sql);
    }
    
    /**
     * 获取某人时间段内推广软件的ip_count
     * @param int $uid
     * @param int $ymd_start
     * @param int $ymd_end
     * @return array
     */
    public function get_month_ip_count_by_uid($uid, $ymd_start, $ymd_end){
    	$sql = "select name,sum(ip_count) as ip_count 
    	from {$this->_realTableName} 
    	where uid={$uid} and ymd>={$ymd_start} and ymd<={$ymd_end} 
    	GROUP by name";
    	return $this->query($sql);
    }

    /**
     *个人中心 推广业绩 统计记录数
     */
    public function get_user_credit_count($array){
        $where = "uid={$array['uid']} and ymd>={$array['start']} and ymd<={$array['end']}";
        if(!empty($array['name'])){
            $where .= " and name='{$array['name']}'";
        }
        $sql = "select count(*) as num
                from (select id from {$this->_realTableName} where {$where} group by ymd ) aa";
        $count = $this->query($sql);
        return !empty($count[0]['num']) ? $count[0]['num'] : 0;
    }

    /**
     *个人中心 推广业绩 取分页数据 新
     */
    public function get_user_credit_list_new($array,$page_arr){
        $where = "uid={$array['uid']} and ymd>={$array['start']} and ymd<={$array['end']}";
        $sql = "select ymd
                from {$this->_realTableName} where {$where} group by ymd order by ymd desc limit {$page_arr['limit_start']},{$page_arr['limit_end']}";
        $list = $this->query($sql);
        return $list;
    }
}
