<?php
namespace Dao\Kuaizip;

class Performance_detail extends Kuaizip {
    /**
     * @return \Dao\Kuaizip\Performance_detail
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table() {
        $sql = "
CREATE TABLE `performance_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `uid` int(10) unsigned DEFAULT NULL COMMENT '用户uid',
  `user_name` varchar(40) DEFAULT NULL COMMENT '用户名',
  `tn` varchar(40) DEFAULT NULL COMMENT '渠道号',
  `ip_count` int(11) NOT NULL DEFAULT '0' COMMENT '安装量',
  `ymd` int(10) unsigned NOT NULL COMMENT '业绩年月日',
  `dateline_confirm` int(10) unsigned DEFAULT NULL COMMENT '确认时间戳',
  `status` tinyint(4) NOT NULL DEFAULT '2' COMMENT '状态, 2:未放发,3:已发放',
  PRIMARY KEY (`id`),
  KEY `ymd` (`ymd`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=156 DEFAULT CHARSET=utf8 COMMENT='业绩明细';
        ";
    }

    public function init_data() {
        $dateline_start = strtotime('20150911');
        $dateline_end = strtotime('20250911');
        $data_init = [];
        while ($dateline_start < $dateline_end ) {
            $ymd_now = date('Ymd', $dateline_start);
            $data_init[] = ['ymd' => $ymd_now];
            $dateline_start += 86400;
        }
        $this->add_all( $data_init );
    }

    public function get_list( $ymd, $uid = null ) {
        $ymd = intval( $ymd );
        $where  = " WHERE ymd = {$ymd} ";
        if (isset($uid)) $where .= " AND uid='{$uid}'";
        $table = $this->_get_table_name();
        $sql = "SELECT * from {$table} {$where}";
        return $this->query( $sql );
    }

    public function get_user_available_list( $uid, $ymd_start, $ymd_end ) {
        $table_name = $this->_get_table_name();
        $sql = "
            select
                *
            from
                {$table_name}
            where
              uid={$uid}
              and ymd BETWEEN {$ymd_start} and {$ymd_end}
              and status=3
        ";
        return $this->query( $sql );
    }

    /**
     * 取用户有效安装量
     * @param $uid
     * @param $ymd_end
     * @return int
     */
    public function get_ip_count( $uid, $ymd_end ) {
        $uid        = intval( $uid );
        $ymd_end    = intval( $ymd_end);
        if (empty($uid) || empty($ymd_end )) return 0;
        $sql = "
            select
                sum(ip_count) as ip_count
            from
                `performance_detail`
            WHERE
                uid = {$uid}
                and ymd <= {$ymd_end}
                and `status` = 3
        ";
        $data = $this->query( $sql );
        $ip_count = !empty($data) ? intval( $data[0]['ip_count']) : 0;
        return $ip_count;
    }

    /**
     * 取用户总有效安装量
     * @param $uid
     * @return int
     */
    public function get_ip_count_total ( $uid ) {
        $uid = intval( $uid );
        if (empty($uid)) return 0;
        $sql = "
            select
                sum(ip_count) as ip_count
            from
                `performance_detail`
            WHERE
                uid = {$uid}
                and `status` = 3
        ";
        $data = $this->query( $sql );
        $ip_count = !empty($data) ? intval( $data[0]['ip_count']) : 0;
        return $ip_count;
    }
}