<?php
/**
 * Created by PhpStorm.
 * User: vling
 * Date: 16/1/31
 * Time: 11:46
 */
namespace Dao\Channel_7654;

class Act_newyear2016 extends Channel_7654 {

    /**
     * @return Act_newyear2016
     */
    public static function  get_instance() {
        return parent::get_instance();
    }

    /**
     * 市场经理红包领取列表
     * @return array
     */
    public function hongbao_rank() {
        $sql = "
select
	manager_uid,manager_user_name, sum(hongbao_num) as hongbao_num
from
	act_newyear2016
where
	hongbao_num > 0
GROUP BY
	manager_uid
        ";
        $data_list = $this->query( $sql );
        return array_merge( $data_list, $this->_fake_hongbao_rank() );
    }

    /**
     * 取市场经理红包领取情况列表
     * @param $manager_uid
     */
    public function hongbao_list( $manager_info, $ymd_start, $ymd_end ) {
        $manager_uid    = $manager_info['id'];
        $manager_idcode = $manager_info['idcode'];
        $manager_uid    = intval( $manager_uid );
        $ymd_start      = intval( $ymd_start );
        $ymd_end        = intval( $ymd_end );
        $ymd_officia    = date('Ymd', $manager_info['officialtime']);
        $ymd_now        = date('Ymd');
        if (empty($manager_uid )) return false;

        //红包基本信息
        $sql = "
            select
                *
            from
              {$this->_get_table_name()}
            WHERE
              manager_uid = '{$manager_uid}'
              and ymd BETWEEN {$ymd_start} and {$ymd_end}
            ORDER BY
              ymd desc
        ";

        foreach ( $this->yield_result( $sql ) as $row ) $data_list[$row['ymd']] = $row;

        if (empty($data_list)) {
            $this->_hongbao_init( $manager_uid, $manager_info['name'], $ymd_start, $ymd_end );
            return $this->hongbao_list( $manager_info, $ymd_start, $ymd_end );
        }

        //业绩发放情况
        $sql = "
            select
               ymd,status_sure
            from
               `union`.ad_product_record_fafang
            where
               ymd BETWEEN {$ymd_start} and {$ymd_end}
               and promotion_name in ('qqpcmgr', 'qqbrowserv2')
        ";
        $fafang_list = $this->query( $sql );
        foreach ( $fafang_list as $fafang_info ) {
            if ($fafang_info['status_sure'] && isset($data_list[$fafang_info['ymd']]) ) {
                $data_list[$fafang_info['ymd']]['is_fafang'] = 1;
            }
        }

        //签到情况
        $sql = "
            select
                ymd
            from
              manager_clock_in
            WHERE
              uid={$manager_uid}
              and ymd BETWEEN {$ymd_start} and {$ymd_end}
        ";
//        \Io::fb( $sql );
        $sign_list = [];
        foreach ( $this->yield_result( $sql) as $row ) $sign_list[ $row['ymd'] ] = $row;
        foreach ( $data_list as &$row ) $row['is_sign'] = !empty($sign_list[$row['ymd']]);

        //市场经理名下技术员软件安装情况, qq管家和qq浏览器安装量满4个
        $sql = "
          select ymd,count(*) as technician_num from (
                select
                uid,
                ymd,
                sum(c.ip_count) as num
                from
                `union`.`user` u
                INNER  JOIN `union`.credit_wait_confirm c
                  on  u.id=c.uid
                  and u.invitetype=1
                  and  u.invitecode='{$manager_idcode}'
                  and c.ymd BETWEEN {$ymd_start} and {$ymd_end}
                  and c.`name` in ('qqpcmgr', 'qqbrowserv2')

                GROUP by c.uid,ymd
                HAVING num >= 4
              ) t
          GROUP by t.ymd
        ";
        foreach ( $this->yield_result( $sql ) as $item ) {
            if ( $data_list[$item['ymd']]['is_get'] == 0 ) {
                $data_list[$item['ymd']]['hongbao_num'] = intval( $item['technician_num'] );
            }
        }

        $hongbao_list = [];
        foreach ($data_list as $hongbao_info ) {
            if ( $hongbao_info['ymd'] >= $ymd_now ) continue;

            if ( $hongbao_info['ymd'] < $ymd_officia ) {
                $hongbao_info['hongbao_num'] = 0;//转正前置0
            }
            $hongbao_list[$hongbao_info['ymd']] = $hongbao_info;
        }
        return $hongbao_list;
    }

    /**
     * 安始化红包列表
     * @param $manager_uid
     * @param $manager_user_name
     * @param $ymd_start
     * @param $ymd_end
     */
    protected function _hongbao_init( $manager_uid,  $manager_user_name, $ymd_start, $ymd_end  ) {
        if ( empty($manager_uid) || empty($manager_user_name) || empty($ymd_start ) || empty($ymd_end)) {
            return false;
        }

        $dateline_start = strtotime( $ymd_start );
        $dateline_end   = strtotime( $ymd_end );
        $data_init      = [];
        while ( $dateline_start <= $dateline_end ) {
            $ymd = date( 'Ymd', $dateline_start );
            $dateline_start = strtotime( '+1 day', $dateline_start );
            $data_init[] = [
                'manager_uid' => $manager_uid,
                'manager_user_name' => $manager_user_name,
                'ymd' => $ymd,
                'is_get' => 0,
            ];
        }
        return $this->add_all( $data_init );
    }

    /**
     * 领取红包
     * @param $manager_uid
     * @param $ymd
     */
    public function take_hongbao( $hongbao_info ) {
        if (empty($hongbao_info['manager_uid']) || empty($hongbao_info['ymd']) || empty( $hongbao_info['credit'])
        || empty($hongbao_info['hongbao_num'])) {
            throw new \Exception('idvalid hongbao');
        }

        $manager_uid = $hongbao_info['manager_uid'];
        $ymd = $hongbao_info['ymd'];
        $credit = $hongbao_info['credit'];

        return $this->update( [ 'manager_uid' => $manager_uid, 'ymd' => $ymd, 'is_get' => 0 ],
           [
               'is_get' => 1,
               'credit' => $credit,
               'datetime_get' => date('Y-m-d H:i:s'),
               'hongbao_num' => $hongbao_info['hongbao_num'],
           ]);
    }

    /**
     * 红包假数据
     * @return array
     */
    protected function _fake_hongbao_rank() {
        $data = \Io\File::csv_to_array(SITE_DIR . '/Conf/hongbao_2016_fake_data.csv', ['manager_user_name', 'hongbao_num']);
        return $data;
    }
}