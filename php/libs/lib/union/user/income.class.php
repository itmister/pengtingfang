<?php
namespace union\user;
use \union\dict_map;

class income extends \Core\Object {
    /**
     * @return income
     */
    public static function i( $option = [] ) { return parent::i( $option ); }

    /**
     * 未发收入
     * @param $uid 用户uid
     * @return integer
     */
    public function not_get($uid) {
        return \Dao\Union\Credit_wait_confirm_no::get_instance()->not_get( $uid );
    }

    /**
     * 收入概况
     * @param $uid
     * @return array
        -- yesterday_credit : 昨日预计总收入
        -- month_credit : 月预计总收入
        -- total_credit : 累计已发总收入
        -- total_not_get : 累计未发总上入
     */
    public function general( $uid ) {
        //转换成查询条件
        $ymd = date("Ymd");
        $ym = date("ym");

        $credit_wait_confirm = \Dao\Union\Credit_wait_confirm::get_instance();
        //装机收入明细
        $income_data = $credit_wait_confirm->fetch_user_income( $uid, $ym, $ymd, 'credit_wait_confirm' );

        //预计装机收入明细
        $expect_income_data = $credit_wait_confirm->fetch_user_income( $uid , $ym, $ymd, 'credit_wait_confirm_no' );

        //相同key值相加
        if( !empty($income_data) ){
            array_walk( $income_data, function($val,$key,$param) use(&$income_data){
                $income_data[$key] += $param[$key];
            }, $expect_income_data);
        }
        else{
            $income_data = $expect_income_data;
        }

        //用户已兑现积分
        $exchange_where = "uid= {$uid} AND status IN (1,2)";
        $exchange_num = \Dao\Union\Exchange::get_instance()->get_user_credit_sum($exchange_where);

        $user_credit = \Dao\Union\User::get_instance()->get_row(['id' => $uid], 'credit_wait_confirm,credit');
        //累计已发总收入
        $income_data['total_credit'] = ( $user_credit['credit_wait_confirm'] + $user_credit['credit'] + $exchange_num );

        $income_data['total_not_get'] = $this->not_get( $uid );
        return $income_data;

    }

    /**
     * 昨天收入明细
     * @param $uid
     * @return array|\Dao\mixed
     */
    public function detail($uid) {
        $credit_wait_confirm = \Dao\Union\Credit_wait_confirm::get_instance();
        $ymd = date('Ymd');
        //昨日装机明细
        $income_detail = $credit_wait_confirm->fetch_user_income_detail( $uid , $ymd, 'credit_wait_confirm');

        //昨日预计装机明细
        $expect_income_detail = $credit_wait_confirm->fetch_user_income_detail($uid , $ymd, 'credit_wait_confirm_no');
        $income_detail = array_merge((array)$income_detail,(array)$expect_income_detail);
        return $income_detail;
    }

