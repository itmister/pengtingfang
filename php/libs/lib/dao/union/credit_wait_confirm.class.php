<?php
/**
 * 积分表的查询，更新，添加
 */
namespace Dao\Union;
use \Dao;
class Credit_wait_confirm extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Credit_wait_confirm
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * 统计用户软件有效推广量
     * @param $uid
     * @param $soft_ids
     */
    public function get_user_ip_count($uid,$soft_ids,$not_soft_ids = []){
        $where  = 'type=2 and delete_flag=0 and is_get<>2 and uid ='.$uid;
        if (is_array($soft_ids) && $soft_ids){
            $n = implode("','",$soft_ids);
            $n = "'".$n."'";
            $where .= " AND name in ({$n})";
        }
        if (is_array($not_soft_ids) && $not_soft_ids){
            $ids = implode("','",$not_soft_ids);
            $ids = "'".$ids."'";
            $where .= " AND name not in ({$ids})";
        }
        $sql = "select sum(ip_count) as ip_count from {$this->_realTableName} where $where";
        $list = $this->query($sql);
        return $list[0]['ip_count']?$list[0]['ip_count'] : 0 ;
    }


    public function get_row_by_coditions($conditions){
        $where = '1';
        foreach($conditions as $key=>$val){
            $where .=" and {$key}='{$val}'";
        }
        $sql  = "select * from {$this->_realTableName} where $where limit 1";
        $ret  = $this->query($sql);
        return  $ret[0]?$ret[0]:[];
    }

    /**
     * 增加积分记录
     * @param integer $user_id 用户id
     * @param integer $credit 得到的积分
     * @param integer $type 类型
     * @param integer $sub_type 子类型
     * @param integer $ip_count 有效数量
     * @param $need_confirm 是否需要审核
     */
    public function add_credit($user_id, $credit, $type, $sub_type, $name = '', $ip_count = 0, $from_id = 0 , $dateline = 0,$xishu=1,$fafang_type=1){
        $time_now = empty($dateline) ? time() : $dateline;
        $data = array(
            'uid' 		=> $user_id,
            'credit'	=> $credit,
            'type'		=> $type,
            'sub_type'	=> $sub_type,
            'name'		=> $name,
            'ip_count'	=> $ip_count,
            'is_get' 	=>  0,
            'ym'		=> date('ym', $time_now),
            'ymd'		=> date('Ymd', $time_now),
            'dateline'  => time(),
            'from_id'	=> $from_id,
            'xishu'     => $xishu,
            'fafang_type'=> $fafang_type
        );
        $credit_wait_confirm_id = $this->add($data);
        if($credit_wait_confirm_id){
            $info = \Dao\Union\Credit_Name_Decs_Map::get_instance()->get_info_by_name($name);
            $user_change_log = array(
                'uid' => $user_id,
                'ip_count' => ($data['type']==2) ?$ip_count:0,
                'credit' =>$credit,
                'name' => $data['name'],
                'rule' => ($data['type']==2) ? "安装":"---",
                'user_type' => $info['with_attr'] ? 2:1,
                'dateline' => $data['dateline'],
                'ymd'=>$data['ymd'],
            );
            \Dao\Union\User_change_log::get_instance()->add_log($user_change_log);
        }
        return $credit_wait_confirm_id;
    }


    /*
     * 某一天的推广明细以及总积分
     */
    public function day_income_detail($uid,$today){
        $sql = "select dateline, sum(credit) as credit, is_get, sum(ip_count) as ip_count,name,type,ymd
                from {$this->_realTableName}
                where uid={$uid} and FROM_UNIXTIME(dateline,'%Y%m%d')={$today} and is_get<>2 and delete_flag=0
                group by name,ymd order by ymd desc";
        return $this->query($sql);
    }
    /*
     *某个月的推广曲线
     */
    public function month_count_detail($uid,$month){
        $sql = "select name,sum(credit) as credit
                from {$this->_realTableName}
                where uid={$uid} and ym={$month} and credit>0 and type=2 and delete_flag=0 and is_get<>2
                group by name order by sum(credit) desc limit 0,6";
        return $this->query($sql);
    }

    /**
     * 技术员产品某几款软件有效量查询
     */
    public function get_user_soft_effective($uid,$soft_ids,$start_ymd,$end_ymd){
        $n = implode("','",$soft_ids);
        $n = "'".$n."'";
        $sql = "select sum(ip_count) as ip_count
                from {$this->_realTableName} where uid={$uid} and ymd>={$start_ymd} and ymd<={$end_ymd} and type=2 and delete_flag=0 and is_get<>2 and name in ({$n})";
        $list = $this->query($sql);
        return $list[0]['ip_count']?$list[0]['ip_count'] : 0 ;
    }


    /**
     *某个技术员某个月里每天的推广积分
     */
    public function get_month_day_detail($uid,$month){
        $sql = "select ymd,sum(credit) as credit
                from {$this->_realTableName}
                where uid={$uid} and ym={$month} and credit>0 and type=2 and delete_flag=0 and is_get<>2
                group by ymd order by ymd asc";
        return $this->query($sql);
    }

    /*
    * *去某个用户某个月积分更新的最后时间
    * */
    public function get_month_end_time($uid,$month){
        $sql = "select dateline
                from {$this->_realTableName}
                where uid={$uid} and ym={$month} and delete_flag=0 and is_get<>2
               order by dateline desc limit 1";
        return $this->query($sql);
    }
    /**
     *个人中心 推广业绩 统计记录数
     */
    public function get_user_credit_count($array){
        $where = "uid={$array['uid']} and ymd>={$array['start']} and ymd<={$array['end']} and delete_flag=0 and is_get<>2 and sub_type not in(10,11)";
        if(!empty($array['name'])){
            $where .= " and name='{$array['name']}'";
        }
        $sql = "select count(*) as num
                from (select id from {$this->_realTableName} where {$where} group by ymd ) aa";
        $count = $this->query($sql);
        return !empty($count[0]['num']) ? $count[0]['num'] : 0;
    }

    /**
     *个人中心 推广业绩 取分页数据
     */
    public function get_user_credit_list($array,$page_arr){
        $where = "uid={$array['uid']} and ymd>={$array['start']} and ymd<={$array['end']} and delete_flag=0 and is_get<>2";
        if(!empty($array['name'])){
            $where .= " and name='{$array['name']}'";
        }
        $sql = "select dateline, sum(credit) as credit, is_get, ymd
                from {$this->_realTableName} where {$where} group by ymd order by ymd desc limit {$page_arr['limit_start']},{$page_arr['limit_end']}";
        $list = $this->query($sql);
        return $list;
    }

    /**
     *个人中心 推广业绩 取分页数据 新
     */
    public function get_user_credit_list_new($array,$page_arr){
        $where = "uid={$array['uid']} and ymd>={$array['start']} and ymd<={$array['end']} and delete_flag=0 and is_get<>2 and sub_type not in(10,11)";
        $sql = "select ymd
                from {$this->_realTableName} where {$where} group by ymd order by ymd desc limit {$page_arr['limit_start']},{$page_arr['limit_end']}";
        $list = $this->query($sql);
        return $list;
    }

    /**
     *个人中心 推广业绩 每天的详细列表
     */
    public function get_user_credit_day($uid,$ymd){
        $sql = "select dateline,is_get,name, type, sub_type, sum(credit) as credit,ip_count
                from {$this->_realTableName} where uid={$uid} and ymd={$ymd} and delete_flag=0 and is_get<>2 and sub_type not in(2,10,11) and name not in('jsdh','hao123','sgdh','360dh') group by name";
        $list = $this->query($sql);
        return $list;
    }

    /**
     *个人中心 产品有效量查询 取数据
     */
    public function get_user_effective_list($array){
        $sql = "select name,sum(ip_count) as ip_count
                from {$this->_realTableName} where uid={$array['uid']} and ymd>={$array['start']} and ymd<={$array['end']} and type=2 and delete_flag=0 and is_get<>2
                group by name order by sum(ip_count) desc";
        $list = $this->query($sql);
        return $list;
    }

    /**
     *个人中心 产品有效量查询 取数据
     */
    public function get_user_effective($array){
        $n = implode("','",$array['name']);
        $n = "'".$n."'";
        $sql = "select ymd,name,sum(ip_count) as ip_count
                from {$this->_realTableName} where uid={$array['uid']} and ymd>={$array['start']} and ymd<={$array['end']} and type=2 and delete_flag=0 and is_get<>2 and name in ({$n})
                group by ymd,name order by ymd desc";
        $list = $this->query($sql);
        return $list;
    }

    /**
     *个人中心 月实际充入积分明细 取数据
     */
    public function get_user_income($array){
        $n = implode("','",$array['name']);
        $n = "'".$n."'";

        $sql = "select dateline, sum(credit) as credit, is_get, sum(ip_count) as ip_count,name,type,ymd
                from {$this->_realTableName} where uid={$array['uid']} and dateline>={$array['start']} and dateline<={$array['end']} and delete_flag=0 and is_get<>2 and name not in ({$n}) group by name";
        $list = $this->query($sql);
        return $list;
    }
    
    /**
     * 获取用户近期推广日期
     * @param unknown $uid
     * @return boolean
     */
    public function get_max_promotion_day($uid){
        if(!$uid){
            return false;
        }
        
        $sql = "SELECT MAX(ymd) AS ymd from {$this->_realTableName} where `uid` = {$uid} AND `type` = 2 AND `delete_flag` = 0 AND `ip_count` > 0";
        $promotion_day = current($this->query($sql));
        $promotion_day = !empty( $promotion_day['ymd'] ) ? $promotion_day['ymd'] : 0;
        
        $sql = "SELECT MAX(ymd) AS ymd from `activity_hao123_vip_num_new` where `uid` = {$uid} AND `ip_count` > 0";
        $hao123_promotion_day = current($this->query($sql));
        $hao123_promotion_day = !empty( $hao123_promotion_day['ymd'] ) ? $hao123_promotion_day['ymd'] : 0;

        if($hao123_promotion_day > $promotion_day){
            return $hao123_promotion_day;
        }
        return $promotion_day;
    }

    /*
     * 获取某用户的一段时间的安装量
     *@param $array = array(
                'uid'
                'startTime'
                'endTime'
                )
              $softIdArray softid
     *
     * */
    public function get_user_numlist_by_param($array,$softIdArray){
        $softStr = implode("','",$softIdArray);
        $softStr = "'".$softStr."'";
        $sql = "select uid,sum(ip_count) as num,name,ymd
                from {$this->_realTableName}
                where uid={$array['uid']} and ymd>={$array['startTime']} and ymd<={$array['endTime']} and is_get in (0,1) and name in ({$softStr})
                group by ymd,name order by ymd desc";
        return $this->query($sql);
    }

    public function get_soft_dateline_by_ymd($start,$end,$softIdArray){
        $softStr = implode("','",$softIdArray);
        $softStr = "'".$softStr."'";
        $sql = "select dateline,name,ymd
                from {$this->_realTableName}
                where  ymd>={$start} and ymd<={$end} and is_get in (0,1) and name in ({$softStr})
                group by ymd,name order by ymd desc";
        return $this->query($sql);
    }
    
    /**
     * 根据uid获取某月软件ip_count,credit
     */
    public function get_credit_ip_count_by_uid($uid,$ym){
    	$sql = "select name,sum(credit) as credit,sum(ip_count) as ip_count
    	from {$this->_realTableName}
    	where uid={$uid} and ym={$ym} and delete_flag=0 and is_get<>2
    	group by name order by sum(credit) desc";
    	return $this->query($sql);
    }
    
    /**
     * 有业绩且资料完整技术员数
     */
    public function fetch_subordinate_performance($invitecode,$start_ymd,$last_task_uid)
    {
        if(!$invitecode || !$start_ymd)
        {
            return [];
        }
        
        $sql = "
            SELECT DISTINCT u.id FROM user AS u INNER JOIN {$this->_realTableName} AS c ON u.id = c.uid 
            WHERE u.invitecode = '{$invitecode}' AND u.info_is_complete = 1 AND u.invitetype = 1 AND c.ip_count > 0 AND c.type = 2 AND c.ymd >= {$start_ymd}
        ";
        //上一次任务下属技术
        if($last_task_uid)
        {
            $sql .= " AND c.uid NOT IN ({$last_task_uid})";
        }
        $query_result = $this->query($sql);
        return $query_result ? $query_result : [];
    }
    
    /**
     * 获取下属推广天数
     * @param string $invitecode
     * @param integer $start_ymd
     * @param string $last_task_uid
     * @return boolean|Ambigous <number, mixed>
     */
    public function fetch_subordinate_promotion_day($invitecode,$start_ymd,$last_task_uid)
    {
        if(!$invitecode || !$start_ymd)
        {
            return [];
        }
        
        $sql = "
            SELECT c.uid,COUNT(DISTINCT c.ymd) AS day FROM user AS u INNER JOIN {$this->_realTableName} AS c ON u.id = c.uid
            WHERE u.invitecode = '{$invitecode}' AND u.info_is_complete = 1 AND c.ip_count > 0 AND u.invitetype = 1 
            AND c.type = 2 AND c.ymd >= {$start_ymd}
        ";
        //上一次任务下属技术
        if($last_task_uid)
        {
            $sql .= " AND c.uid NOT IN ({$last_task_uid})";
        }
        $sql .= " GROUP BY c.uid  HAVING day > 4";
        $query_result = $this->query($sql);
        
        return $query_result ? $query_result : [];
    }
    
    /**
     * 获取下属推广指定软件推广总量与累计天数
     * @param string $invitecode
     * @param integer $start_ymd
     * @param string $last_task_uid
     * @return boolean|Ambigous <number, mixed>
     */
    public function fetch_subordinate_promotion_specifie_software_sum($invitecode,$start_ymd)
    {
        if(!$invitecode || !$start_ymd)
        {
            return false;
        }
        
        //指定软件
        $software_list   = ["qqpcmgr","bdbrowserv2","kyzip","kyzipx"];
        $software_list   = array_map('parse_value',$software_list);
        $software_string = implode(',', $software_list);

        $sql = "
            SELECT SUM(c.ip_count) AS num FROM user AS u INNER JOIN {$this->_realTableName} AS c ON u.id = c.uid
            WHERE u.invitecode = '{$invitecode}' AND u.invitetype = 1 AND c.ip_count > 0 AND c.type = 2 AND c.ymd >= {$start_ymd} AND c.name IN({$software_string})
        ";
        $query_result = current($this->query($sql));

        return $query_result['num'] ? $query_result['num'] : 0;
    }
    
    /**
     * 获取下属推广指定软件推广累计天数
     * @param string $invitecode
     * @param integer $start_ymd
     * @param string $last_task_uid
     * @return boolean|Ambigous <number, mixed>
     */
    public function fetch_subordinate_promotion_specifie_software_day($invitecode,$start_ymd,$last_task_uid)
    {
        if(!$invitecode || !$start_ymd)
        {
            return [];
        }
        
        //指定软件
        $software_list   = ["qqpcmgr","bdbrowserv2","kyzip","kyzipx"];
        $software_list   = array_map('parse_value',$software_list);
        $software_string = implode(',', $software_list);
        
        $sql = "
            SELECT c.uid,COUNT(DISTINCT c.ymd) AS day FROM user AS u INNER JOIN {$this->_realTableName} AS c ON u.id = c.uid
            WHERE u.invitecode = '{$invitecode}' AND u.invitetype = 1 
            AND c.ip_count > 0 AND c.type = 2 AND c.ymd >= {$start_ymd} AND c.name IN({$software_string})
        ";
        //上一次任务下属技术
        if($last_task_uid)
        {
            $sql .= " AND c.uid NOT IN ({$last_task_uid})";
        }
        $sql .= " GROUP BY c.uid HAVING day > 9";
        $query_result = $this->query($sql);
        
        return $query_result ? $query_result : [];
    }
	
	    /*
     * 获取某用户的一段时间指定软件的总安装量 dateline
     *@param $array = array(
                'uid'
                'startTime'
                'endTime'
                )
              $softIdArray softid
     * */
    public function get_user_ipcount_by_param($array,$softIdArray=''){
        if($softIdArray){
            $softStr = implode("','",$softIdArray);
            $softStr = "'".$softStr."'";
            $softStr = "and name in ({$softStr})";
        }
        $sql = "select sum(ip_count) as num
                from {$this->_realTableName}
                where uid={$array['uid']} and ymd>={$array['startTime']} and delete_flag=0 and ymd<={$array['endTime']} and type=2 and is_get in (0,1) {$softStr}";
    
        return $this->query($sql);
    }
    
    
    public function get_user_ipcount_by_param_dateline($array,$softIdArray=''){
        if($softIdArray){
            $softStr = implode("','",$softIdArray);
            $softStr = "'".$softStr."'";
            $softStr = "and name in ({$softStr})";
        }
        $array['startTime'] = strtotime($array['startTime']);
        $array['endTime'] = strtotime($array['endTime']);
    
        $sql = "select sum(ip_count) as num
        from {$this->_realTableName}
        where uid={$array['uid']} and dateline>={$array['startTime']} and delete_flag=0 and dateline<={$array['endTime']} and type=2 and is_get in (0,1) {$softStr}";
    
         
        return $this->query($sql);
    }
    /**
     * 获取用户装机数据
     * @param string $uid
     * @param string $ym
     * @param string $ymd
     * @param string $tablename
     * @return array
     */
    public function fetch_user_income($uid,$ym,$ymd,$tablename = ''){
        if (empty($tablename)) $tablename = $this->_get_table_name();
        $sql = "
            SELECT SUM(CASE WHEN FROM_UNIXTIME(dateline,'%Y%m%d') = {$ymd} THEN credit END) AS yesterday_credit,
            SUM(CASE WHEN ym = {$ym} THEN credit END) AS month_credit
            FROM {$tablename}
            WHERE uid = {$uid} AND is_get <> 2
        ";
        $query_result = $this->query($sql);
        return $query_result ? current($query_result) : array();
    }
    
    /**
     * 获取用户装机数据明细
     * @param unknown $uid
     * @param unknown $ymd
     * @return \Dao\mixed
     */
    public function fetch_user_income_detail($uid,$ymd,$tablename){
        $status = ($tablename == 'credit_wait_confirm') ? '已发放' : '未发放（次月发放）';
        $sql = "
            SELECT
            SUM(ip_count) AS num,
            SUM(credit) AS credit_num,
            name,type,'{$status}' AS status,dateline
            FROM {$tablename}
            WHERE uid = {$uid}
            AND is_get <> 2
            AND credit > 0 AND FROM_UNIXTIME(dateline,'%Y%m%d')= {$ymd}
            GROUP BY name
        ";
        $query_result = $this->query($sql);
        return $query_result;
    }
    
    /**
     * 获取用户装机明细列表 分页
     * @param array $param
     * @method pengtingfang
     */
    public function fetch_user_income_detail_limit($param){
        
        if($param['type']){
            $param['type'] = " and name in(select short_name from promotion where  {$param['type']} )  ";
        }
        
        $sql = "SELECT a. * ,b.namecn from (
SELECT SUM(ip_count) AS ip_count, SUM(credit) AS credit_num, name,ymd ,dateline
FROM {$this->_realTableName} WHERE uid = {$param['uid']} AND ymd>={$param['startTime']} AND ymd<={$param['endTime']} AND is_get <> 2 AND type=2 {$param['type']} GROUP BY {$param['field']} order by  {$param['field']} desc limit {$param['firstRow']} , {$param['listRows']} 
)a , (select short_name,name namecn from promotion  ) b where a.name=b.short_name
        ";
  
        $query_result = $this->query($sql);
        return $query_result;
    }

    /**
     * 获取用户装机明细 总数
     * @param array $param
     * @method pengtingfang
     */
    public function fetch_user_income_detail_count($param){
        
        if($param['type']){
            $param['type'] = " and name in(select short_name from promotion where  {$param['type']} )  ";
        }
        
        $sql = "
        select count( a.name  ) count 
            from  ( 
                SELECT name FROM credit_wait_confirm WHERE uid = {$param['uid']}
                AND ymd>={$param['startTime']}  AND ymd<={$param['endTime']}   AND is_get <> 2   AND type=2  {$param['type']}  GROUP BY {$param['field']} 
            ) a
        ";

        $query_result = $this->query($sql);
        return $query_result[0]['count'] ?:0;
    }
    
    
    /**
     * 获取用户当月推广明细
     * @param string $uid
     * @param string $ym
     * @param string $tablename
     * @return \Dao\mixed
     */
    public function fetch_user_income_by_month_detail($uid,$ym,$tablename){
        $sql = "
            SELECT
            ymd,
            SUM(credit) AS value
            FROM %s
            WHERE uid = {$uid}
            AND is_get <> 2
            AND credit > 0 AND ym = {$ym}
            GROUP BY ymd ORDER BY ymd ASC
        ";
        $query_result = $this->query(sprintf($sql,$tablename));
        return $query_result;
    }
    
    /**
     * 获取用户当月推广明细按软件分组
     * @param unknown $uid
     * @param unknown $ym
     * @param unknown $tablename
     * @return \Dao\mixed
     */
    public function fetch_user_income_by_soft($uid,$ym,$tablename){
        $sql = "
            SELECT
            SUM(c.ip_count) AS value,
            (SELECT `name` FROM promotion WHERE short_name = c.name LIMIT 1) AS name
            FROM %s AS c
            WHERE c.uid = {$uid}
            AND c.type = 2 AND c.is_get <> 2
            AND c.ip_count > 0 AND c.ym = {$ym}
            GROUP BY c.name ORDER BY value DESC
        ";
        $query_result = $this->query(sprintf($sql,$tablename));
        return $query_result;
    }

    /**
     * 取指定月份活动积分情况
     * @param $ym
     * @return mixed
     */
    public function activity_credit_by_ym( $ym ) {
        $ym = $this->ym( $ym );
        $sql = <<<eot
select
	cm.`name`,
	cm.`desc`,
	sum(c.credit) as credit,
	c.ym
FROM
	credit_name_decs_map cm
	INNER JOIN credit_wait_confirm c on c.ym={$ym} and c.`name`=cm.`name`
GROUP BY
	cm.`name`
eot;
        return $this->query( $sql );

    }

    /**
     * 取指定区间积分收入技术员人数
     * @param $ym
     * @param $min
     * @param $max
     * @return mixed
     */
    public function technician_credit_range_info( $ym , $min, $max ) {
        $ym = $this->ym($ym);
        $min = intval($min);
        $max = intval($max);
        $sql = <<<eot
select
	count(*) as total
from
(
select
	sum(credit) as credit
FROM
	credit_wait_confirm
WHERE
	ym={$ym}
	and credit > 0
GROUP BY uid
HAVING
	credit >= {$min} and credit < {$max}
) t
eot;
        $data = $this->query( $sql );
        return !empty($data) ? $data[0]['total'] : 0;

    }

    public function ym( $ym ) {
        $ym = intval( $ym );
        if ($ym > 10000 ) $ym -= 200000;
        return $ym;
    }
	
	/**
     *@desc 春节活动签名统计
     */
    public function get_act_sign_count($uid,$ymdStart,$ymdEnd){
		$sql = "SELECT COUNT(*) AS sign_num FROM {$this->_realTableName}  WHERE NAME= 'sign' AND uid={$uid} AND ymd>={$ymdStart} AND ymd<={$ymdEnd}";		
        $list = $this->query($sql);
        return $list;
    }
    
    /**
     * 预计收入
     */
    public function expect_income($params){
        if(!$params['uid'] || !$params['ymd_start'] || !$params['ymd_end'] || !$params['limit']){
            return false;
        }
        $sql = "SELECT
                    SUM(c.credit) AS fafang_num,c.ymd,
                	IFNULL((SELECT SUM(n.credit) FROM `union`.credit_wait_confirm_no AS n WHERE c.uid = n.uid AND c.ymd = n.ymd AND n.is_get = 0 AND n.name NOT IN('ktwjf','602gmf','jsdh','hao123','sgdh','360dh')),0) AS nofafang_num
                FROM
                	`union`.credit_wait_confirm AS c
                WHERE
                	c.is_get <> 2
                AND c.delete_flag = 0
                AND c.sub_type NOT IN (2,10,11)
                AND c.uid = '{$params['uid']}'
                AND c.ymd BETWEEN {$params['ymd_start']} AND {$params['ymd_end']}
                AND c.name NOT IN('ktwjf','602gmf','jsdh','hao123','sgdh','360dh')
                GROUP BY c.ymd
                ORDER BY c.ymd DESC
                LIMIT {$params['limit']}
            ";
        $query_res = $this->query($sql);
        return $query_res;
    }
	
}
