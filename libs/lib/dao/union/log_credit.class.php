<?php

namespace Dao\Union;
use \Dao;
class Log_credit extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Log_credit
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * 装机员变成市场经理,更新市场经理下属业绩记录
     * @param integer $uid 装机员uid
     * @param integer $area_id 区域id
     * @return boolean
     */
    public function become_manager( $uid, $area_id ) {
        $uid                = intval( $uid );
        $area_id            = intval( $area_id );
        $table_log_credit   = $this->_get_table_name();
        $table_log_register = $this->_get_table_name('log_register');

        $sql = "UPDATE {$table_log_credit}
            SET area_id={$area_id}, is_stat_manager={$area_id}
            WHERE invite_uid={$uid}";
        $this->query( $sql );

        $sql_update_register = "UPDATE {$table_log_register}
            SET invite_area_id={$area_id}, is_stat_manager={$area_id}
            WHERE invite_uid={$uid}";
        $this->query( $sql_update_register );

    }

    /**
     * 同步日志
     * @param $ymd_start 开始年月日
     * @param $ymd_end 结束年月日
     * @return boolean
     */
    public function sync_log( $ymd_start = 0, $ymd_end = 0 ){
        $now            = time();
        $ymd_start      = intval( $ymd_start );
        $ymd_start      = empty( $ymd_start ) ? date('Ym01', ( strtotime( '-1 month') ) ) : $ymd_start;//取前一个月1号到当前
        $ymd_end        = intval( $ymd_end );
        $dateline_start = strtotime( $ymd_start );
        $dateline_end   = empty($ymd_end) ?  $now : strtotime($ymd_end);

        $this->_sync_credit_wait_confirm(0, $dateline_start, $dateline_end);
        $this->_sync_activity_hao123_vip_num();
        $this->_sync_activity_hao123_vip_num_new();
    }

    public function sync_credit_wait_confirm( $id ) {
        $id = intval($id);
        if (empty($id)) return false;
        $this->_sync_credit_wait_confirm( $id );
    }

    /**
     * 同步credit_wait_confirm 的记录
     * @param integer $id credit_wait_firm的id
     * @param integer $dateline_start 开始时间戳
     * @param integer $dateline_end 结束时间戳
     */
    protected function _sync_credit_wait_confirm( $id = 0, $dateline_start = 0, $dateline_end = 0 ) {
        /*
            REPLACE into log_credit (
                    id,
                    uid,
                    credit,
                    type,
                    sub_type,
                    dateline,
                    ym,
                    ymd,
                    ip_count,
                    `name`,
                    is_get,
                    promotion_id,
                    user_name,
                    invite_uid,
                    invite_user_name,
                    area_id,
                    is_stat_manager,
                    original_num,
                    software_num,
                    navigate_num
            )
            select
                    c.id,#记录id
                    c.uid,#用户id
                    c.credit,#积分
                    c.type,#主类型
                    (case when p.id = 6 OR p.id = 32 then 3 else c.sub_type end) as sub_type,#子类型
                    c.dateline,#记录产生的时间戳
                    c.ym,#年月
                    c.ymd,#年月日
                    ip_count,#安装量
                    c.`name`,#推广的资源标识名
                    c.is_get,#是否已经发放
                    p.id as promotion_id,#推广的资源id

                    u.`name` as user_name,#用户名
                    m.userid as invite_uid,#上级市场经理id
                    m.username as invite_user_name,#上级市场经理名
                    m.areaid as area_id,#城市id
                    m.is_stat_manager as is_stat_manager,#上级是否已经成为市场经理

                    f.f_num_original as original_num,#产商返回量
                    (case when p.id != 6 AND p.id != 32 then c.ip_count  else 0 end) as software_num,#软件安装实际量
                    (case when p.id = 6 OR p.id = 32 then c.ip_count else 0 end) as navigate_num#导航量

            from credit_wait_confirm c
                    LEFT JOIN  `user` u on c.uid=u.id#取用户信息
                    LEFT JOIN channel_7654.`user_marketer` m on u.invitecode = m.idcode#取上级市场经理
                    LEFT JOIN promotion p on c.`name`=p.short_name#取promotion_id
                    LEFT JOIN ad_product_record_fafang_log f on c.uid = f.f_uid and c.ymd=f.f_ymd and p.id=f.f_promotion_id#取产商反回量
            where
                  c.delete_flag=0
                  AND c.is_get <> 2
            ORDER BY id desc
         */



        $where_id       = !empty( $id ) ? " AND c.id={$id} " : '';
        $where_dateline = ( !empty($dateline_start) && !empty($dateline_end) ) ? " AND c.dateline between {$dateline_start} and {$dateline_end} " : '';
        $table_log_credit = $this->_get_table_name();

        if (  !empty($dateline_start) && !empty($dateline_end) ) {
            $hour_now = intval(date('H'));
            if ($hour_now > 1 and $hour_now < 6) {
                //只在凌晨执行删除操作
                $sql_delete_old = "
               DELETE FROM
                  {$table_log_credit}
               WHERE
                  dateline BETWEEN {$dateline_start} and {$dateline_end}
               ";
                    $this->query($sql_delete_old);
            }
        }
        $sql = "
            REPLACE into log_credit (
                    id,
                    uid,
                    credit,
                    `type`,
                    sub_type,
                    dateline,
                    ym,
                    ymd,
                    ip_count,
                    `name`,
                    is_get,
                    promotion_id,
                    user_name,
                    invite_uid,
                    invite_user_name,
                    area_id,
                    is_stat_manager,
                    original_num,
                    software_num,
                    navigate_num
            )
            select
                    c.id,#记录id
                    c.uid,#用户id
                    c.credit,#积分
                    c.type,#主类型
                    (case when p.id = 6 OR p.id = 32 then 3 else c.sub_type end) as sub_type,#子类型
                    c.dateline,#记录产生的时间戳
                    c.ym,#年月
                    c.ymd,#年月日
                    ip_count,#安装量
                    c.`name`,#推广的资源标识名
                    c.is_get,#是否已经发放
                    p.id as promotion_id,#推广的资源id

                    u.`name` as user_name,#用户名
                    m.userid as invite_uid,#上级市场经理id
                    m.username as invite_user_name,#上级市场经理名
                    m.areaid as area_id,#城市id
                    m.is_stat_manager as is_stat_manager,#上级是否已经成为市场经理

                    f.f_num_original as original_num,#产商返回量
                    (case when p.id != 6 AND p.id != 32 then c.ip_count  else 0 end) as software_num,#软件安装实际量
                    (case when p.id = 6 OR p.id = 32 then c.ip_count else 0 end) as navigate_num#导航量

            FROM credit_wait_confirm c
                    LEFT JOIN  `user` u on c.uid=u.id#取用户信息
                    LEFT JOIN channel_7654.`user_marketer` m on u.invitecode = m.idcode#取上级市场经理
                    LEFT JOIN promotion p on c.`name`=p.short_name#取promotion_id
                    LEFT JOIN ad_product_record_fafang_log f on c.uid = f.f_uid and c.ymd=f.f_ymd and p.id=f.f_promotion_id#取产商反回量
            WHERE
                  c.delete_flag=0
                  {$where_id}
                  {$where_dateline}
                  AND c.is_get <> 2
            ORDER BY
                  id desc
        ";
        $this->db()->query( $sql );
    }

    /**
     * @return bool|\mysqli_result
     */
    protected  function _sync_activity_hao123_vip_num() {
        //@todo 需要根据业务修改为增量
        $sql = "
            REPLACE INTO log_credit (
             id,
             uid,
             `type`,
             sub_type,
             dateline,
             ym,
             ymd,
             navigate_num,
             `name`,
             promotion_id,
              user_name,
             invite_uid,
             invite_user_name,
             area_id,
             is_stat_manager)
            SELECT
             (h.id + 100000000) as id,#id基数1亿
             h.uid as uid,#uid
             2 as `type`,#2:主类推广
             3 as sub_type,#3:子类导航
             h.dateline as dateline, #记录产生的时间戳
             FROM_UNIXTIME( UNIX_TIMESTAMP(h.ymd), '%y%m' ) as ym,#年月
             h.ymd as ymd,#年月日
             h.ip_count as navigate_num,#有导航有效量
             'hao123' as `name`,#推广的标识名
             6 as promotion_id,#推广的资源id

             u.`name` as user_name,#用户名

             m.userid as invite_uid,#上级市场经理id
             m.username as invite_user_name,#上级市场经理用户名
             m.areaid as area_id,#城市id
             m.is_stat_manager#上级是否已经是市场经理
            FROM activity_hao123_vip_num h
             LEFT JOIN `user` u on h.uid=u.id #用户表取用户名
             LEFT JOIN channel_7654.`user_marketer` m on u.invitecode = m.idcode #取上级市场经理信息
        ";
        return $this->db()->query( $sql );
    }

    protected function _sync_activity_hao123_vip_num_new() {
        $sql = "
            REPLACE INTO log_credit (
                id,
                uid,
                `type`,
                sub_type,
                dateline,
                ym,
                ymd,
                navigate_num,
                `name`,
                promotion_id,
                user_name,
                invite_uid,
                invite_user_name,
                area_id,
                is_stat_manager)
            SELECT
                (hn.id + 200000000) as id,#id基数1亿
                hn.uid as uid,#uid
                2 as `type`,#2:主类推广
                3 as sub_type,#3:子类导航
                hn.dateline as dateline, #记录产生的时间戳
                FROM_UNIXTIME( UNIX_TIMESTAMP(hn.ymd), '%y%m' ) as ym,#年月
                hn.ymd as ymd,#年月日
                hn.ip_count as navigate_num,#有导航有效量
                hn.`name` as `name`,#推广的标识名

                p.id as promotion_id,#推广的资源id

                u.`name` as user_name,#用户名

                m.userid as invite_uid,#上级市场经理id
                m.username as invite_user_name,#上级市场经理用户名
                m.areaid as area_id,#城市id
                m.is_stat_manager#上级是否已经是市场经理
            FROM activity_hao123_vip_num_new hn
                LEFT JOIN promotion p on hn.`name` = p.short_name #取推广的资源名称
                LEFT JOIN `user` u on hn.uid=u.id #用户表取用户名
                LEFT JOIN channel_7654.`user_marketer` m on u.invitecode = m.idcode #取上级市场经理信息
        ";
        return $this->db()->query( $sql );

    }

    /**
     * 取有业绩市场经理列表,按月分组
     * @param array $arr_area_id_list
     * @return array|mixed
     */
    public function get_manager_has_performance_ym_group(  $arr_area_id_list = array() ) {
        /**
        select count(distinct invite_uid) as manager_num, ym from log_credit
        where area_id > 0
        GROUP BY ym
         */
        $table_log_credit = $this->_get_table_name();
        $where_area_id = ( !empty($arr_area_id_list) && is_array( $arr_area_id_list) ) ? ( ' AND area_id in (' . implode(',', $arr_area_id_list) . ') ' ) : '';
        $sql = "select count(distinct invite_uid) as manager_num, ym from {$table_log_credit}
          where is_stat_manager > 0
          {$where_area_id}
          GROUP BY ym";
        $arr_data = $this->query( $sql );
        $result = array();
        foreach ($arr_data as $item ) {
            $result[ $item['ym'] ] += $item['manager_num'];
        }
        return $result;
    }

    /**
     * 取有业绩市场经理列表，按年月日分组
     * @param array $arr_area_id_list
     * @param $ymd_start
     * @param $ymd_end
     * @return array
     */
    public function get_manager_has_performance_group_ymd(  $arr_area_id_list = array(), $ymd_start, $ymd_end  ) {
        $table_log_credit   = $this->_get_table_name();
        $ymd_start          = $this->_get_ymd( $ymd_start );
        $ymd_end            = $this->_get_ymd( $ymd_end );

        $where_area_id = ( !empty($arr_area_id_list) && is_array( $arr_area_id_list) ) ? ( ' AND area_id in (' . implode(',', $arr_area_id_list) . ') ' ) : '';
        $sql = "select count(distinct invite_uid) as manager_num, ymd from {$table_log_credit}
          where is_stat_manager > 0
          AND ymd>={$ymd_start} AND ymd<={$ymd_end}
          {$where_area_id}
          GROUP BY ymd";
        $arr_data = $this->query( $sql );
        $result = array();
        foreach ($arr_data as $item ) {
            $result[ $item['ymd'] ] += $item['manager_num'];
        }
        return $result;
    }

    /**
     * 取有业绩市场经理列表
     * @param array $arr_area_id_list 城市id列表
     * @param int $ym 限定月份，默认不限
     * @param int $ymd 限定年月日,默认不限
     * @param array $arr_promotion_short_name_list 推广软件short_name 数组
     * @param int $row_begin 开始行
     * @param int $num 数量
     * @param string $user_name
     * @return array
     */
    public function get_manager_has_performance_list(  $arr_area_id_list = array(), $ym = 0, $ymd = 0, $arr_promotion_short_name_list, $row_begin = 0, $num = 10, $user_name = ''  ) {
        /**
        select count(distinct invite_uid) as manager_num, ym from log_credit
        where area_id > 0
        GROUP BY ym
         */

        $table_log_credit   = $this->_get_table_name();
        $table_user         = $this->_get_table_name('user');
        $where_area_id      = ( !empty($arr_area_id_list) && is_array( $arr_area_id_list) ) ? ( ' AND c.area_id in (' . implode(',', $arr_area_id_list) . ') ' ) : '';
        $where_short_name   = ( !empty($arr_promotion_short_name_list) && is_array( $arr_promotion_short_name_list) ) ? ' AND c.`name` in (\'' . implode('\',\'', $arr_promotion_short_name_list) . '\')' : '';
        $ym                 = intval( $ym );
        $ymd                = intval( $ymd );
        $where_ym           = '';
        if (!empty($ymd)) {
            $where_ym = ' AND c.ymd=' . $this->_get_ymd( $ymd );
        }
        elseif ( !empty($ym) ) {
            $where_ym = ' AND c.ym=' . $ym;
        }
        $where_user_name = !empty($user_name) ? " AND invite_user_name LIKE '%{$user_name}%' " : '';
        $sql = "select c.invite_uid,c.invite_user_name as user_name, u.phone from {$table_log_credit} c
          LEFT JOIN {$table_user} u on c.invite_uid=u.id
          where c.is_stat_manager > 0
          {$where_short_name}
          {$where_ym}
          {$where_area_id}
          {$where_user_name}
          GROUP by c.invite_uid
          LIMIT {$row_begin},{$num}";

        $arr_data = $this->query( $sql );
        $result = array();
        foreach ($arr_data as $item ) $result[$item['invite_uid'] ] = $item;
        return $result;
    }

    /**
     * 取有业绩市场经理总数
     * @param array $arr_area_id_list
     * @param int $ym 限定月份，默认不限
     * @param int $ymd 限定年月日，默认不限
     * @param array $arr_promotion_short_name_list
     * @param string $user_name
     * @return array|mixed
     */
    public function get_manager_has_performance_total( $arr_area_id_list = array(), $ym = 0 , $ymd = 0 , $arr_promotion_short_name_list = array(), $user_name = '' ) {
        $ym                 = intval($ym);
        $ymd                = intval($ymd);

        $table_log_credit   = $this->_get_table_name();
        $where_short_name   = !empty($arr_promotion_short_name_list) ? ' AND `name` in (\'' . implode('\',\'', $arr_promotion_short_name_list) . '\')' : '';
        $where_area_id      = ( !empty($arr_area_id_list) && is_array( $arr_area_id_list) ) ? ( ' AND area_id in (' . implode(',', $arr_area_id_list) . ') ' ) : '';

        $where_ym           = '';
        if (!empty($ymd)) {
            if ( $ymd < 20000000) $ymd += 20000000;
            $where_ym = ' AND ymd=' . $ymd;
        }
        elseif ( !empty($ym ) ) {
            $where_ym = ' AND ym=' . $ym;
        }
        $where_user_name = !empty($user_name) ? " AND invite_user_name LIKE '%{$user_name}%' " : '';

        $sql = "select count(distinct invite_uid) as manager_num from {$table_log_credit}
          where is_stat_manager > 0
          {$where_ym}
          {$where_short_name}
          {$where_area_id}
          {$where_user_name}
          ";
        $arr_data = current( $this->query( $sql ) );
        return intval( $arr_data['manager_num'] );

    }

    /**
     * 取软件安装量按月分组
     * @param array $arr_area_id_list
     * @return array|mixed
     */
    public function get_promotion_install( $arr_area_id_list = array() ) {
        /*
            select sum(ip_count) as ip_count, ym from log_credit
            where area_id > 0 and type=2
            GROUP BY ym
         */
        $table_log_credit = $this->_get_table_name();
        $where_area_id = ( !empty($arr_area_id_list) && is_array( $arr_area_id_list) ) ? ( ' AND area_id in (' . implode(',', $arr_area_id_list) . ') ' ) : '';
        $sql = "select sum(ip_count) as ip_count, ym from {$table_log_credit}
          where is_stat_manager > 0
          AND type=2
          AND `name` <> 'hao123'
          AND `name` <> '360dh'
          {$where_area_id}
          GROUP BY ym";

        $arr_data = $this->query( $sql );
        $result = array();
        foreach ($arr_data as $item ) {
            $result[ $item['ym'] ] += $item['ip_count'];
        }
        return $result;
    }

    /**
     * 取软件安装量按年月日分组
     * @param array $arr_area_id_list
     * @param $ymd_start
     * @param $ymd_end
     * @return array
     */
    public function get_promotion_install_group_ymd( $arr_area_id_list = array(), $ymd_start, $ymd_end  ) {
        $ymd_start = $this->_get_ymd( $ymd_start );
        $ymd_end = $this->_get_ymd( $ymd_end );
        $table_log_credit = $this->_get_table_name();
        $where_area_id = ( !empty($arr_area_id_list) && is_array( $arr_area_id_list) ) ? ( ' AND area_id in (' . implode(',', $arr_area_id_list) . ') ' ) : '';
        $sql = "select sum(ip_count) as ip_count, ymd from {$table_log_credit}
          where is_stat_manager > 0
          AND type=2
          AND `name` <> 'hao123'
          AND `name` <> '360dh'
          AND ymd>={$ymd_start} AND ymd<={$ymd_end}
          {$where_area_id}
          GROUP BY ymd";

        $arr_data = $this->query( $sql );
        $result = array();
        foreach ($arr_data as $item ) {
            $result[ $item['ymd'] ] += $item['ip_count'];
        }
        return $result;
    }

    /**
     * 取有业绩技术员数量
     * @param array $arr_area_id_list
     * @return array|mixed
     */
    public function get_technician_has_performance( $arr_area_id_list = array() ) {
        /*
          select count(distinct uid) as technician_num, ym from log_credit
          where area_id > 0
          GROUP BY ym
         */
        $table_log_credit = $this->_get_table_name();
        $where_area_id = ( !empty($arr_area_id_list) && is_array( $arr_area_id_list) ) ? ( ' AND area_id in (' . implode(',', $arr_area_id_list) . ') ' ) : '';
        $sql = "select count(distinct uid) as technician_num, ym from {$table_log_credit}
          where is_stat_manager > 0
          {$where_area_id}
          GROUP BY ym";

        $arr_data = $this->query( $sql );
        $result = array();
        foreach ($arr_data as $item ) {
            $result[ $item['ym'] ] += $item['technician_num'];
        }
        return $result;
    }

    /**
     * 取有业绩技术员数量,年月日分组
     * @param $arr_area_id_list
     * @param $ymd_start
     * @param $ymd_end
     * @return array
     */
    public function get_technician_has_performance_group_ymd( $arr_area_id_list, $ymd_start, $ymd_end ) {
        $table_log_credit = $this->_get_table_name();
        $ymd_start      = $this->_get_ymd( $ymd_start );
        $ymd_end        = $this->_get_ymd( $ymd_end );
        $where_area_id  = ( !empty($arr_area_id_list) && is_array( $arr_area_id_list) ) ? ( ' AND area_id in (' . implode(',', $arr_area_id_list) . ') ' ) : '';
        $sql            = "select count(distinct uid) as technician_num, ymd from {$table_log_credit}
          where is_stat_manager > 0 AND ymd>={$ymd_start} AND ymd<={$ymd_end}
          {$where_area_id}
          GROUP BY ymd";

        $arr_data       = $this->query( $sql );
        $result         = array();
        foreach ($arr_data as $item ) {
            $result[ $item['ymd'] ] += $item['technician_num'];
        }
        return $result;
    }

    /**
     *
     * 出下属绑定有业绩技术员数量
     * @param array $arr_manager_id 市场经理id数组
        manager_id1
        manager_id2
     * @param int $ym 年月
     * @param int $ymd 年月日
     * @param array $arr_promotion_short_name_list 推广软件short_name 数组

     * @return array
    manager_id1 : 技术员数量
     *
     */
    public function get_technician_has_performance_total_by_manager_id_list( $arr_manager_id, $ym = 0, $ymd = 0, $arr_promotion_short_name_list = array() ) {

        $table_log_credit = $this->_get_table_name();
        $where_ym               = '';
        if (!empty($ymd)) {
            $where_ym = ' AND ymd=' . $this->_get_ymd( $ymd );
        }
        elseif ( !empty($ym)) {
            $where_ym = ' AND ym=' . $ym;
        }

        $invite_uid = implode(',', $arr_manager_id);
        $where_short_name   = ( !empty($arr_promotion_short_name_list) && is_array( $arr_promotion_short_name_list) ) ? ' AND `name` in (\'' . implode('\',\'', $arr_promotion_short_name_list) . '\')' : '';
        $sql = "SELECT count(distinct uid) as num, invite_uid
        FROM {$table_log_credit}
        WHERE invite_uid in($invite_uid)
        {$where_ym}
        {$where_short_name}
        GROUP BY invite_uid
        ";
        $arr_result = $this->query( $sql );
        $result = array();
        foreach ( $arr_result as $item ) {
            $result[$item['invite_uid']] = $item['num'];
        }
        return $result;
    }

    /**
     *
     * 取下属安装总量
     * @param array $arr_uid_list 邀请者uid数组
        uid1
        uid2
     * @param int $ym 年月
     * @param int $ymd 年月日
     * @param array $arr_promotion_short_name_list 推广软件short_name 数组
     * @return array
        uid1 : 安装量
     *
     */
    public function get_promotion_install_total_by_uid_list ( $arr_uid_list, $ym = 0, $ymd = 0, $arr_promotion_short_name_list = array() ) {
        $table_log_credit   = $this->_get_table_name();

        $where_ym               = '';
        if ( !empty($ymd) ) {
            $where_ym = ' AND ymd=' . $ymd = $this->_get_ymd( $ymd );
        }
        elseif ( !empty($ym)) {
            $where_ym = ' AND ym=' . $ym;
        }

        $uid = implode(',', $arr_uid_list );
        $where_short_name   = ( !empty($arr_promotion_short_name_list) && is_array( $arr_promotion_short_name_list) ) ? ' AND `name` in (\'' . implode('\',\'', $arr_promotion_short_name_list) . '\')' : '';
        $sql = "SELECT sum( ip_count ) as num, invite_uid
        FROM {$table_log_credit}
        WHERE
        type = 2
        AND name <> 'hao123'
        AND name <> '360dh'
        AND invite_uid in( $uid )
        {$where_ym}
        {$where_short_name}
        GROUP BY uid
        ";
        $arr_result = $this->query( $sql );
        $result = array();
        foreach ( $arr_result as $item ) {
            $result[$item['invite_uid']] += $item['num'];
        }
        return $result;
    }
    
    /**
     * @todo
     * 取有业绩的邀请者，他或下家有业绩
     * @param int $ym 年月
     * @param int|array $ymd 年月日,值为单数值限定年月日，值为数组年月日范围，下标0开始年月日,下标1结束年月日
     * @param array $arr_in_user_id 指定用户
     * @return array
     */
    public function get_invite_has_performance($ym = 0, $ymd = 0, $arr_in_user_id = array()) {
        $table_log_credit   = $this->_get_table_name();
        $arr_where          = array();

        if (!empty($ymd)) {
            if ( is_array( $ymd ) ) {
                $arr_where['ymd_start']  = ' ymd>=' . $this->_get_ymd( $ymd[0] );
                $arr_where['ymd_end']    = ' ymd<=' . $this->_get_ymd( $ymd[1] );
            }
            else {
                $arr_where['ymd'] = 'ymd=' . $ymd = $this->_get_ymd($ymd);
            }

        }
        elseif ( !empty($ym)) {
            $arr_where['ym'] = 'ym=' . $ym;
        }

        if ( !empty( $arr_in_user_id ) ) {
            $invite_uid                 = implode(',', $arr_in_user_id);
            $arr_where['invite_uid']   = 'invite_uid IN (' . $invite_uid . ')';
        }

        $where = !empty($arr_where) ? ( ' WHERE ' . implode(' AND ', $arr_where) ) : '';
        $sql = "SELECT count(distinct uid) as num, invite_uid
        FROM {$table_log_credit}
        {$where}";

        $arr_data = current($this->query( $sql ));
        return !empty($arr_data) ? $arr_data['num'] : 0;
    }



    /**
     * 统计有业绩市场经理
     * @param int $ym
     * @param int|array $ymd
     * @param array $arr_in_user_id 限定的市场经理uid
     * @return int
     */
    public function count_manager_has_performance( $ym = 0, $ymd = 0, $arr_in_user_id = array()) {

        $table_log_credit   = $this->_get_table_name();
        $arr_where          = array(
            'invite_area_id' => 'is_stat_manager > 0'
        );

        if ( !empty($ymd) ) {
            if ( is_array( $ymd ) ) {
                $arr_where['ymd_start']  = ' ymd>=' . $this->_get_ymd( $ymd[0] );
                $arr_where['ymd_end']    = ' ymd<=' . $this->_get_ymd( $ymd[1] );
            }
            else {
                $arr_where['ymd'] = 'ymd=' . $ymd = $this->_get_ymd($ymd);
            }

        }
        elseif ( !empty($ym)) {
            $arr_where['ym'] = 'ym=' . $ym;
        }

        if ( !empty( $arr_in_user_id ) ) {
            $invite_uid                 = implode(',', $arr_in_user_id);
            $arr_where['invite_uid']   = 'invite_uid IN (' . $invite_uid . ')';
        }
        $where = !empty($arr_where) ? ( ' WHERE ' . implode(' AND ', $arr_where) ) : '';
        $sql = "select count(distinct invite_uid) as manager_num from {$table_log_credit}
         {$where}
          ";
        $arr_data = current( $this->query( $sql ) );
        return intval( $arr_data['manager_num'] );
    }

    /**
     * 统计有业绩市场经理
     * @param int $ym
     * @param int $ymd
     * @param array $arr_in_user_id 限定的市场经理uid
     * @return int
     */
    public function count_technician_has_performance( $ym = 0, $ymd = 0, $arr_in_user_id = array() ) {

        $table_log_credit   = $this->_get_table_name();
        $arr_where          = array(
            'invite_area_id' => 'is_stat_manager > 0'
        );

        if ( !empty($ymd) ) {
            if ( is_array( $ymd ) ) {
                $arr_where['ymd_start']  = ' ymd>=' . $this->_get_ymd( $ymd[0] );
                $arr_where['ymd_end']    = ' ymd<=' . $this->_get_ymd( $ymd[1] );
            }
            else {
                $arr_where['ymd'] = 'ymd=' . $ymd = $this->_get_ymd($ymd);
            }

        }
        elseif ( !empty($ym)) {
            $arr_where['ym'] = 'ym=' . $ym;
        }

        if ( !empty( $arr_in_user_id ) ) {
            $invite_uid                 = implode(',', $arr_in_user_id);
            $arr_where['invite_uid']   = 'invite_uid IN (' . $invite_uid . ')';
        }
        $where = !empty($arr_where) ? ( ' WHERE ' . implode(' AND ', $arr_where) ) : '';
        $sql = "select count(distinct uid) as num from {$table_log_credit}
         {$where}
          ";
        $arr_data = current( $this->query( $sql ) );
        return intval( $arr_data['num'] );
    }

    /**
     * 统计安装量
     * @param int $ym
     * @param int|array $ymd
     * @param array $arr_in_user_id 限定的市场经理uid
     * @return integer
     */
    public function count_install_num( $ym = 0, $ymd = 0, $arr_in_user_id = array() ) {
        $table_log_credit   = $this->_get_table_name();
        $arr_where          = array(
            'invite_area_id' => 'is_stat_manager > 0'
        );

        if ( !empty($ymd) ) {
            if ( is_array( $ymd ) ) {
                $arr_where['ymd_start']  = ' ymd>=' . $this->_get_ymd( $ymd[0] );
                $arr_where['ymd_end']    = ' ymd<=' . $this->_get_ymd( $ymd[1] );
            }
            else {
                $arr_where['ymd'] = 'ymd=' . $ymd = $this->_get_ymd($ymd);
            }

        }
        elseif ( !empty($ym)) {
            $arr_where['ym'] = 'ym=' . $ym;
        }

        if ( !empty( $arr_in_user_id ) ) {
            $invite_uid                 = implode(',', $arr_in_user_id);
            $arr_where['invite_uid']   = 'invite_uid IN (' . $invite_uid . ')';
        }
        $where = !empty($arr_where) ? ( ' WHERE ' . implode(' AND ', $arr_where) ) : '';
        $sql = "select sum(ip_count) as num from {$table_log_credit}
         {$where}
          ";
        $arr_data = current( $this->query( $sql ) );
        return intval( $arr_data['num'] );
    }



    
    /**
     * @desc 通过市场经理uid 取有业绩的市场经理;
     * @param type $arr_uid_list
     * @return array(0=>uid,)
    
    public function get_manager_uid_by_promotion($arr_uid_list,$soft_id){
        $where_uid = implode(',', $arr_uid_list);
        $sql = "SELECT uid FROM log_credit WHERE name='{$soft_id}' uid IN ($where_uid);";
        $arr_result = $this->query( $sql );
        return $arr_result ? $arr_result : array();
    }
   
     * @desc 通过有业绩的技术员uid 取有业绩的上级;
     * @param string $soft_id
     * @return array(0=>uid,)
    
    public function get_invite_uid_by_promotion($soft_id){
        $sql = "SELECT DISTINCT(invite_uid) FROM log_credit WHERE name='{$soft_id}'";
        $arr_result = $this->query( $sql );
        return $arr_result ? $arr_result : array();
    }
     */
    
    /**
     * @desc 通过有业绩的技术员uid 取邀请人;
     * @desc area_id>0 or refer_type = 2
     * @param string $soft_id
     * @return array(0=>uid,)
     */
    public function get_manager_uid_by_promotion_area_id($soft_id){
        $sql = "SELECT DISTINCT(invite_uid)  FROM {$this->_get_table_name()} WHERE area_id>0 AND name='{$soft_id}'";
        #echo $sql;
        $arr_result = $this->query( $sql );
        return $arr_result ? $arr_result : array();
    }
    

    /**
     * @desc 取有业绩的技术员uid;
     * @param string $soft_id
     * @return int $result
     */
    public function get_promotion_uid_total_by_softid($soft_id){
        $sql = "SELECT DISTINCT(uid)  FROM {$this->_get_table_name()} WHERE  name='{$soft_id}'";
        #echo $sql;
        $arr_result = $this->query( $sql );
        return $arr_result ? $arr_result : array();
    }
    
    /**
     * @desc 通过城市id，软件id，获取软件明细信息
     * @param string $soft_id
     * @param array $arr_area_id
     * @return array $result
     */
    public function get_softid_manager_detail($soft_id,$arr_area_id = array(),$start_time,$end_time,$start,$limit){
        $str_area_id = implode(',', $arr_area_id);
        if($str_area_id){
            $where_area = "AND area_id IN ({$str_area_id})";
        }else{
            $where_area = "AND is_stat_manager>0";
        }
        $sql = "SELECT ymd,COUNT(DISTINCT(invite_uid)) AS manager_total,COUNT(DISTINCT(uid)) AS user_total ,SUM(ip_count) AS performance_total, SUM(original_num) AS total_org FROM {$this->_get_table_name()}
            WHERE NAME='{$soft_id}' {$where_area} AND ymd>={$start_time} AND ymd<={$end_time} GROUP BY ymd ORDER BY ymd DESC LIMIT {$start},{$limit}";
        #echo $sql;
        $arr_result = $this->query( $sql );
        return $arr_result ? $arr_result : array();
    }
    
    /**
     * @desc 通过城市id，软件id，获取软件明细总数
     * @param string $soft_id
     * @param array $arr_area_id
     * @return array $result
     */
    public function get_softid_manager_detail_total($soft_id,$arr_area_id = array(),$start_time,$end_time){
        $str_area_id = implode(',', $arr_area_id);
        if($str_area_id){
            $where_area = "AND area_id IN ({$str_area_id})";
        }else{
            $where_area = "AND is_stat_manager>0";
        }
        $sql = "SELECT count(1) as total FROM {$this->_get_table_name()} WHERE NAME='{$soft_id}'  {$where_area} AND ymd>={$start_time} AND ymd<={$end_time} GROUP BY ymd";
        #echo $sql;
        $arr_result = $this->query( $sql );
        return $arr_result ? count($arr_result) : 0;
    }

    /**
     * 年月日转换
     * @param $ymd
     * @return integer
     */
    protected function _get_ymd( $ymd ) {
        $ymd = intval($ymd);
        return $ymd < 20000000 ? ($ymd + 20000000) : $ymd;
    }
    
    
    /**
     * @desc 通过ymd，获取市场经理明细信息
     * @param string $soft_id
     * @param array $arr_area_id
     * @return array $result
     */
    public function get_softid_manager_detail_by_ymd($soft_id,$ymd,$arr_area_id = array(),$column = 'user_total',$updown = 'DESC', $user_name = ''){
        $str_area_id = implode(',', $arr_area_id);
        if($str_area_id){
            $where_area = "AND area_id IN ({$str_area_id})";
        }else{
            $where_area = "AND is_stat_manager>0";
        }
        $where_user_name = !empty($user_name) ? " AND invite_user_name LIKE '%{$user_name}%' " : '';
        $sql = "SELECT invite_user_name,invite_uid,COUNT(DISTINCT(uid)) AS user_total ,SUM(ip_count) AS performance_total, SUM(original_num) AS total_org FROM {$this->_get_table_name()}
            WHERE NAME='{$soft_id}' {$where_area} {$where_user_name} AND ymd={$ymd} GROUP BY invite_uid ORDER BY {$column} {$updown}";
        #echo $sql;
        $arr_result = $this->query( $sql );
        return $arr_result ? $arr_result : array();
    }
    
   /**
    * 通过经理人ID获取他的技术员推广信息
    * @param integer $user_id
    * @param integer $start_time
    * @param integer $end_time
    * @return array  $query_results
    */
    public function get_technician_credit_by_invite_uid( $user_id , $start_time = 0 , $end_time = 0 , $promotion_id = '', $start_day = 0 , $end_day = 0) {
        //查询条件
        $table_log_credit   = $this->_get_table_name();
        $table_user         = $this->_get_table_name('user');
        $condition = $this->_get_technician_credit_where( $user_id , $start_time , $end_time , $promotion_id );
        //$limit = is_int($row_begin) ? "LIMIT {$row_begin},{$num}" : '';
        
        //获取绑定技术员总数（通过用户invitecode分组）
        //用户查询条件
        $user_where = "a.`id` = '{$user_id}'";
        $s_time = strtotime($start_day);
        $e_time = strtotime($end_day);
        if( $start_day && $end_day ) {
            $user_where .= " AND b.`bind_dateline` BETWEEN {$s_time} AND {$e_time}";
        }
        
        //历史安装软件总量
        $history_software_total_where = "`invite_uid` = {$user_id} AND `is_stat_manager` > 0 AND `name` <> 'hao123' AND `name` <> '360dh'  AND type =2";
        $history_software_total_sql = "SELECT  SUM( `ip_count` ) AS history_software_total FROM {$table_log_credit} WHERE {$history_software_total_where}";
        $history_software_total = current( $this->query( $history_software_total_sql ) );
        
        $st_time =  $start_day > 20000000 ? ($start_day - 20000000) : $start_day;
        $ed_time =  $end_day > 20000000 ? ($end_day - 20000000) : $end_day;
        
        $total_bind_user_sql = "SELECT COUNT(uid) AS total_bind_user FROM union.log_register WHERE invite_uid = {$user_id} AND ymd BETWEEN {$st_time} AND {$ed_time}";
        $total_bind_user = current( $this->query( $total_bind_user_sql ) );
        
        //获取各个软件安装总量
        $software_total_condition = $this->_get_technician_credit_where( $user_id , $start_day , $end_day , $promotion_id ,false);
        $software_total_sql = "SELECT `name`, SUM( `ip_count` ) AS software_total FROM {$table_log_credit} {$software_total_condition} GROUP BY name";
        $software_group_name = $this->query( $software_total_sql );
        if( !$software_group_name ){
            return array(
                'history_software_total' => $history_software_total['history_software_total']
            );
        }
        
        //获取当前时间段软件安装总量
        $software_all_condition = $this->_get_technician_credit_where( $user_id , $start_day , $end_day , $promotion_id ,false);
        $software_all_total_sql = "SELECT SUM( `ip_count` ) AS software_total FROM {$table_log_credit} {$software_all_condition}";
        $software_all_total = current( $this->query( $software_all_total_sql ) );
        
        //获取软件推广总量(按时间分组)
        $field     = "`ymd`, SUM( `ip_count` ) AS software_total";
        $sql       = "SELECT {$field} FROM {$table_log_credit} {$condition} GROUP BY `ymd` ORDER BY `ymd` DESC";
        $software_total   = $this->query( $sql );
        
        //获取绑定技术员总数（通过用户时间分组）
        /* $total_technician_sql = "SELECT COUNT(id) AS total_technician,A.ymd FROM (
                    SELECT a.id AS fid,b.id,DATE_FORMAT(FROM_UNIXTIME(b.reg_dateline),'%Y%m%d') AS ymd
                    FROM union.user a LEFT JOIN union.user b
                    ON a.idcode=b.invitecode WHERE $user_where {$limit}) AS A GROUP BY  A.ymd"; */
        
        $total_technician_sql = "SELECT COUNT(uid) AS total_technician,ymd FROM union.log_register WHERE invite_uid = {$user_id} AND ymd BETWEEN {$st_time} AND {$ed_time} GROUP BY ymd";
        $total_technician = $this->query( $total_technician_sql );
       
        //获取软件推广明细(按软件id、时间分组)
        $condition      = $this->_get_technician_credit_where( $user_id , $start_time , $end_time , $promotion_id ,true);
        $detail_field   = "l.`ymd`,p.`name`,p.`short_name`,SUM( l.`ip_count` ) AS detail_total";
        $detail_sql     = "SELECT {$detail_field} FROM {$table_log_credit} AS l
                           LEFT JOIN {$this->_get_table_name('promotion')} AS p 
                           ON l.promotion_id = p.id {$condition} 
                           GROUP BY l.`ymd`,l.`promotion_id` 
                           ORDER BY l.`ymd` DESC";
        
        $software_group_ymd_total  = $this->query( $detail_sql );
        if(!$software_total) {
            return array(
                'history_software_total' => $history_software_total['history_software_total']
            );
        }
        
        return array(
            'history_software_total' => $history_software_total['history_software_total'],
            'software_total' => $software_total , 
            'software_group_ymd_total' => $software_group_ymd_total,
            'software_group_name' => $software_group_name,
            'total_technician' => $total_bind_user['total_bind_user'],
            'total_technician_group_time' => $total_technician,
            'software_all_total'   => $software_all_total['software_total'],
        );
    }
    
    /**
    * 获取技术员推广信息查询条件
    * @param integer $user_id
    * @param integer $start_time
    * @param integer $end_time
    * @return array  $query_results
    * @return string
     */
    private function _get_technician_credit_where( $user_id , $start_time = 0 , $end_time = 0 , $promotion_id = '' ,$join = false) {
        
        $l = $join ? 'l.' : '';
        $p = $join ? 'p.' : '';
        //查询条件
        $condition = "WHERE {$l}`invite_uid` = {$user_id} AND {$l}`is_stat_manager` > 0 AND {$p}`name` <> 'hao123' and {$p}`name` <> '360dh' AND {$l}`type` =2 ";
        if( $promotion_id ) {
            $condition .= " AND {$l}`promotion_id` IN ({$promotion_id} )";
        }
        if( $start_time && $end_time ) {
            $condition .= " AND {$l}`ymd` BETWEEN {$start_time} AND {$end_time}";
        }
        return $condition;
    }
    
    /**
    * 通过经理人ID获取他的技术员推广信息总量
    * @param integer $user_id
    * @param integer $start_time
    * @param integer $end_time
    * @return array  $query_results
    * @return string
     */
    public function get_technician_credit_total_by_invite_id( $user_id , $start_time = 0 , $end_time = 0 , $promotion_id = '' ) {
        $table_log_credit   = $this->_get_table_name();
        $condition = $this->_get_technician_credit_where( $user_id , $start_time , $end_time , $promotion_id);
        //获取软件推广总量(按时间分组)
        $sql   = "SELECT COUNT( DISTINCT( `ymd` ) ) AS credit_num FROM {$table_log_credit} {$condition}  ORDER BY `ymd` DESC";
        $query_result   = current( $this->query( $sql ) );

        return intval( $query_result['credit_num'] );
    }

    /**
     * 有业绩且资料完整技术员数量
     * @param $invite_uid
     * @param $ymd_start
     * @param $ymd_end
     * @param int $info_is_complete
     */
    public function get_technician_has_performance_count($invite_uid, $ym = 0, $ymd_start = 0, $ymd_end = 0, $info_is_complete = 1, $from_manager = 1) {
//        $dateline_start     = strtotime( $ymd_start);
//        $dateline_end       = strtotime( $ymd_end );
        $table_log_credit           = $this->_get_table_name();
        $table_user                 = $this->_get_table_name('user');

        $where_info_is_complete     = !empty($info_is_complete) ? ' AND  u.info_is_complete > 0 ' : '';
        $where_ym                   = !empty($ym) ? " AND c.ym ={$ym} " : '';
        $where_ymd                  = ( !empty($ymd_start) && !empty($ymd_end) ) ? " AND c.ymd BETWEEN {$ymd_start} AND {$ymd_end} " : '';
        $where_from_manager         = !empty($from_manager) ? ' AND u.invitetype = 1 ' : '';
        $sql = "
        SELECT
            count(distinct uid) as total
        FROM {$table_log_credit} c
            LEFT JOIN {$table_user} u on c.uid = u.id
        WHERE
          c.invite_uid = {$invite_uid}
          {$where_from_manager}
          {$where_ym}
          {$where_ymd}
          AND c.type = 2
          AND c.ip_count > 0
          {$where_info_is_complete}
        ";
        $arr_data = current($this->query($sql));
        return $arr_data['total'];
    }


    public function get_software_install_num( $invite_uid, $ym = 0) {
        $table_log_credit           = $this->_get_table_name();
        $where_ym                   = !empty($ym) ? " AND c.ym = {$ym} " : '';
        $sql = "
        SELECT
            sum(software_num) as total
        FROM {$table_log_credit} c
            LEFT JOIN `user` u on c.uid=u.id
        WHERE
          c.invite_uid = {$invite_uid}
          AND c.type = 2
          AND c.software_num > 0
          AND u.invitetype = 1
          {$where_ym}
        ";
        $ret = $this->query( $sql );
        $arr_data = current( $ret );
        return $arr_data['total'];
    }

    /**
     * 市场经理名下导航量月平均值
     * @param $manager_uid
     * @param $ym
     * @return float
     */
    public function avg_manager_navigate_month( $manager_uid, $ym ) {
        if (empty($ym) ) return 0;
        $total      = $this->count_manager_navigate( $manager_uid, $ym );
        $day_total  = date('ym') == $ym ? ( (date('ymd') - ($ym . '01'))  + 1 ) : \Util\Datetime::count_month_days($ym);
        $avg        = round( $total / $day_total );
        return $avg;
    }

    /**
     * 市场经理名下导航量
     * @param $manager_uid
     * @param int $ym
     * @return mixed
     */
    public function count_manager_navigate( $manager_uid, $ym = 0 ) {
        $table_log_credit           = $this->_get_table_name();
        $where_ym                   = !empty($ym) ? " AND c.ym = {$ym} " : '';
        $sql = "
        SELECT
            sum(c.navigate_num) as total
        FROM {$table_log_credit} c
            LEFT JOIN `user` u on c.uid=u.id
        WHERE
          c.invite_uid = {$manager_uid}
          AND c.type = 2
          AND c.sub_type = 3
          AND c.navigate_num > 0
          AND u.invitetype = 1
          {$where_ym}
        ";
        $ret = $this->query( $sql );
        $arr_data = current( $ret );
        return $arr_data['total'];
    }

    public function get_software_install_list_by_manager_id( $invite_uid, $ym = 0 ) {
        $table_log_credit           = $this->_get_table_name();
        $where_ym                   = !empty($ym) ? " AND c.ym = {$ym} " : '';
        $sql = "
        SELECT
            c.`name` as short_name, sum(software_num) as software_num
        FROM {$table_log_credit} c
            LEFT JOIN `user` u on c.uid=u.id
        WHERE
          c.invite_uid = {$invite_uid}
          AND c.type = 2
          AND c.software_num > 0
          AND u.invitetype = 1
          {$where_ym}
        GROUP by c.`name`
        ";
        $arr_data = $this->query( $sql );
        return $arr_data;
    }

    /**
     * 取有业绩且资料完整技术员列表
     * @param $invite_uid
     * @param $ymd_start
     * @param $ymd_end
     * @param $order
     * @param $asc
     * @return array
     */
    public function get_technician_has_performance_list( $invite_uid , $ymd_start, $ymd_end, $order = 'credit', $asc = 0 ) {

        $invite_uid             = intval($invite_uid);
        $table_log_credit       = $this->_get_table_name();
        $table_user             = $this->_get_table_name('user');
        $asc                    = $this->_get_asc_flag( $asc );
        if (!in_array($order, ['credit', 'software_num', 'info_is_complete'])) $order = 'credit';
        $sql = "
            SELECT
              uid,
              user_name,
              sum(c.software_num) as software_num,
              sum(c.credit) as credit,
              sum(c.navigate_num) as navigate_num,
              u.info_is_complete as info_is_complete
            FROM {$table_log_credit} c
              LEFT JOIN `user` u ON c.uid=u.id
            WHERE
              invite_uid = {$invite_uid}
              AND ymd BETWEEN {$ymd_start} AND {$ymd_end}
              AND c.type = 2
              and c.ip_count > 0
              AND u.invitetype = 1
            GROUP BY uid
            ORDER BY {$order} {$asc};
        ";
        $arr_data = $this->query( $sql );
//        \Io::fb($this->get_error());
        return $arr_data;
    }


    /**
     * 分页取技术员推广详情
     * @param $uid
     * @param $ymd_promotion_start
     * @param $ymd_promotion_end
     * @param int $row_start
     * @param int $row_num
     * @param string $order
     * @param int $asc
     * @return array
     */
    public function get_technician_promotion_page( $uid,$ymd_promotion_start, $ymd_promotion_end,
                                                  $row_start = 0, $row_num = 10, $order = 'ymd', $asc = 0) {
        $uid                            = intval($uid);
        if ( !in_array($order, ['ymd','credit', 'software_num', 'navigate_num', 'info_is_complete']) ) $order = 'ymd';
        $uid                            = intval($uid);
        $table_log_credit               = $this->_get_table_name();
        $asc = $this->_get_asc_flag( $asc );

        $sql_total = "
            SELECT
                count(distinct ymd) as total
            FROM {$table_log_credit} c
                LEFT JOIN `user` u on c.uid=u.id
            WHERE
               c.uid = {$uid}
               AND c.ymd BETWEEN {$ymd_promotion_start} AND {$ymd_promotion_end}
               AND u.invitetype = 1
        ";
        $arr_data = current( $this->query($sql_total) );
        $total    = $arr_data['total'];
        $sql = "
            SELECT
                sum(c.software_num) AS software_num ,#软件实际有效量
                sum(c.credit) AS credit,#积分
                sum(c.navigate_num) AS navigate_num, #导航有效量
                c.ymd as ymd,#推广年月日
                c.user_name,#推广用户名
                UNIX_TIMESTAMP(c.ymd) as dateline_ymd #推广年月日时间戳
            FROM {$table_log_credit} c
                LEFT JOIN `user` u on c.uid=u.id
            WHERE
              c.uid = {$uid}
              AND c.ymd BETWEEN {$ymd_promotion_start} AND {$ymd_promotion_end}
              AND u.invitetype = 1
            GROUP BY c.ymd
            ORDER BY {$order} {$asc}
            LIMIT {$row_start},{$row_num};
        ";
        $arr_data = $this->query( $sql );
        $this->_fill_idx($arr_data, $row_start);
        return array(
            'total' => $total,
            'list'  => $arr_data
        );
    }

    /**
     * @param $invite_uid
     * @param int $uid
     * @param $ymd_promotion_start
     * @param $ymd_promotion_end
     * @param int $row_start
     * @param int $row_num
     * @param string $order
     * @param integer $asc 升降序，1：升序，0：降序，默认0
     * @return mixed
     */
    public function get_technician_by_invite_uid_page( $invite_uid, $uid = 0,$ymd_promotion_start, $ymd_promotion_end,
                                                       $row_start = 0, $row_num = 10, $order = 'credit', $asc = 0 ,$info_is_complete = '',$status = '') {
        $invite_uid                     = intval($invite_uid);
        $uid                            = intval($uid);
        $table_log_credit               = $this->_get_table_name();
        $table_user                     = $this->_get_table_name('user');
        $table_user_info_ext            = $this->_get_table_name('user_info_ext');
        $table_user_marketer_children   = $this->_get_table_name('user_marketer_children', \Lib\Core::config('DB_CHANNEL_7654'));
        if ( !in_array($order, ['credit', 'software_num', 'navigate_num', 'info_is_complete']) ) $order = 'credit';
        $asc = $this->_get_asc_flag( $asc );
        $where_uid =  !empty($uid) ?  "AND c.uid={$uid}"  :  '';
        //用户资料是否完整
        $where_info_is_complete = is_numeric($info_is_complete) && in_array($info_is_complete,array(0,1)) ? " AND u.info_is_complete = {$info_is_complete}" : '';
        //用户是否黑名单
        if(is_numeric($status) && in_array($status,array(0,1))){
            if($status == 0){
                $where_status = " AND u.status > 0";
            }else{
                $where_status = " AND u.status = 0";
            }
        }

        $sql_total = "
            SELECT
                count(distinct uid) as total
            FROM {$table_log_credit} c
                LEFT JOIN `user` u on c.uid=u.id
            WHERE
              c.invite_uid = {$invite_uid}
              {$where_uid}
              {$where_info_is_complete}
              {$where_status}
               AND c.ymd BETWEEN {$ymd_promotion_start} AND {$ymd_promotion_end}
               AND u.invitetype = 1
               AND c.type=2
               AND c.ip_count > 0
        ";
        $arr_data = current( $this->query($sql_total) );
        $total    = $arr_data['total'];
        $sql = "
            SELECT
                c.uid,
                c.user_name,
                sum(c.software_num) AS software_num ,#软件实际有效量
                sum(c.credit) AS credit,#积分
                sum(c.navigate_num) AS navigate_num, #导航有效量
                u.bind_dateline AS bind_dateline,#绑定时间戳
                u.last_login_time AS dateline_last_login, #最后登录时间戳
                u.info_is_complete AS info_is_complete,  #资料是否完整
                u.status,#是否是黑名单
                (select `name` from {$table_user_info_ext} where uid=c.uid ) AS info_user_name, #自己填资料的真实姓名
                (select `realname` from {$table_user_marketer_children} where userid=c.uid ) AS manager_set_user_name #市场经理填写的真实姓名
            FROM {$table_log_credit} c
                LEFT JOIN `user` u on c.uid=u.id
            WHERE
              c.invite_uid = {$invite_uid}
              {$where_uid}
              {$where_info_is_complete}
              {$where_status}
              AND c.ymd BETWEEN {$ymd_promotion_start} AND {$ymd_promotion_end}
              AND u.invitetype = 1
              AND c.type=2
              AND c.ip_count > 0
            GROUP BY uid
            ORDER BY {$order} {$asc}
            LIMIT {$row_start},{$row_num};
        ";
        $arr_data = $this->query( $sql );
        foreach ( $arr_data as $key => $item ) {
            $arr_data[$key]['real_name'] = !empty($item['manager_set_user_name']) ? $item['manager_set_user_name']
                : (!empty($item['info_user_name']) ? $item['info_user_name'] : '');
        }

        foreach ($arr_data as $row ) $uid_list[] = $row['uid'];
        $data_list = $this->promotion_day_count(  $uid_list  );//取总推广天数
        $data_ym_list = $this->promotion_day_count( $uid_list, date('ym'));//月推广天数
        foreach ( $arr_data as &$row ) {
            $row['promotion_day_count'] = isset($data_list[$row['uid']]) ? $data_list[$row['uid']] : 0;
            $row['promotion_ym_day_count'] = isset($data_ym_list[$row['uid']]) ? $data_ym_list[$row['uid']] : 0;
        }

        $this->_fill_idx( $arr_data, $row_start );
        return array(
            'total' => $total,
            'list'  => $arr_data
        );
    }


    /**
     * 取推广天数
     * @param $uids_list
     * @param $ym
     * @return mixed
     */
    public function promotion_day_count($uid_list, $ym = null) {
        $table  = $this->_get_table_name();
        $ym     = intval( $ym );
        $uids   = $this->_field_to_str( $uid_list );

        $where  = '';
        if ( !empty($uids) ) $where  = " AND uid in({$uids}) ";
        if ( !empty($ym) ) $where .= " AND ym={$ym}";

        $sql = "
        select
            uid,
            count(DISTINCT ymd) as  promotion_day_count
        from
            {$table}
        WHERE
            type = 2
            and ip_count > 0
            {$where}
        GROUP by
            uid
        ";
        foreach ( $this->query( $sql ) as $row ) $result[$row['uid']] = $row['promotion_day_count'];
        return $result;
    }

    /**
     * 无业绩装机员
     * @param $invite_uid
     * @param int $uid
     * @param $ymd_promotion_start
     * @param $ymd_promotion_end
     * @param int $row_start
     * @param int $row_num
     * @param string $order
     * @param int $asc
     * @return array
     */
    public function get_technician_by_invite_uid_no_performance_page( $invite_uid, $uid = 0,$ymd_promotion_start, $ymd_promotion_end,
                                                       $row_start = 0, $row_num = 10, $order = 'credit', $asc = 0 ,$info_is_complete ='',$status = '') {
        $result                         = array(
                'list'  => array(),
                'total' => 0,
        );
        $invite_uid                     = intval($invite_uid);
        $uid                            = intval($uid);
        $table_log_credit               = $this->_get_table_name();
        $table_log_register             = $this->_get_table_name('log_register');
        $table_user                     = $this->_get_table_name('user');
        $table_user_info_ext            = $this->_get_table_name('user_info_ext');
        $table_user_marketer_children   = $this->_get_table_name('user_marketer_children', \Lib\Core::config('DB_CHANNEL_7654'));
        if ( !in_array($order, ['credit', 'software_num', 'navigate_num', 'info_is_complete']) ) $order = 'credit';
        $asc = $this->_get_asc_flag( $asc );
        //用户资料是否完整
        $where_info_is_complete = is_numeric($info_is_complete) && in_array($info_is_complete,array(0,1)) ? " AND u.info_is_complete = {$info_is_complete}" : '';
        //用户是否是黑名单
        if(is_numeric($status) && in_array($status,array(0,1))){
            if($status == 0){
                $where_status = " AND u.status > 0";
            }else{
                $where_status = " AND u.status = 0";
            }
        }

        $sql_uid_has_performance = "
            SELECT
              uid
            FROM {$table_log_credit} c
                LEFT JOIN {$table_user} u on c.uid=u.id
            WHERE
              c.invite_uid = {$invite_uid}
               AND c.ymd BETWEEN {$ymd_promotion_start} AND {$ymd_promotion_end}
               AND u.invitetype = 1
               {$where_info_is_complete}
               {$where_status}
               AND c.ip_count > 0
               AND c.type=2
            GROUP BY c.uid
        ";
        $arr_uid    = array();
        $arr_data   =  $this->query( $sql_uid_has_performance );
        if ( !empty($arr_data) ) foreach ( $arr_data as $item ) $arr_uid[] = $item['uid'];


        $uids       = implode(',', $arr_uid);
        $where_uids = !empty($uids) ? " AND r.uid not in({$uids})" : '';
        $where_uid =  !empty($uid) ?  "AND r.uid={$uid}"  :  '';
        $sql_total = "
            SELECT
                count(*) as total
            FROM {$table_log_register} r
                LEFT JOIN {$table_user} u on r.uid=u.id
            WHERE
                r.invite_uid = {$invite_uid}
                #AND r.ymd BETWEEN {$ymd_promotion_start} AND {$ymd_promotion_end}
                AND u.invitetype = 1
                {$where_uid}
                {$where_uids}
                {$where_info_is_complete}
                {$where_status}
        ";
        $arr_data = current( $this->query($sql_total) );
        $total    = $arr_data['total'];

        $sql = "
            SELECT
                r.uid,
                r.user_name,
                (select sum(software_num) from log_credit where uid=r.uid and ymd between {$ymd_promotion_start} AND {$ymd_promotion_end} and type=2 )  AS software_num ,#软件实际有效量
                0 as credit,#积分
                (select sum(navigate_num) from log_credit where uid=r.uid and ymd between {$ymd_promotion_start} AND {$ymd_promotion_end} ) AS navigate_num, #导航有效量
                u.bind_dateline AS bind_dateline,#绑定时间戳
                u.last_login_time AS dateline_last_login, #最后登录时间戳
                u.info_is_complete AS info_is_complete,  #资料是否完整
                u.status,#是否是黑名单
                (select `name` from {$table_user_info_ext} where uid=r.uid ) AS info_user_name, #自己填资料的真实姓名
                (select `realname` from {$table_user_marketer_children} where userid=r.uid ) AS manager_set_user_name #市场经理填写的真实姓名
            FROM {$table_log_register} r
                LEFT JOIN {$table_user} u on r.uid=u.id
            WHERE
                r.invite_uid = {$invite_uid}
                #AND r.ymd BETWEEN {$ymd_promotion_start} AND {$ymd_promotion_end}
                AND u.invitetype = 1
                {$where_uid}
                {$where_uids}
                {$where_info_is_complete}
                {$where_status}
            ORDER BY {$order} {$asc}
            LIMIT {$row_start},{$row_num};
        ";
        $arr_data = $this->query( $sql );
        foreach ( $arr_data as $key => $item ) {
            $arr_data[$key]['real_name'] = !empty($item['manager_set_user_name']) ? $item['manager_set_user_name']
                : (!empty($item['info_user_name']) ? $item['info_user_name'] : '' );
        }


        foreach ($arr_data as $row ) $uid_list[] = $row['uid'];
        $data_list = $this->promotion_day_count(  $uid_list  );//取总推广天数
        $data_ym_list = $this->promotion_day_count( $uid_list, date('ym'));//月推广天数
        foreach ( $arr_data as &$row ) {
            $row['promotion_day_count'] = isset($data_list[$row['uid']]) ? $data_list[$row['uid']] : 0;
            $row['promotion_ym_day_count'] = isset($data_ym_list[$row['uid']]) ? $data_ym_list[$row['uid']] : 0;
        }

        $this->_fill_idx($arr_data, $row_start);
        return array(
            'total' => $total,
            'list'  => $arr_data
        );
    }

    /**
     * 推广积分明细按年月日分组
     */
    public function get_credit_detail_group_ymd( $uid, $ymd_promotion_start, $ymd_promotion_end ) {
        $uid                            = intval($uid);
        $ymd_promotion_start            = intval($ymd_promotion_start);
        $ymd_promotion_end              = intval($ymd_promotion_end);
        $table_log_credit               = $this->_get_table_name();
        $sql = "
            SELECT
                sum(software_num) AS software_num ,#软件实际有效量
                sum(credit) AS credit,#积分
                sum(navigate_num) AS navigate_num, #导航有效量
                c.ymd as ymd,#推广年月日
                UNIX_TIMESTAMP(c.ymd) as dateline_ymd #推广年月日时间戳
            FROM {$table_log_credit} c
            WHERE
              c.uid = {$uid}
              AND c.ymd BETWEEN {$ymd_promotion_start} AND {$ymd_promotion_end}
            GROUP BY c.ymd
            ORDER BY c.ymd DESC
        ";
        $arr_data = $this->query( $sql );
        return $arr_data;
    }

    /**
     * 推广积分明细按推广类分组
     */
    public function get_credit_detail_group_promotion( $uid, $ymd_promotion_start, $ymd_promotion_end ) {
        $uid                            = intval($uid);
        $ymd_promotion_start            = intval($ymd_promotion_start);
        $ymd_promotion_end              = intval($ymd_promotion_end);
        $table_log_credit               = $this->_get_table_name();
        $sql = "
            SELECT
                c.uid as uid,
                c.`name` as `name`,#推广软件的名称
                c.promotion_id as promotion_id,
                c.type as type,
                c.sub_type as sub_type,
                sum(ip_count) as ip_count,#签到或活动实际有效量
                sum(software_num) AS software_num ,#软件实际有效量
                sum(credit) AS credit,#积分
                sum(navigate_num) AS navigate_num, #导航有效量
                c.ymd as ymd,#推广年月日
                UNIX_TIMESTAMP(c.ymd) as dateline_ymd #推广年月日时间戳
            FROM {$table_log_credit} c
            WHERE
              c.uid = {$uid}
              AND c.ymd BETWEEN {$ymd_promotion_start} AND {$ymd_promotion_end}
            GROUP BY c.name
            ORDER BY c.dateline DESC
        ";
        $arr_data = $this->query( $sql );
        return $arr_data;
    }



    /**
     * 导航推广量明细按推广类分组
     */
    public function get_navigate_detail_group_promotion( $uid, $ymd_promotion_start, $ymd_promotion_end  ) {
        $uid                            = intval($uid);
        $ymd_promotion_start            = intval($ymd_promotion_start);
        $ymd_promotion_end              = intval($ymd_promotion_end);
        $table_log_credit               = $this->_get_table_name();
        $sql = "
            SELECT
                c.`name` as `name`,#推广软件的名称
                sum(ip_count) as ip_count,#签到或活动实际有效量
                sum(navigate_num) AS navigate_num #导航有效量
            FROM {$table_log_credit} c
            WHERE
              c.uid = {$uid}
              AND c.type = 2
              AND c.sub_type = 3
              AND c.ymd BETWEEN {$ymd_promotion_start} AND {$ymd_promotion_end}
            GROUP BY c.name
            ORDER BY c.dateline DESC
        ";
        $arr_data = $this->query( $sql );
        return $arr_data;
    }


    /**
     * 软件安装量明细按推广软件分组
     */
    public function get_software_num_detail_group_software($uid, $ymd_promotion_start, $ymd_promotion_end) {
        $uid                    = intval($uid);
        $ymd_promotion_start    = intval($ymd_promotion_start);
        $ymd_promotion_end      = intval($ymd_promotion_end);
        $table_log_credit       = $this->_get_table_name();
        $sql = "
            SELECT
                c.`name` as `name`,#推广软件的名称
                sum(credit) as credit,#推广应收积分
                sum(software_num) AS software_num #实际有效量
            FROM {$table_log_credit} c
            WHERE
              c.uid = {$uid}
              AND c.type = 2
              AND c.sub_type < 3
              AND c.ymd BETWEEN {$ymd_promotion_start} AND {$ymd_promotion_end}
            GROUP BY c.name
            ORDER BY c.promotion_id DESC
        ";
        $arr_data = $this->query( $sql );
        return $arr_data;
    }

    /**
     * 取最后更新时间
     * @return int
     */
    public function get_last_update() {
        $last_update    = '';
        $now            = time();
        if ( $now > strtotime(date('Y/m/d 17:50:00'))) {
            if ( $now > strtotime(date('Y/m/d 23:50:00'))) {
                $last_update = date('Y/m/d 23:50:00');
            }
            else {
                $last_update = date('Y/m/d 17:50:00');
            }
        }
        else {
            $last_update = date('Y/m/d 23:50:00', strtotime('-1 day'));
        }

        return !empty($last_update) ? "最后更新:{$last_update}" : '';
    }

    public function get_num_by_uid($uid,$is_manger){
        if($is_manger!=1){
            return 1;
        }
        $statr = date("Ymd",strtotime("-30 days"));
        $end = date("Ymd");
        $sql = "select sum(software_num) as software_num,sum(navigate_num) as navigate_num
                 from {$this->_get_table_name()}
                 where type=2 and uid={$uid} and ymd between {$statr} and {$end}";
        $arr_data = $this->query($sql);
        if($arr_data&&($arr_data[0]['software_num']+$arr_data[0]['navigate_num'])>0){
            return $arr_data[0]['software_num']+$arr_data[0]['navigate_num'];
        }
        return 0;
    }
}
