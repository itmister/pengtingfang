<?php
namespace Union\Point_grant;
/**
 * Created by JetBrains PhpStorm.
 * User: caolei
 * Date: 16-4-7
 * Time: 上午10:28
 * To change this template use File | Settings | File Templates.
 * 积分发放 有效量更新的模块
 */
class Point_grant{

    protected static $_instance = null;
    protected $kouBase = 0;
    private $softId_360se = [56386,56533,56534,56536,56542,107500,107842,107841,107840,108096,108097,108098,108101,107638,51540,51598,51664,52006,52615,52671,52680,52778,52949,53172,53194,53302,53392,53411,53895,54593,55233,57589,57835,58198,58199,58207,61324,67506,106319,106320,106407,106437,74948,58818,53600,57482,74945,106318,58814,58815,106475,54945,51628,106476,74949,53608,56198,51952,63769,52537,56411,63794,63791,63786,54951,107485,108942,108967,110698,110699,110700,110719,110763,110776,110777,110083,108715,108774,108378,108525,110009,108374,110140,110139,77829,77830,77852,77853,110013,77827,77828,110082,108741,110081,110010,77856,108824,108377,108772,108823,108740,110080,108380,108986,108987,77825,108876,110014,77847,77850,108997,110456,110493,110494,110495,110548,110549,110551,68124,68121,56129,57362,57363,61061,68114,68117,61128,56172,56140,56141,61126,61058,57358,61057,57346,57347,57348,57350,57355,57356,57374,56163,68126,68127,56161,56162,68128,64914,61062,56134,57380,57381,56164,56166,57375,56167,64861,61131,61130,67332,61133,67331,56171,56169,56138,57382,52301,52313,52315,56112,48718,51225,51422,49491,49497,56144,56102,52174,48473,63632,50958,63624,50013,50010,56109,48712,50978,50979,52454,50975,48805,48809,56026,56033,56133,56146,49101,51536,49271,48701,56151,49111,60967,49113,48683,48706,51419,56180,48704,56149,52230,52233,52245,55966,56071,67426,56069,48886,48908,53284,50955,53680,50351,54258,55257,52299,52303,52317,50968,52289,51227,56111,49489,49492,49498,49433,56101,56115,49281,52083,49122,56124,48471,48495,56110,56056,48713,52452,48802,48808,56132,52457,49100,56145,52227,50009,60969,50004,48711,49110,49112,48708,50002,52152,56150,56179,52232,52239,48702,51413,56147,67425,56100,52172,56136,52138,58092,58174,57807,48810,54955,68123,55750,53185,53188,49880,50163,50165,49682,56121,56123,56126,56127,53436,50293,50445,50450,53233,53235,53229,49777,50036,50986,54462,53334,50047,48502,55753,50079,53503,53505,59551,59552,53225,53228,50337,61214,53508,53510,53513,53514,53515,53519,59554,59556,59557,53418,59545,56375,49710,56284,56288,56290,56292,52692,51176,56280,56278,53570,56381,110593,110598,110599,110600,110619,110620,110621,59704,48813,48880,48965,48971,49351,49814,50134,50188,52062,52284,63581,63584,63590,63604,63618,67505,69436,106005,106251,106325,106334,106404,106416,106421,106424,106509,112456,112457,112461,112036,112037,112038,112122,112124,111533,106030,106051,53192,106031,49127,108983,49313,48667,48669,48761,52729,58833,51439,51809,110779,110782,110427,110703,110704,110712,110991,110992,110993,110506,110507,110775,110778,111049,111051,110455,110519,110429,110623,110625,110627,111054,110896,110897,110898,110552,110559,110367,110376,110283,110284,63776,48539,49721,48709,48910,59700,52779,49043,52489,52494,50149,51913,49952,49806,53083,53081,52939,52941,53085,53179,53180,53184,52847,52848,55741,55743,54231,53581,48739,52485,61350,53665,49407,49555,49556,50151,50152,50158,53671,49879,66953,49383,49384,49289,52672,67967,50017,50019,70088,70102,48741,48742];
    private $softId_360safe = [55531,55502,55514,55515,55516,55518,55521,55524,55520,55527,55535,55530,55549,55532,55544,55539];
    /**
     * @return \Union\Point_grant\Point_grant
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        $kouBaseInfo =\Dao\Union\Ad_fafang_time::get_instance()->select(array("field"=>'ip_count_deliter'));
        if($kouBaseInfo[0]['ip_count_deliter']>0){
            $this->kouBase = $kouBaseInfo[0]['ip_count_deliter'];
        }
    }
    /*
     * 手动上传，或自动上传 后 处理数据 在积分后台显示
     * $parameter = array(
        'ymd'=> 推广日期,
        'softId'=>软件简称
        'pId'=>软件 在promotion表的id，
        'fafangType' => 1 自动 2手动
        );
     * */
    public function run($parameter){
        $log_path = "/app/www/jf7654/Public/upload/log/point_grant/{$parameter['softId']}";
        if(is_file($log_path."/".$parameter['ymd'].".txt")){
            unlink($log_path."/".$parameter['ymd'].".txt");
        }
        //取数据
        //org_id 转换
        $class_name = 'Union\\Point_grant\\Change\\Change_'.$parameter['softId'];
        if (!class_exists($class_name)){
            $log_msg = "40001 not class {$class_name}";
            \Util\Tool::write_log($log_path,$log_msg,$parameter['ymd'].".txt");
            return false;
        }
        $object = $class_name::get_instance();
        $list = $object->get_data_to_vendor_org($parameter['softId'],$parameter['ymd']);
        if(empty($list)){
            $log_msg = "40002 not original data";
            \Util\Tool::write_log($log_path,$log_msg,$parameter['ymd'].".txt");
            return false;
        }
        $filePath = "/app/www/jf7654/Public/upload/PerUpload/{$parameter['softId']}/{$parameter['ymd']}.csv";
        if(is_file($filePath)){
            unlink($filePath);
        }
        \Io\File::array_to_csv($filePath,$list,array('渠道号','安装有效量','使用量'));

        $data  = $object->org_id_change_my_org_id($list);
        \Union\Stat\Performance_original::get_instance()->batch_add($data['arr_original'],$parameter['softId'],$parameter['ymd'], null);//保存原始发放量
        unset($data['arr_original']);
        if(empty($data['arr'])){
            $log_msg = "40003 not original to my org_id data";
            \Util\Tool::write_log($log_path,$log_msg,$parameter['ymd'].".txt");
            return false;
        }
        //org_id 匹配我们自己的uid
        $changeData = $object->org_id_change_uid($data['arr'],$parameter['softId'],$parameter['ymd']);
        if(empty($changeData)){
            $log_msg = "40004 not org_id to my uid data";
            \Util\Tool::write_log($log_path,$log_msg,$parameter['ymd'].".txt");
            return false;
        }
        //所有的uid
        $allUidList = array_keys($changeData);
        //取作弊的人 作弊标签 和黑名单
        $cheatUidList = \Union\Point_grant\Change\Change::get_instance()->get_cheat($allUidList,$parameter['softId']);

        //取扣量系数 如： array('软件id'=>系数);
        $xiShuInfo = \Dao\Union\Ad_fafang_config::get_instance()->select(array("field"=>"xishu","where"=>"name='{$parameter['softId']}'"));
        $xiShu = 1;//默认系数为1
        if($xiShuInfo[0]['xishu']){
            $xiShu = $xiShuInfo[0]['xishu'];
        }
        $adProductRecordFafangLog = array();
        $fafangSum = array();//实际发放人数 和有效量


        //特定用户特定软件结算系数
        $user_softid = \Dao\Union\User_softid::get_instance();
        $user_soft = $user_softid->select(array("field"=>"uid,sx","where"=>"pro_id = {$parameter['pId']}"));
        foreach($user_soft as $k=>$v){
            $soft_uid[] = $v['uid'];
            $user_soft_arr[$v['uid']] = $v['sx'];
        }
        foreach($changeData as $uid=>$num){
            if($num>$this->kouBase){
                $xs = $xiShu;
                //#360安全卫士
                if(in_array($uid,$this->softId_360safe)&&in_array($parameter['pId'],array(16))){
                    $xs = 1;
                }
                //#360安全浏览器
                if(in_array($uid,$this->softId_360se)&&in_array($parameter['pId'],array(17))){
                    $xs = 0.3;
                }
            }else{
                if(!empty($soft_uid) && in_array($uid,$soft_uid)){
                    $xs = $user_soft_arr[$uid];
                }else{
                    $xs = 1;
                }
            }
            $f_num = floor($xs*$num);
            $f_num = $f_num<=1?1:$f_num;
            $isZuobi = in_array($uid,$cheatUidList);
            array_push($adProductRecordFafangLog,array(
                'f_id'			=>'',
                'f_uid'			=>$uid,
                'f_promotion_id'=>$parameter['pId'],
                'f_num'			=>$f_num,
                'f_num_original'=>$num,
                'f_ymd'			=>$parameter['ymd'],
                'f_add_time'	=>time(),
                'f_stat'		=>1,
                'f_xishu'		=>$xs,
                'fafang'		=>$parameter['fafangType'],
                'zuobi'			=>$isZuobi?2:1 //作弊 1 未作弊  2已作弊
            ));
            if(!$isZuobi){
                $fafangSum[$uid] = $f_num;
            }
        }
        $adProductRecordFafang = array(
            'original_unum_all'		=>$data['original_count'],
            'original_num_all'		=>$data['original_sum'],
            'original_unum'		=>count($changeData),
            'original_num'		=>array_sum($changeData),
            'status_upload'		=>2,
            'status_sure'		=>1,
            'status_fafang'		=>1,
            'actual_unum'		=>count($fafangSum),
            'actual_num'		=>array_sum($fafangSum),
            'type'				=>$parameter['fafangType']==1?2:1,
            'ymd'				=>$parameter['ymd'],
            'add_time'			=>time(),
            'promotion_id'		=>$parameter['pId'],
            'promotion_name'	=>$parameter['softId'],
            'original_filename' =>$filePath
        );
        \Dao\Union\Ad_product_record_fafang::get_instance()->delete(array("promotion_id"=>$parameter['pId'],"ymd"=>$parameter['ymd']));
        \Dao\Union\Ad_product_record_fafang_log::get_instance()->delete(array("f_promotion_id"=>$parameter['pId'],"f_ymd"=>$parameter['ymd']));
        \Dao\Union\Ad_product_record_fafang::get_instance()->begin_transaction();
        $ret1 = \Dao\Union\Ad_product_record_fafang::get_instance()->add($adProductRecordFafang);
        $ret2 = \Dao\Union\Ad_product_record_fafang_log::get_instance()->addAll($adProductRecordFafangLog);
        if($ret1&&$ret2){
            \Dao\Union\Ad_product_record_fafang::get_instance()->commit();
            return true;
        }else{
            \Dao\Union\Ad_product_record_fafang::get_instance()->rollback();
            $log_msg = "40005 insert data error";
            \Util\Tool::write_log($log_path,$log_msg,$parameter['ymd'].".txt");
            return false;
        }
    }

