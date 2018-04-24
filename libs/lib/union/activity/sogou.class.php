<?php
namespace Union\Activity;

/**
 * 活动基类
 * Class Base
 * @package Union\Activity
 */
class Sogou extends Base {

    protected $_act_id = 'sogou';

    public function __construct( $_config = [] ) {

        $config = [
            'sdate'         => 20150604,//开始日期
            'edate'         => 20150703,//结束日期
            'hongbao_max'  => 100,//领取的红包上限,单位元
            'hongbao_per'  => 5,//红包最小单位，单位元
            'rate'          => 25,//红包汇率5
            'install_min'  => 500,//上榜最小安装量
            'rank_num'      => 100,//排行榜数量

            'reward_list'   => [//推广排行榜奖励
                '1'         => ['credit' => 1000000 ],//第一名
                '2'         => ['credit' => 500000 ],
                '3-10'      => ['credit' => 100000 ],//第三至第拾名
                '11-50'     => ['credit' => 50000 ],
                '51-100'    => ['credit' => 10000 ]
            ],
        ];
        if (!empty($_config)) $config = array_merge( $config, $_config);
        $config['dateline_start']   = strtotime( $config['sdate'] );
        $config['dateline_end']     = strtotime( $config['edate'] );

        parent::__construct( $config );

    }



    /**
     * 取活动期间推广业绩
     */
    public function get_promotion_credit( $uid ) {
//        196
        $time_begin     =  $this->_config['dateline_start'];
        $time_end       =  $this->_config['dateline_end'];
        $credit = \Dao\Union\Credit_wait_confirm::get_instance()->sum_credit('', $uid, $time_begin, $time_end);
        return $credit;
    }


    /**
     * 活动期间安装量排名
     * @return [
        [
            rank : 名次
            name : 用户名
            num : 安装量
            credit : 奖励积分
        ]
     * ]
     */
    public function get_rank_of_install() {
        $time_begin     =  $this->_config['dateline_start'];
        $time_end       =  $this->_config['dateline_end'];
        $rank_num       =  $this->_config['rank_num'];
        $install_min    =  $this->_config['install_min'];
//        $install_min = 0;
        $list           =  \Union\Performance\Navigate::i()->sum_install_list('sgdh', null, $time_begin, $time_end, 'desc', $install_min, $rank_num );
        $rank           = 0;
        foreach ( $list as $key => $row ) {
            $rank++;
            $list[$key]['rank'] = $rank;
        }
        return $list;
    }


    /**
     * 取活动期间安装量
     * @param $uid
     */
    public function get_install( $uid ) {
        $time_begin     =  $this->_config['dateline_start'];
        $time_end       =  $this->_config['dateline_end'];
        return \Union\Performance\Navigate::i()->sum_install('sgdh', $uid, $time_begin, $time_end);
    }

    /**
     * 取排行榜奖励列表
     * @return array
        名次 : [
            credit : 奖励的积分
        ]
     */
    public function get_rank_reward_list() {

        $result = array();
        foreach ($this->_config['reward_list'] as $rank => $cfg ) {
            $arr = explode('-', $rank);
            if ( isset($arr[1]) && is_int($arr[0]) && is_int($arr[1]) ) {
                $result = array_merge( $result, array_fill( $arr[0], $arr[1], $cfg ) );
                continue;
            }
            $result[$rank] = $cfg;
        }
        return $result;

    }

    /**
     * 取红包信息
     * @param $uid
     * @param $credit 活动期间得到的业绩
     * @return array
     */
    public function get_hongbao_info( $uid, $credit  ) {
        $now        = \Dao\Union\Act_credit::get_instance()->get_credit($this->_act_id, $uid);
        $rate       = $this->_config['rate'];
        $per        = $this->_config['hongbao_per'];
        $total      = min($this->_config['hongbao_max'], floor( $credit / ($rate * 1000) ) * $per);
        $available  = max(0, $total - $now);
        return [
            'now'       => intval($now),
            'total'     => $total,
            'available'=> $available,
        ];
        //ON DUPLICATE KEY UPDATE c=c+1;
    }

    /**
     * 领取红包，每5元一级
     * @param integer $uid 用户id
     * @param array $hongbao_info
     * @return boolean
     */
    public function get_hongbao( $uid, $hongbao_info ) {
        if (empty($uid) || empty($hongbao_info) || empty($hongbao_info['available'])) return false;
        $data = [
            'act_id'        => $this->_act_id,
            'uid'            => $uid,
            'act_credit'    => $hongbao_info['available'],
            'dateline'      => time(),
        ];
        $dao_act_credit = \Dao\Union\Act_credit::get_instance();
        $dao_act_credit->begin_transaction();
        $ret = \Dao\Union\Act_credit_log::get_instance()->add($data);
//        var_dump($ret);die();
        if ( $ret <= 0 ) {
            $dao_act_credit->rollback();
            return false;
        }
        $data['act_credit'] += $hongbao_info['now'];
        $ret =$dao_act_credit->add($data, false, '', true);
//        die( $dao_act_credit->get_last_sql() );
        if ($ret <= 0 ) {
            $dao_act_credit->rollback();
            return false;
        }
        $dao_act_credit->commit();

        return true;
    }
}