    /**
     * 日预计收入
     * @param $uid
     * @param $ymd
     * @param int $type 类型 1：软件, 2: 导航f
     * @param int $types
     * @return array
     */
    public  function predict($uid, $ymd, $type = 0, $types = 1){

        $cfg = dict_map::i()->get( dict_map::CATEGORY_PROMOTE );
        $s_ymd = strtotime($ymd);
        if($type==0){
            $w = "state=1 and datetime<{$s_ymd}";
        }else{
            $w = "datetime<{$s_ymd}";

        }

        if($types==6){
            $data = array ();
            $w .= " and type=6";
        }else{
            $w .= " and type<>6";
            $data = array ();
            $data['sign']['name'] = "签到";
            $data['sign']['gui_ze'] = "---";
            $data['sign']['datetype'] = "实时";
        }

        $promotion_types = array( 1 => '有效安装', 2 => '使用', 3 => '返利');
        if (!$ymd) $ymd = date('Ymd', strtotime('-1 day'));

        //日软件安装量
        $list = \Union\User\Account::get_instance()->get_user_credit_day( $uid, $ymd);
        if(empty($list)) return [];

        $yes_credit_list_all = $this->all_name( $ymd, 0, 1);//软件收入明细列表
        $noListCredit = \Union\User\Account::get_instance()->get_user_no_credit_day( $uid, $ymd );//未发收入

        $listNotYmd = \Union\Credit\Manager::get_instance()->get_soft_noincome_ymd_state();
        $listNotYmd = array_merge( $listNotYmd,\Union\Credit\Manager::get_instance()->get_soft_noincome_ymd_state(6) );
        $_list = array();
        $_lists = array();
        $_list_h = array();
        //modify by xiechuanxia 20150713
        foreach ($list as $k => $item) {
            if( $item['type']==10 ){
                //活动
                $_list_h[$item['name']]['name'] = !empty($cfg[$item['name']]) ? $cfg[$item['name']] : "活动积分";
                $_list_h[$item['name']]['dateline'] = date("Y-m-d H:i",$item['dateline']);
                if($_list_h[$item['name']]['credit']<=0){
                    $_list_h[$item['name']]['credit'] = 0;
                }
                $_list_h[$item['name']]['ip_count'] =  $item['ip_count'];
                $_list_h[$item['name']]['credit'] = $_list_h[$item['name']]['credit']+$item['credit'];
                $_list_h[$item['name']]['is_get'] = $item['is_get'];
                $_list_h[$item['name']]['status'] = 1;
            }else{
                $yes_credit_list_all[$item['name']]['dateline'] = date("Y-m-d H:i",$item['dateline']);
                $yes_credit_list_all[$item['name']]['ip_count'] = $item['ip_count'];
                $yes_credit_list_all[$item['name']]['credit'] = $item['credit'];
                $yes_credit_list_all[$item['name']]['is_get'] = $item['is_get'];
            }
        }

        foreach($yes_credit_list_all as $kkk => $vvv){
            if(empty($vvv['name'])){
                $yes_credit_list_all[$kkk]['name'] = $kkk=='9377'?'9377游戏':$cfg[$kkk];
                $yes_credit_list_all[$kkk]['datetype'] = $cfg[$kkk]['time_modify'];
            }
            if(intval($vvv['credit'])<=0){
                $yes_credit_list_all[$kkk]['credit'] = $noListCredit[$kkk]>0?$noListCredit[$kkk]:0;
                if(in_array($ymd,$listNotYmd[$kkk])){
                    $yes_credit_list_all[$kkk]['status'] = 2;
                }
            }
            //$yes_credit_list_all[$kkk]['dateline'] = date("Y-m-d H:i",strtotime($vvv['dateline']));
            if(in_array($kkk,array('ktw'))){
                if($yes_credit_list_all['ktw']['is']==0&&$yes_credit_list_all['ktwjf']['is']==1){
                    $yes_credit_list_all['ktw']['credit'] = $yes_credit_list_all['ktwjf']['credit'];
                    $yes_credit_list_all['ktw']['dateline'] = $yes_credit_list_all['ktwjf']['dateline'];
                }elseif($yes_credit_list_all['ktw']['is']==1&&$yes_credit_list_all['ktwjf']['is']==1){
                    $yes_credit_list_all['ktw']['credit'] += $yes_credit_list_all['ktwjf']['credit'];
                }
                $yes_credit_list_all['ktw']['ip_count'] = $yes_credit_list_all['ktw']['ip_count']."/".$yes_credit_list_all['ktwjf']['ip_count'];
            }
            if(in_array($kkk,array('602gm'))){
                if($yes_credit_list_all['602gm']['is']==0&&$yes_credit_list_all['602gmf']['is']==1){
                    $yes_credit_list_all['602gm']['credit'] = $yes_credit_list_all['602gmf']['credit'];
                    $yes_credit_list_all['602gm']['dateline'] = $yes_credit_list_all['602gmf']['dateline'];
                }elseif($yes_credit_list_all['602gm']['is']==1&&$yes_credit_list_all['602gmf']['is']==1){
                    $yes_credit_list_all['602gm']['credit'] += $yes_credit_list_all['602gmf']['credit'];
                }
                $yes_credit_list_all['602gm']['ip_count'] = $yes_credit_list_all['602gm']['ip_count']."/".$yes_credit_list_all['602gmf']['ip_count'];
            }
            if(intval($vvv['credit'])<=0&&intval($vvv['ip_count'])<=0){
                unset($yes_credit_list_all[$kkk]);
            }
        }
//        $yes_credit_list_all = $this->pai_xu($yes_credit_list_all);
        $_list = $yes_credit_list_all;
        if(!empty($_list_h)){
            $_list = array_merge($_list,$_list_h);
        }
        return $_list;
    }