    /*
     * 积分确认发放
     * $softId
     * $pId 产品表的id
     * $ymd
     * */
    public function product_sure($softId,$pId,$ymd){
        $log_path = "/app/www/jf7654/Public/upload/log/point_grant/{$softId}";
        if(is_file($log_path."/".$ymd.".txt")){
            unlink($log_path."/".$ymd.".txt");
        }
        //取待确认的数据
        $data =  \Dao\Union\Ad_product_record_fafang_log::get_instance()->query(
            "select f_uid,f_num,f_xishu,fafang from ad_product_record_fafang_log where f_promotion_id={$pId} and f_ymd={$ymd} and zuobi=1;"
        );
        if(empty($data)){
            $log_msg = "40006 not sure data";
            \Util\Tool::write_log($log_path,$log_msg,$ymd.".txt");
            return false;
        }
        //更新确认状态
        $softConfig = \Dao\Union\Promotion::get_instance()->query(
            "select type,credit_install,credit_online,credit_from_source,credit_rebate,is_credit,app_type from promotion where id={$pId};"
        );
        if(empty($softConfig[0])){
            $log_msg = "40007 not Promotion data";
            \Util\Tool::write_log($log_path,$log_msg,$ymd.".txt");
            return false;
        }
        $softConfigInfo = $softConfig[0];
        //安装
        $sub_type = 1;
        $price = 0;//产品单价
        if($softConfigInfo['credit_install']>0&&$softConfigInfo['credit_online']==0&&$softConfigInfo['credit_rebate']==0){
             $price = $softConfigInfo['credit_install'];
        }
        //使用 规定为导航
        if($softConfigInfo['credit_install']==0&&$softConfigInfo['credit_online']>0&&$softConfigInfo['credit_rebate']==0){
            $price = $softConfigInfo['credit_online'];
            $sub_type = 2;
        }
        //充值返利
        if($softConfigInfo['credit_install']==0&&$softConfigInfo['credit_online']==0&&$softConfigInfo['credit_rebate']>0
            &&$softConfigInfo['credit_from_source']==1&&$softConfigInfo['app_type']!=1){
            $price = $softConfigInfo['credit_rebate'];
            $sub_type = 3;
        }
        //安卓APP充值返利
        if($softConfigInfo['credit_install']==0&&$softConfigInfo['credit_online']==0&&$softConfigInfo['credit_rebate']>0
            &&$softConfigInfo['credit_from_source']==1&&$softConfigInfo['app_type']==1){
            $price = $softConfigInfo['credit_rebate'];
            $sub_type = 10;
        }
        //PE赚钱工具
        if(in_array($softConfigInfo['short_name'],['uds_dh','uds_sy','uds_sy_uefi'])){
            $price = $softConfigInfo['credit_online'];
            $sub_type = 11;
        }
        if($softConfigInfo['is_credit']==1){//发积分的软件
            #TODO 特权入口，取该软件的特权用户 条件是在这个推广时间
            $privilege = \Union\Point_grant\Privilege::get_instance();
            $arr_user_soft_privilege = $privilege->get_user_soft_config(
                array(
                'name' => $softId,//softID
                'ymd' => $ymd
                )
            );
            if(empty($arr_user_soft_privilege)){
                $arr_uid_privilege = array();
                $arr_privilege_per = array();
            }else{
                $arr_uid_privilege = $arr_user_soft_privilege['uid_arr'];
                $arr_privilege_per = $arr_user_soft_privilege['privilege'];
            }
        }else{//不发积分的软件
            $arr_uid_privilege = array();
            $arr_privilege_per = array();
        }
        if(empty($price)) return false;
        $orgIdUserArray = array();
        if(in_array($softId,array('jsdh'))){
            $orgIdList = \Dao\Union\Assign_orgid::get_instance()->query(
                "SELECT org_id,uid FROM assign_orgid WHERE softID='{$softId}' and FROM_UNIXTIME(dateline,'%Y%m%d')<={$ymd} and ((FROM_UNIXTIME(updateline,'%Y%m%d')>{$ymd} and `status`>0) or `status`=0);"
            );
            if($orgIdList){
                foreach($orgIdList as $_v){
                    $orgIdUserArray[$_v['uid']] = $_v['org_id'];
                }
            }
        }
        $credit_wait_confirm = array();
        $activity_hao123_vip_num_new = array();
        $credit_wait_confirm_no = array();
        foreach($data as $key=>$val) {
            $log = array();
            $data_no = array();
            $ip_count = intval($val['f_num']);
            $uid      = $val['f_uid'];
            if($softConfigInfo['is_credit']==1){//发积分的软件
                #TODO 特定用户 特定软件 特定价格
                if(in_array($uid,$arr_uid_privilege)&&$arr_privilege_per[$uid]>0){
                    $credit   = $ip_count * $arr_privilege_per[$uid];
                }else{
                    $credit   = $ip_count * $price;
                }
            }else{
                $credit = 0;
            }
            $log = array(
                'uid' 		=> $val['f_uid'],
                'credit'	=> $credit,
                'type'		=> 2,
                'sub_type'	=> $sub_type,
                'name'		=> $softId,
                'ip_count'	=> $sub_type==10?0:($sub_type==3?1:$ip_count),
                'is_get' 	=> 2,
                'ym'		=> date('ym', strtotime($ymd)),
                'ymd'		=> date('Ymd', strtotime($ymd)),
                'dateline'  => time(),
                'xishu'     => $val['f_xishu'],
                'fafang_type'    => $val['fafang']
            );
            $credit_wait_confirm[] = $log;

            $logDh = array(
                'uid' => $val['f_uid'],
                'tn' => $orgIdUserArray[$val['f_uid']]?$orgIdUserArray[$val['f_uid']]:"",
                'ip_count' => $ip_count,
                'ymd' => $ymd,
                'dateline' => time(),
                'name' => $softId
            );
            $activity_hao123_vip_num_new[] = $logDh;

            if($credit==0){
                $data_no = $log;
                #TODO 特定用户 特定软件 特定价格
                if(in_array($uid,$arr_uid_privilege)&&$arr_privilege_per[$uid]>0){
                    $data_no['credit']   = $ip_count * $arr_privilege_per[$uid];
                }else{
                    $data_no['credit']   = $ip_count * $price;
                }
                if($softConfigInfo['type']==6) $data_no['is_get'] = 0;
                $credit_wait_confirm_no[] = $data_no;
            }
        }
        //恢复积分
        $this->restoreCredit($softId,$ymd);
        //删除日志
        \Dao\Union\Point_grant_log::get_instance()->delete(array('ymd'=>$ymd,'soft_id'=>$softId));
        if(empty($credit_wait_confirm)){
            $log_msg = "40008 not credit data";
            \Util\Tool::write_log($log_path,$log_msg,$ymd.".txt");
            return false;
        }
        if($softConfigInfo['type']==6){
            $Ad_product_record_fafang = array('status_sure'=>2,'status_fafang'=>2,'sure_time'=>time());
        }else{
            $Ad_product_record_fafang = array('status_sure'=>2,'sure_time'=>time());
        }
        \Dao\Union\Credit_wait_confirm::get_instance()->begin_transaction();
        if(in_array($softId,array('jsdh'))){
            $ret1 = \Dao\Union\Activity_Hao123_Vip_Num_New::get_instance()->addAll($activity_hao123_vip_num_new);
        }else{
            $ret1 = \Dao\Union\Credit_wait_confirm::get_instance()->addAll($credit_wait_confirm);
        }
        if(!empty($credit_wait_confirm_no)){
            $ret2 = \Dao\Union\Credit_wait_confirm_no::get_instance()->addAll($credit_wait_confirm_no);
        }else{
            $ret2 = 1;
        }
        //增加统计同步
        \Dao\Stat\Union\Sync_task::get_instance()->ignore_add_all(array(0=>array('ymd'=>$ymd,'status'=>0,'dateline'=>time())));
        $ret3 = \Dao\Union\Point_grant_log::get_instance()->add(array('ymd'=>$ymd,'soft_id'=>$softId,'dateline'=>time()));
        $ret4 = \Dao\Union\Ad_product_record_fafang::get_instance()->update(array('ymd'=>$ymd,'promotion_id'=>$pId),$Ad_product_record_fafang);
        if($ret1&&$ret2&&$ret3&&$ret4){
            \Dao\Union\Credit_wait_confirm::get_instance()->commit();
            return true;
        }else{
            \Dao\Union\Credit_wait_confirm::get_instance()->rollback();
            $log_msg = "40009 insert credit data error";
            \Util\Tool::write_log($log_path,$log_msg,$ymd.".txt");
            return false;
        }
    }

