<?php
namespace Dao\Union;
use \Dao;
class User extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\User
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;

    }


    /**
     * 增加用户积分
     * @param $id
     * @param $credit
     * @return bool|int
     */
    public function add_credit($id,$credit){
        $table_name = $this->_get_table_name();
        $sql = "update {$table_name} set credit= credit+{$credit},credit_total=credit_total+{$credit} WHERE id = {$id} limit 1";
        return $this->exec($sql);
    }


    /**
     * 根据idcode 取用户信息
     * @param $idcode
     * @return array|mixed
     */
    public function get_user_info_by_idcode( $idcode ) {
        if (empty($idcode)) return array();
        $table_name = $this->_get_table_name();
        $data = $this->query("select * from {$table_name} where idcode='{$idcode}' LIMIT 1" );
        return !empty($data) ? current( $data ) : array();
    }

    /**
     * 根据注册ip获取用户列表
     * @param $reg_ip
     * @return array
     */
    public function get_user_info_by_regip($reg_ip){
        if (empty($reg_ip)) return array();
        $table_name = $this->_get_table_name();
        $data = $this->query("select * from {$table_name} where reg_ip='{$reg_ip}'" );
        return !empty($data)?$data:[];
    }

    
   /**
    * @desc 取用户信息
    * @param type $arr_uid
    * @return type
    */
    public function get_user_info_by_uids( $arr_uid ) {
        if (empty($arr_uid)) return array();
        $uids = implode(',', $arr_uid);
        $table_name = $this->_get_table_name();
        $sql = "select * from {$table_name} where id IN ({$uids})";
        $data = $this->query( $sql );
        return $data ? $data : array();
    }
    
    /**
     * 根据用户名获取用户信息
     * @param string $name
     * @return integer
     */
    public function get_user_info_by_name ( $name , $field = "*" ) {
        if( !$name )  return array();

        $user_info = $this->query("SELECT {$field} FROM {$this->_get_table_name()} WHERE `name` = '{$name}' LIMIT 1");
        return !empty( $user_info ) ? current( $user_info ) : array();
    }

    /**
     * 更改用户状态
     * @param $status
     */
    public function change_user_status($uid,$status){
        if(!$uid) return false;
        return  $this->update("id = {$uid}",['status'=>$status]);
    }

    /**
     * 根据用户名id获取用户信息
     * @param string $name
     * @return integer
     */
    public function get_user_info_by_id( $id , $field = "*" ) {
        if( !$id && !is_int( $id )) return array();
        $data = $this->query("SELECT {$field} FROM {$this->_get_table_name()} WHERE `id` = '{$id}' LIMIT 1");
        $user_info = !empty( $data ) ? current( $data ) : array();
        //修正邀请码有大小写问题 vl@20150710
        if ( !empty($user_info['idcode']) ) $user_info['idcode'] = strtolower( $user_info['idcode'] );
        if ( !empty($user_info['invitecode']) ) $user_info['invitecode'] = strtolower( $user_info['invitecode'] );
        return $user_info;
    }

    /**
     * 设置资料是否完整标识
     * @param $uid
     * @param $is_complete
     */
    public function set_info_is_complete($uid, $is_complete) {
        $uid = intval($uid);
        $this->update("id={$uid}", array('info_is_complete' => $is_complete));
    }
    
    /**
     * 根据用户名和密码获取用户信息
     * @param string $name
     * @return integer
     */
    public function get_user_info_by_name_password ( $name , $password , $field = "*" ) {
        if( !$name || !$password)  return array();
    
        $user_info = $this->query("SELECT {$field} FROM {$this->_get_table_name()} WHERE `name` = '{$name}' AND `password` = '{$password}' LIMIT 1");
        return !empty( $user_info ) ? current( $user_info ) : array();
    }
    
    /**
     * 获取用户信息
     * @param array $parmas 查询参数
     * @param string $field 查询字段
     * @return boolean|Ambigous <multitype:, mixed>
     */
    public function get_user_info_by_parmas($parmas,$field = "*"){
        $where = '';
        //用户id
        if($parmas['id']){
            $where .= " AND `id` = ".trim($parmas['id']);
        }
        //用户名
        if($parmas['name']){
            $where .= " AND `name` = '{$parmas['name']}'";
        }
        //手机号
        if($parmas['phone']){
            $where .= " AND `phone` = ".trim($parmas['phone']);
        }
        //邀请码
        if($parmas['idcode']){
            $where .= " AND `idcode` = '{$parmas['idcode']}'";
        }
        
        if(!$where){
            return false;
        }
        $where = trim($where,' AND ');
        $user_info = $this->query("SELECT {$field} FROM {$this->_get_table_name()} WHERE {$where}");
        
        return !empty( $user_info ) ? current( $user_info ) : false;
    }
    
    /**
     * 设置用户邀请码（默认为市场经理邀请）
     * @param unknown $uid
     * @param unknown $invitecode
     * @param unknown $bind_dateline
     * @param number $invitetype
     * @return boolean
     */
    public function set_user_invitecode( $uid,$invitecode,$bind_dateline,$invitetype = 1 ){
        if(!$uid || !$invitecode || !$bind_dateline) return false;
        
        $where = "id = {$uid}";
        $set_data = [
            'invitecode'    => $invitecode,
            'invitetype'    => $invitetype,
            'bind_dateline' => $bind_dateline,
        ];
        $this->update($where,$set_data);
    }
    
    /**
     * 获取list
     * @param string $where
     * @param string $fields
     * @return array
     */
    public function select($where = true,$fields='*'){
    	$table_name = $this->_get_table_name();
    	$sql = "select {$fields} from {$table_name} where {$where}";
    	$data = $this->query( $sql );
    	return $data ? $data : array();
    }
    
    /**
     * 获取数目
     * @param str $where
     */
    public function count($where){
    	$table_name = $this->_get_table_name();
    	$sql = "select count(*) as count from {$table_name} where {$where}";
    	$count = $this->query( $sql );
    	return $count[0]['count'];
    }

    public function kou_credit($credit,$credit_total,$uid){
        $sql = "update user set credit=credit-{$credit},credit_total=credit_total-{$credit_total} where id = {$uid}; ";
        return $this->query($sql);
    }

    public function update_credit($credit,$uid){
        $sql = "update user set credit=credit+{$credit} where id = {$uid}; ";
        return $this->query($sql);
    }
    
    /**
     * 获取当月市场经理下属技术员人数
     * @param $invite_uid
     * @param $ym
     * @param int $info_is_complete
     */
    public function get_new_technician_count($invitecode, $ym ,$info_is_complete = false){
        if(!$invitecode || !$ym){
            return false;
        }
        $ymd_start = strtotime(date('Ym01',strtotime($ym)));
        $ymd_end   = strtotime(date('Ymt',strtotime($ym)));
        
        $sql = "SELECT COUNT(id) AS num FROM {$this->_get_table_name()} WHERE invitecode = '{$invitecode}' AND invitetype = 1 AND reg_dateline BETWEEN {$ymd_start} AND {$ymd_end}";
        if($info_is_complete){
            $sql.= " AND info_is_complete > 0";
        }
        $query_result = current($this->query($sql));
        return isset($query_result['num']) ? $query_result['num'] : 0;
    }
    
    /**
     * 获取下属黑名单
     * @param string $invitecode
     * @return boolean
     */
    public function get_technician_blacklist($invitecode){
        if(!$invitecode){
            return false;
        }
        $sql = "SELECT COUNT(id) AS num FROM {$this->_get_table_name()} WHERE invitecode = '{$invitecode}' AND invitetype = 1 AND status = 0";
        $query_result = current($this->query($sql));
        return isset($query_result['num']) ? $query_result['num'] : 0;
    }

    /*
     * 判断是不是虚拟手机号
     * */
    public function check_user_name($name){
        if(!$name) return false;
        $day = date("Ymd");
        $sql = "select reg_ip as ip from {$this->_get_table_name()} where FROM_UNIXTIME(reg_dateline,'%Y%m%d')={$day} and name LIKE '{$name}%';";
        return $this->query($sql);
    }
}