    /**
     * 每个产品的更新判断（某天）
     * @param $ymd
     * @param int $type
     * @param int $types 类型， 6 ：导航
     * @return array
     */
    protected  function all_name($ymd,$type=0,$types=6){
        $s_ymd = strtotime($ymd);
        if($type==0){
            $w = "state=1 and datetime<{$s_ymd}";
        }else{
            $w = "datetime<{$s_ymd}";

        }
        if($types==6){
            $data = array ();
            $w .= " and type=6";
        }else{
            $w .= " and type<>6";
            $data = array ();
            $data['sign']['name'] = "签到";
            $data['sign']['gui_ze'] = "---";
            $data['sign']['datetype'] = "实时";
        }
//        $_name = M('promotion')->field('name,short_name,state,time_modify,rule')->where($w)->order("sort,id asc")->select();
        $_name = \Dao\Union\Promotion::get_instance()->get_list( 1, '`id`, `name`,`short_name`,`state`,`time_modify`,`rule`');
        $_arr_state = array();
        foreach ( $_name as $key => $val ) {
            $_datetype[$val['short_name']] = $val['time_modify'];
            $_datetype_gz[$val['short_name']] = $val['rule'];
            $_arr_state[$val['short_name']] = $val['state'];
            if(in_array($val['short_name'],array('yqjsy','jsie'))){
                continue;
            }
            $where[] = "'".$val['short_name']."'";
            $data[$val['short_name']]['name'] = $_name[$key]['name'];

        }

        //取最后更新时间
        $where[] = "'sign'";
        $where_type = implode(',',$where);
        $sql = "
            select
                dateline,`name`,is_get
            from
                credit_wait_confirm
            WHERE
                `name` in ({$where_type}) and ymd={$ymd}
            GROUP by
                `name`
            ORDER by
                dateline desc
        ";
        $info = \Dao\Union\Credit_wait_confirm::get_instance()->query( $sql );

        foreach($info as $v_info){
            $info_dateline[$v_info['name']]['dateline'] = $v_info['dateline'];
            $info_dateline[$v_info['name']]['is_get'] = $v_info['is_get'];
        }

        foreach($data as $key => $val){
            if(isset($info_dateline[$key]['dateline'])&&!empty($info_dateline[$key]['dateline'])&&$info_dateline[$key]['is_get']!=2){
                $data[$key]['dateline'] = date('Y-m-d H:i',$info_dateline[$key]['dateline']);
                $data[$key]['is_get'] = $info_dateline[$key]['is_get'];
                $data[$key]['is'] = 1;
                $data[$key]['ip_count'] = 0;
                $data[$key]['credit'] = 0;
                $data[$key]['status'] = 1;
            }else{
                if( $_arr_state[$key]==0){
                    $data[$key]['dateline'] = date('Y-m-d H:i',$s_ymd);
                    $data[$key]['is_get'] = 1;
                    $data[$key]['is'] = 1;
                    $data[$key]['ip_count'] = 0;
                    $data[$key]['credit'] = 0;
                    $data[$key]['status'] = 1;
                }else{
                    $data[$key]['dateline'] = '---';
                    $data[$key]['is_get'] = '';
                    $data[$key]['is'] = 0;
                    $data[$key]['ip_count'] = '---';
                    $data[$key]['credit'] = '---';
                    $data[$key]['status'] = 2;
                }
            }
            if($type==0){
                $data[$key]['datetype'] = $data[$key]['datetype']?$data[$key]['datetype']:$_datetype[$key];
                $data[$key]['gui_ze'] = $data[$key]['gui_ze']?$data[$key]['gui_ze']:$_datetype_gz[$key];
            }
        }
        return $data;
    }
}
