<?php
namespace Dao\Union;
use \Dao;
class Log_register extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Log_register
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function sync_log( $uid = 0 ) {
        $dateline_start = time() - 86400 * 3;
        if (!empty($uid)) {
            $sql ="
          REPLACE into log_register (
				uid,
				user_name,
				ym,
				ymd,
				bind_ymd,
				dateline,
				invite_uid,
				invite_user_name,
				invite_area_id,
				is_stat_manager,
				parent_invite_uid,
				parent_invite_user_name
			)
			SELECT
                  u.id as uid,
                  u.`name` as user_name,
                  FROM_UNIXTIME(u.reg_dateline,'%y%m') as ym,
                  FROM_UNIXTIME(u.reg_dateline,'%y%m%d') as ymd,
                  FROM_UNIXTIME(u.bind_dateline,'%y%m%d') as bind_ymd,
                  u.reg_dateline as dateline,
                u2.id as invite_uid,
                u2.`name` as invite_user_name,
                m.areaid as invite_area_id,
                m.areaid+m.refer_type as is_stat_manager,
                u3.id as parent_invite_uid,
                u3.`name` as parent_invite_user_name
			FROM `user` u
			    LEFT JOIN `user` u2 on u.invitecode=u2.idcode
			    LEFT JOIN channel_7654.user_marketer m on u2.id=m.userid
				LEFT JOIN `user` u3 on u2.invitecode=u3.idcode
			WHERE u.id>={$uid}
         ";
        }
        else {
            $sql ="
          REPLACE into log_register (
				uid,
				user_name,
				ym,
				ymd,
				bind_ymd,
				dateline,
				invite_uid,
				invite_user_name,
				invite_area_id,
				is_stat_manager,
				parent_invite_uid,
				parent_invite_user_name
			)
			SELECT
                  u.id as uid,
                  u.`name` as user_name,
                  FROM_UNIXTIME(u.reg_dateline,'%y%m') as ym,
                  FROM_UNIXTIME(u.reg_dateline,'%y%m%d') as ymd,
                  FROM_UNIXTIME(u.bind_dateline,'%y%m%d') as bind_ymd,
                  u.reg_dateline as dateline,
                u2.id as invite_uid,
                u2.`name` as invite_user_name,
                m.areaid as invite_area_id,
                m.areaid+m.refer_type as is_stat_manager,
                u3.id as parent_invite_uid,
                u3.`name` as parent_invite_user_name
			FROM `user` u
			    LEFT JOIN `user` u2 on u.invitecode=u2.idcode
			    LEFT JOIN channel_7654.user_marketer m on u2.id=m.userid
				LEFT JOIN `user` u3 on u2.invitecode=u3.idcode
			WHERE
			  u.reg_dateline>={$dateline_start}
         ";
        }
        $this->query($sql);

    }

    /**
     * 取活跃市场经理列表,有注册绑定装机员,按月分组
     * @param array $arr_area_id_list
     * @return array|mixed
     */
    public function get_manager_active_ym_group(  $arr_area_id_list = array() ) {
        /*
            select count(*) as invite_num, m.areaid as area_id, r.ym from log_register r
            LEFT JOIN  channel_7654.user_marketer m on r.invite_uid = m.userid
            WHERE r.invite_uid > 0 and m.areaid > 0
            GROUP BY m.areaid,r.ym
         */
        $table_log_register = $this->_get_table_name();
        $table_channel_user_marketer = $this->_get_table_name('user_marketer', \Lib\Core::config('db_channel_7654'));
        $where_area_id = ( !empty($arr_area_id_list) && is_array( $arr_area_id_list) ) ? ( ' AND m.areaid in (' . implode(',', $arr_area_id_list) . ') ' ) : '';
        $sql = "select count(distinct r.invite_uid ) as invite_num, m.areaid as area_id, r.ym from {$table_log_register} r
          LEFT JOIN  {$table_channel_user_marketer} m on r.invite_uid = m.userid
          where r.invite_uid > 0 and m.is_stat_manager > 0
          {$where_area_id}
          GROUP BY m.areaid,r.ym";

        $arr_data = $this->query( $sql );
        $result = array();
        foreach ($arr_data as $item ) {
            $result[ $item['ym'] ] += $item['invite_num'];
        }
        return $result;
    }

    /**
     * 取活跃市场经理，按ymd分组
     * @param array $arr_area_id_list
     * @param $ymd_start
     * @param $ymd_end
     * @return array
     */
    public function get_manager_active_group_ymd(  $arr_area_id_list = array(), $ymd_start, $ymd_end  ) {
        $table_log_register             = $this->_get_table_name();
        $table_channel_user_marketer    = $this->_get_table_name('user_marketer', \Lib\Core::config('db_channel_7654'));
        $where_area_id                  = ( !empty($arr_area_id_list) && is_array( $arr_area_id_list) ) ? ( ' AND m.areaid in (' . implode(',', $arr_area_id_list) . ') ' ) : '';
        $sql                            = "select count(distinct r.invite_uid ) as invite_num, m.areaid as area_id, r.ymd from {$table_log_register} r
          LEFT JOIN  {$table_channel_user_marketer} m on r.invite_uid = m.userid
          WHERE r.invite_uid > 0 and m.is_stat_manager > 0 AND ymd>={$ymd_start} AND ymd<={$ymd_end}
          {$where_area_id}
          GROUP BY m.areaid,r.ymd";

        $arr_data = $this->query( $sql );
        $result = array();
        foreach ($arr_data as $item ) {
            $result[ $item['ymd'] ] += $item['invite_num'];
        }
        return $result;
    }

    public function get_manager_active_num_group_ymd( $manager_id, $arr_ymd_list ) {

    }

    /**
     * 取活跃市场经理信息列表,有注册绑定装机员
     * @param array $arr_area_id_list 渠道主管数组
     * @param int $ym 限定月份，默认不限
     * @param int $ymd 年月日
     * @param int $row_begin 开始行
     * @param int $num 数量
     * @param string $user_name
     * @return array
     */
    public function get_manager_active_list(  $arr_area_id_list = array(), $ym = 0,  $ymd = 0, $row_begin = 0, $num = 10, $user_name = '' ) {
        /*
            select count(*) as invite_num, m.areaid as area_id, r.ym from log_register r
            LEFT JOIN  channel_7654.user_marketer m on r.invite_uid = m.userid
            WHERE r.invite_uid > 0 and m.areaid > 0
            GROUP BY m.areaid,r.ym
         */
        $table_log_register             = $this->_get_table_name();
        $table_user                     = $this->_get_table_name('user');

        $table_channel_user_marketer    = $this->_get_table_name('user_marketer', \Lib\Core::config('db_channel_7654'));
        $where_area_id                  = ( !empty($arr_area_id_list) && is_array( $arr_area_id_list) ) ? ( ' AND m.areaid in (' . implode(',', $arr_area_id_list) . ') ' ) : '';
        $ym                             = intval( $ym );
        $ymd                            = intval( $ymd );
        if (!empty($ymd)) {
            $where_ym = ' AND r.ymd=' . $this->_get_ymd( $ymd );
        }
        elseif ( !empty($ym ) ) {
            $where_ym = ' AND r.ym=' . $ym;
        }
        $where_user_name = !empty($user_name) ? " AND m.username LIKE '{$user_name}%'" : '';

        $sql = "select r.invite_uid as uid,r.invite_user_name as user_name,u.phone from {$table_log_register} r
          LEFT JOIN {$table_channel_user_marketer} m on r.invite_uid = m.userid
          LEFT JOIN  {$table_user} u on r.invite_uid = u.id
          where r.invite_uid > 0 and m.is_stat_manager > 0
          {$where_area_id}
          {$where_ym}
          {$where_user_name}
          GROUP by r.invite_uid
          LIMIT {$row_begin}, {$num}";

        $arr_data = $this->query( $sql );
        $result = array();
        foreach ($arr_data as $item ) $result[$item['uid'] ] = $item;
        return $result;
    }

    /**
     * 取总活跃经理人数
     * @param array $arr_area_id_list
     * @param int $ym
     * @param int $ymd
     * @param string $user_name
     * @return integer
     */
    public function get_manager_active_total(  $arr_area_id_list = array(), $ym = 0, $ymd = 0,  $user_name = ''  ) {

        $table_log_register             = $this->_get_table_name();
        $table_channel_user_marketer    = $this->_get_table_name('user_marketer', \Lib\Core::config('db_channel_7654'));
        $where_area_id                  = ( !empty($arr_area_id_list) && is_array( $arr_area_id_list) ) ? ( ' AND m.areaid in (' . implode(',', $arr_area_id_list) . ') ' ) : '';
        $ym                             = intval( $ym );
        $ymd                            = intval( $ymd );

        if (!empty($ymd)) {
            $where_ym = ' AND r.ymd=' . $this->_get_ymd( $ymd );
        }
        elseif ( !empty($ym ) ) {
            $where_ym = ' AND r.ym=' . $ym;
        }

        $where_user_name = !empty($user_name) ? " AND m.username LIKE '{$user_name}%'" : '';


        $sql = "select count(distinct r.invite_uid ) as invite_num, m.areaid as area_id, r.ym from {$table_log_register} r
          LEFT JOIN  {$table_channel_user_marketer} m on r.invite_uid = m.userid
          where r.invite_uid > 0 and m.is_stat_manager > 0
          {$where_area_id}
          {$where_ym}
          {$where_user_name}
          ";

        $arr_data = current( $this->query( $sql ) );
        return intval( $arr_data['invite_num']);
    }

    /**
     *
     * 出下属绑定技术数量
     * @param array $arr_manager_id 市场经理id数组
        manager_id1
        manager_id2
     * @param int $ym 年月
     * @param int $ymd 年月日
     * @return array
        manager_id1 : 技术员数量
     *
     */
    public function get_technician_total_by_manager_id_list( $arr_manager_id, $ym = 0, $ymd = 0) {

        $table_log_register     = $this->_get_table_name();

        $where_ym               = '';
        if (!empty($ymd)) {
            $where_ym = ' AND ymd=' . $ymd;
        }
        elseif ( !empty($ym)) {
            $where_ym = ' AND ym=' . $ym;
        }

        $invite_uid = implode(',', $arr_manager_id);
        $sql = "SELECT count(distinct uid) as num, invite_uid
        FROM {$table_log_register}
        WHERE invite_uid in($invite_uid)
        {$where_ym}
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
     * 取被邀请的技术员总量
     * @param $invite_uid
     * @param int $ym
     * @param int $ymd_start
     * @param int $ymd_end
     * @param int $info_is_complete
     * @param int $from_manager
     * @return mixed
     */
    public function get_technician_count( $invite_uid, $ym = 0, $ymd_start = 0, $ymd_end = 0, $info_is_complete = 0, $from_manager = 1 ) {

        $table_log_register     = $this->_get_table_name();

        $where_ym               = !empty($ym) ? " AND r.ym = {$ym} " : '';
        if (!empty($ymd_start) && !empty($ymd_end) ) {
            $ymd_start = $this->_get_ymd($ymd_start);
            $ymd_end   = $this->_get_ymd( $ymd_end );
        }
        $where_ymd              = ( !empty($ymd_start) && !empty($ymd_end) ) ? " AND r.ymd BETWEEN {$ymd_start} AND {$ymd_end} " : '';
        $where_from_manager     = !empty($from_manager) ? ' AND u.invitetype = 1 ' : '';
        $where_info_is_complete = !empty($info_is_complete) ? " AND ue.info_is_complete > 0 " : '';
        $sql = "
        SELECT
            count(distinct r.uid) as total
        FROM {$table_log_register} r
            LEFT JOIN `user` u on u.id=r.uid
            LEFT JOIN `user_info_ext` ue on ue.uid=r.uid
        WHERE
          r.invite_uid = {$invite_uid}
          {$where_from_manager}
          {$where_ym}
          {$where_ymd}
          {$where_info_is_complete}
        ";

        $arr_data = current($this->query($sql));
        return $arr_data['total'];

    }

    /**
     * @param $invite_uid
     * @param $dateline_start
     */
    public function get_technician_count_dateline_start( $invite_uid, $dateline_start = 0 ) {
        $table_log_register     = $this->_get_table_name();
        $where_dateline         = !empty($dateline_start) ? " AND u.bind_dateline > {$dateline_start} " : '';
        $sql = "
        SELECT
            count(distinct uid) as total
        FROM {$table_log_register} r
            LEFT JOIN `user` u on u.id=r.uid
        WHERE
          r.invite_uid = {$invite_uid}
          #AND u.invitetype = 1
          {$where_dateline}
        ";
        $arr_data = current($this->query($sql));
        return $arr_data['total'];
    }

    /**
     * 统计活跃（有绑定下家）用户数量
     * @param int $ym 年月
     * @param int|array $ymd 年月日,值为单数值限定年月日，值为数组年月日范围，下标0开始年月日,下标1结束年月日
     * @param array $arr_in_user_id 指定用户
     * @return array
     */
    public function count_active( $ym = 0, $ymd = 0, $arr_in_user_id = array() ) {

        $table_log_register = $this->_get_table_name();
        $arr_where          = array(
            'invite_area_id' => 'is_stat_manager > 0'
        );

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
        $sql = "SELECT count(distinct invite_uid) as num, invite_uid
        FROM {$table_log_register}
        {$where}";

        $arr_data = current($this->query( $sql ));
        return !empty($arr_data) ? $arr_data['num'] : 0;
    }

    /**
     * 邀请用户总计去重
     * @param int $ym 年月
     * @param int|array $ymd 年月日,值为单数值限定年月日，值为数组年月日范围，下标0开始年月日,下标1结束年月日
     * @param array $arr_in_user_id 指定用户
     * @return array
     */
    public function count_invite( $ym = 0, $ymd = 0, $arr_in_user_id = array() ) {
        $table_log_register = $this->_get_table_name();
        $arr_where          = array(
            'invite_area_id' => 'is_stat_manager > 0'
        );

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
        FROM {$table_log_register}
        {$where}";

        $arr_data = current($this->query( $sql ));
        return !empty($arr_data) ? $arr_data['num'] : 0;
    }

    /**
     * 取用户邀请总计按ymd分组
     * @param int $invite_uid
     * @param $ymd_start
     * @param $ymd_end
     * @return string
     */
    public function get_invite_list_group_by_ymd(  $invite_uid,  $ymd_start,  $ymd_end, $is_from_manager = 1 ) {
        $result                 = array();
        $table_log_register     = $this->_get_table_name();
        $ymd_start              = $this->_get_ymd( $ymd_start );
        $ymd_end                = $this->_get_ymd( $ymd_end );

        $where_is_from_manager  = !empty( $is_from_manager ) ? ' AND u.invitetype = 1 ' : '';
        $where_ymd              = ( !empty( $ymd_start ) && !empty($ymd_end) )  ?  " AND  r.bind_ymd BETWEEN {$ymd_start} AND {$ymd_end} " : '';
        $sql = "
          SELECT
              count(*) as num,
              r.bind_ymd
          FROM {$table_log_register} r
              LEFT JOIN `user` u on u.id = r.uid
          WHERE
              r.invite_uid={$invite_uid}
              {$where_ymd}
              {$where_is_from_manager}
          GROUP BY r.bind_ymd
        ";
        $arr_data = $this->query( $sql );
        foreach ( $arr_data as $item ) {
            $field_key = $this->_ymd_Ymd( $item['bind_ymd'] );
            $result[$field_key] = array(
                'ymd' => $field_key,
                'num' => $item['num']
            );
        }
//        \Io::fb($sql);
        error_log( $sql . "\n", 3, PATH_RUNTIME . 'mysql_error.log');
        return $result;
    }

    /**
     * 年月日转换
     * @param $ymd
     * @return integer
     */
    protected function _get_ymd( $ymd ) {
        $ymd = intval($ymd);
        return $ymd > 20000000 ? ($ymd - 20000000) : $ymd;
    }

    /**
     * 6位年月日到8位年月日
     * @param $ymd
     * @return integer
     */
    protected function _ymd_Ymd( $ymd ) {
        $ymd = intval($ymd);
        return $ymd < 20000000 ? ($ymd + 20000000) : $ymd;
    }
}
