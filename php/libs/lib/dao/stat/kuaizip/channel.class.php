<?php
/**
 * 统计-渠道主管-我的业绩
 * Mongo存储
 * 渠道主管各项业绩
 */
namespace Dao\Stat\Kuaizip;
use \Dao\Stat\Stat;

class Channel extends Stat {
    /**
     * @return Channel
     */
    public static function get_instance(){ return parent::get_instance(); }

    public function create_table() {
        $sql = "
CREATE TABLE `kuaizip_channel` (
  `ymd` int(11) NOT NULL COMMENT '年月日',
  `channel` varchar(40) NOT NULL COMMENT '渠道',
  `reg_count_total` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '累计到当前ymd所有注册数',
  `reg_count` int(10) unsigned NOT NULL COMMENT '当天注册数',
  `performance_user_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当天有业绩用户数',
  `ip_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当天安装量',
  PRIMARY KEY (`ymd`,`channel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='渠道推广情况';
        ";
    }

    public function get_list($ymd_start, $ymd_end) {

        $table_name = $this->_get_table_name();
        $sql = "
            select
                *
            from
                {$table_name}
            WHERE
                ymd BETWEEN {$ymd_start} and {$ymd_end}
            ORDER by
                ymd desc
        ";
        return $this->query( $sql );

    }

    /**
     * 统计指定日信息
     * @param $ymd
     */
    public function sync_ymd( $ymd ) {

        $table_user = $this->_get_table_name('user', \Config::get('DB_KUAIZIP'));
        $table_performance_detail =  $this->_get_table_name('performance_detail', \Config::get('DB_KUAIZIP'));
        $sql_reg_count = "
            select
                count(*) as reg_count,
                `from`
            from
                {$table_user} u
            where
                reg_ymd={$ymd}
            GROUP BY
                `from`
        ";

        $sql_reg_count_total = "
            select
                count(*) as reg_count_total,
                `from`
            from
                {$table_user} u
            where
                reg_ymd<={$ymd}
            GROUP BY
                `from`
        ";
        $sql_performance_user_count = "
            select
                count(DISTINCT pd.uid) as performance_user_count,
                u.`from`
            from
                {$table_performance_detail} pd
                INNER JOIN {$table_user} u on u.uid=pd.uid  and pd.ip_count > 0  and pd.ymd={$ymd}
            GROUP BY
                u.`from`
        ";

        $sql_ip_count = "
            select
                sum( pd.ip_count ) as ip_count,
                u.`from`
            from
                {$table_performance_detail} pd
                INNER JOIN {$table_user} u on u.uid=pd.uid  and pd.ymd={$ymd}
            GROUP BY
                u.`from`
        ";


        $channel_list = \Dao\Kuaizip\Channel::get_instance()->get_list();
        $sql_list = [
            'reg_count' => $sql_reg_count,
            'reg_count_total' => $sql_reg_count_total,
            'performance_user_count' => $sql_performance_user_count,
            'ip_count' => $sql_ip_count
        ];

        $data = [];
        foreach ( $sql_list as $field => $sql ) {
            foreach ( $this->yield_result( $sql ) as $row ) {
                if (empty($row['from']) || !isset($channel_list[$row['from']])) continue;
                if ( !isset($data[$row['from']] ) ) $data[$row['from']] = [
                    'ymd'   => $ymd,
                    'channel' => $row['from'],
                    'reg_count' => 0,
                    'reg_count_total' => 0,
                    'performance_user_count' => 0,
                    'ip_count' => 0
                ];
                $data[$row['from']][$field]= $row[$field];
            }
        }
        $this->delete('ymd=' . $ymd);
        $this->addAll($data);
        return count($data);
    }
}