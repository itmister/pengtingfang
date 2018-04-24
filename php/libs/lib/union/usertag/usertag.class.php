<?php
namespace Union\Usertag;
class UserTag {
    protected static $_instance = null;

    /**
     * @return Union\Usertag\UserTag
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * 添加标签
     * @param $uid
     * @param $username
     * @param $t_id
     * @param string $promotion_id
     * @param string $promoton_name
     * @param string $note
     * @return bool
     */
    public function add($uid,$username,$t_id,$promotion_id = null,$promoton_name=null,$note =''){
        $promotion_id = $promotion_id ? $promotion_id : null;
        $promoton_name = $promoton_name ? $promoton_name : null;
        //用户已经存在这个标签了
        if (\Dao\Union\User_Tags::get_instance()->userTag($uid,$t_id,$promotion_id,1)){
            return false;
        }
        //用户已经被限制登录（在黑名单里头）不需要打额外标签
        $userinfo = \Dao\Union\User::get_instance()->get_user_info_by_id($uid);
        if ($userinfo['status'] == 0){
            return false;
        }
        $taginfo  = \Dao\Union\Tags::get_instance()->tag_info($t_id);
        //有互斥标签的时候
        if ($taginfo['mutex']){
            $mutexs =  explode(',',$taginfo['mutex']);
            foreach ($mutexs as $mutex){
                if (!trim($mutex)) continue;
                $mutex_taginfo = \Dao\Union\Tags::get_instance()->tag_info($mutex);
                //该用户身上是否有互斥标签
                $user_mutex_taginfo = \Dao\Union\User_Tags::get_instance()->userTag($uid,$mutex,$promotion_id);
                if($user_mutex_taginfo){ //有互斥标签
                    //如果优先级比用户身上互斥的优先级低则不打标签
                    if ($taginfo['mutex_priority'] < $mutex_taginfo['mutex_priority']){
                        \Dao\Union\User_Tags::get_instance()->rollback();
                        return false;
                    }else{ //优先级高的话则删除 互斥标签
                        \Dao\Union\User_Tags::get_instance()->delete_tag($uid,$mutex,$promotion_id);
                        $d = [
                            'c_tagtype' => $mutex,
                            'c_type' => 2,
                            'c_uid'=>$uid,
                            'promotion'=>$promoton_name,
                            'c_addtime'=>time(),
                            'note'=>'互斥标签优先级低被删除',
                        ];
                        \Dao\Union\Usertag_changelog::get_instance()->add_log($d);
                    }
                }
            }
        }
        \Dao\Union\User_Tags::get_instance()->begin_transaction();
        if($t_id == 3){//受限用户
            \Dao\Union\User::get_instance()->change_user_status($uid,3);
        }elseif($t_id == 4){//黑名单
            \Dao\Union\User::get_instance()->change_user_status($uid,0);
        }
        //嫌疑和作弊标签
        if ($t_id <= 2){
            $b_data = [
                'uid'=>$uid,
                'dateline'=>time(),
                'delete_flag'=>0,
                'name'=>$promoton_name,
                'type'=>2
            ];
            \Dao\Union\Hao123_blackname::get_instance()->add($b_data,true);
        }
        $data = [
            'uid'=>$uid,
            'username'=>$username,
            't_id'=>$t_id,
            'promotion_id'=>$promotion_id,
            'promotion_name'=>$promoton_name,
            'ctime'=>date("Y-m-d H:i:s")
        ];
        $ret1 = \Dao\Union\User_Tags::get_instance()->add_tag($data);
        $d = [
            'c_tagtype' => $t_id,
            'c_type' => 1,
            'c_uid'=>$uid,
            'promotion'=>$promoton_name,
            'c_addtime'=>time(),
            'note'=>$note,
        ];
        $ret2 = \Dao\Union\Usertag_changelog::get_instance()->add_log($d);
        if ($ret1 && $ret2){
            \Dao\Union\User_Tags::get_instance()->commit();
            return true;
        }else{
            \Dao\Union\User_Tags::get_instance()->rollback();
            return false;
        }
    }

    /**
     * 删除标签
     * @param $uid
     * @param $username
     * @param $t_id
     * @param string $promotion_id
     * @return bool
     */
    public function  delete_tag($uid,$username,$t_id,$promotion_id = '',$promoton_name='',$note =''){
        \Dao\Union\User_Tags::get_instance()->begin_transaction();
        $ret1 = \Dao\Union\User_Tags::get_instance()->delete_tag($uid,$t_id,$promotion_id);
        if($t_id == 4){//黑名单
            \Dao\Union\User::get_instance()->change_user_status($uid,1);
        }elseif($t_id == 3){//受限用户
            \Dao\Union\User::get_instance()->change_user_status($uid,1);
        }
        //嫌疑和作弊标签
        if ($t_id <= 2){
            \Dao\Union\Hao123_blackname::get_instance()->remove_blackname($uid,$promoton_name);
        }
        $d = [
            'c_tagtype' => $t_id,
            'c_type' => 2,
            'c_uid'=>$uid,
            'promotion'=>$promoton_name,
            'c_addtime'=>time(),
            'note'=>$note,
        ];
        $ret2 = \Dao\Union\Usertag_changelog::get_instance()->add_log($d);
        if ($ret1 && $ret2){
            \Dao\Union\User_Tags::get_instance()->commit();
            return true;
        }else{
            \Dao\Union\User_Tags::get_instance()->rollback();
            return false;
        }
    }


    /**
     *  增加ip标签
     * @param $ip
     * @param $t_id
     * @param string $promotion_id
     * @param string $promoton_name
     * @param string $note
     * @return bool
     */
    public function add_ip_tag($ip,$t_id,$promotion_id = '',$promoton_name='',$note =''){
        $promotion_id = $promotion_id ? $promotion_id : null;
        $promoton_name = $promoton_name ? $promoton_name : null;
        $ret  = \Dao\Union\Iptag_Config::get_instance()->get_ip_tag_ext($ip,$t_id,$promotion_id);
        if(!empty($ret)){
            return false;
        }
        $info = \Dao\Union\Iptag_Source::get_instance()->get_ip($ip);
        $id = $info['id'];
        \Dao\Union\Iptag_Config::get_instance()->begin_transaction();
        if (!$id){
            $id = \Dao\Union\Iptag_Source::get_instance()->add_ip($ip);
            if (!$id){
                \Dao\Union\Iptag_Config::get_instance()->rollback();
                return false;
            }
        }
        $data = [
            'c_uid'=>$id,
            'c_uname'=>$ip,
            'c_tagtype'=>$t_id,
            'c_promotion_id'=>$promotion_id,
            'c_promotion_name'=>$promoton_name,
            'c_stat'=>1,
            'c_addtime'=>time(),
            'note'=>$note,
        ];
        $ret1 = \Dao\Union\Iptag_Config::get_instance()->add_tag($data);

        if ($ret1){
            \Dao\Union\Iptag_Config::get_instance()->commit();
            return true;
        }else{
            \Dao\Union\Iptag_Config::get_instance()->rollback();
            return false;
        }
    }
}