    public function restoreCredit($vals,$time){
        $p = "/app/www/jf7654/Public/upload/log/kou_credit/".$vals;
        $ss = \Dao\Union\Credit_wait_confirm::get_instance()->query(
            "select * from credit_wait_confirm where ymd={$time} and is_get=1 and name='{$vals}';"
        );
        if(!empty($ss)){
            $deleteId = array();
            \Dao\Union\User_Credit_Log::get_instance()->query(
                "delete from user_credit_log where name='{$vals}' and ymd={$time};"
            );
            foreach($ss as $key => $va){
                if($va['delete_flag']==0&&$va['credit']>0){
                    $credit = $va['credit'];
                    $where = "id={$va['uid']} and credit>={$credit}";
                    $sql = "update user set credit=credit-{$credit},credit_total=credit_total-{$credit}  where {$where};";
                    $s =\Dao\Union\User::get_instance()->exec($sql);
                    if(!$s){
                        $u =\Dao\Union\User::get_instance()->query("select credit from user where id={$va['uid']} limit 1;");
                        $jifen = intval($u[0]['credit']);
                        $sql = "update user set credit=credit-{$jifen},credit_total=credit_total-{$credit}  where id={$va['uid']};";
                        \Dao\Union\User::get_instance()->exec($sql);
                    }
                    \Util\Tool::write_log($p,$sql,$time.".txt");
                }
                $deleteId[] = $va['id'];
            }
            if(!empty($deleteId)){
                $str = implode(',',$deleteId);
                \Dao\Union\Credit_wait_confirm::get_instance()->query(
                    "delete from credit_wait_confirm where id in ({$str});"
                );
            }
            \Union\Credit\Manager::get_instance()->delete($ss);//积分删除同步 vl@20150409
        }
        \Dao\Union\Credit_wait_confirm::get_instance()->query(
            "delete from credit_wait_confirm where name='{$vals}' and ymd={$time};"
        );
        \Dao\Union\Activity_Hao123_Vip_Num_New::get_instance()->query(
            "delete from activity_hao123_vip_num_new where name='{$vals}' and ymd={$time};"
        );
        \Dao\Union\Credit_wait_confirm_no::get_instance()->query(
            "delete from credit_wait_confirm_no where name='{$vals}' and ymd={$time};"
        );
        \Dao\Union\User_change_log::get_instance()->query(
            "delete from user_change_log where name='{$vals}' and ymd={$time};"
        );

        //删除用户的原始经验值
        $org_ss = \Dao\Union\User_change_log::get_instance()->query(
            "select * from org_empirical where name='{$vals}' and ymd={$time};"
        );
        $orgEmpiricalId = array();
        if(!empty($org_ss)){
            foreach($org_ss as $org_v){
                $org_empirical = $org_v['empirical'];
                $where = "uid={$org_v['uid']} and empirical>={$org_empirical}";
                $org_s =\Dao\Union\User_ext::get_instance()->exec(
                    "update user_ext set empirical=empirical-{$org_empirical}  where {$where};"
                );
                if(!$org_s){
                    \Dao\Union\User_ext::get_instance()->exec(
                        "update user_ext set empirical=0  where uid={$org_v['uid']};"
                    );
                }
                $orgEmpiricalId[] = $org_v['id'];
            }
            if(!empty($orgEmpiricalId)){
                $strO = implode(',',$orgEmpiricalId);
                \Dao\Union\User_change_log::get_instance()->query(
                    "delete from org_empirical where id in ({$strO});"
                );
            }
        }
    }
}
