<?php
namespace Dao\Channel_7654;
use \Dao;
class Log_apply_bind_old_user extends Channel_7654 {

    protected static $_instance = null;

    /**
     * @return Dao\Channel_7654\Log_apply_bind_old_user
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    /**
     * 添加绑定申请记录
     * (non-PHPdoc)
     * @see \Dao\Dao::add()
     */
    public function add($user) {
        if (empty($user)) return false;
        $time = time();
        $data = array(
            'uid'       => $user['uid'],
            'name'      => $user['name'],
            'bind_uid'  => $user['bind_uid'],
            'bind_name' => $user['bind_name'],
            'has_bind'  => 0,
            'ymd'       => date('Ymd', $time),
            'created'   => $time
        );
        return parent::add($data);
    }
    
    /**
     * 获取一条记录
     * @param integer $uid
     * @param integer $bind_uid
     * @param integer $has_bind
     * @param string  $filed
     * @return boolean|Ambigous <multitype:, mixed>
     */
    public function find($uid = 0,$bind_uid = 0,$has_bind,$filed = "*"){
        $where = '';
        if($uid)        $where.="`uid` = {$uid}";               //市场经理id
        if($bind_uid)   $where.=" AND `bind_uid` = {$bind_uid}"; //老技术员id
        if($has_bind)   $where.=" AND `has_bind` = ".$has_bind; //绑定状态
        if(!$where)     return false;
        
        $where = trim($where,' AND ');
        $sql = "SELECT {$filed} FROM {$this->_get_table_name()} WHERE {$where} ORDER BY `created` DESC LIMIT 1";
        $query_result = $this->query($sql);
        
        return $query_result ? current($query_result) : array();
    }
    
    /**
     * 删除绑定记录
     * @param int $uid      用户id
     * @param int $bind_uid 绑定用户id
     * @return boolean
     */
    public function detete($uid,$bind_uid){
        if(!$uid || !$bind_uid) return false;
        $where = "uid = {$uid} AND bind_uid = {$bind_uid}";
        return parent::delete($where);
    }
    
    /**
     * 更新
     * (non-PHPdoc)
     * @see \Dao\Dao::update()
     */
    public function update($uid = '',$bind_uid,$set_data,$has_bind = ''){
        if(!$bind_uid || !is_array($set_data)){
            return false;
        }
        if($uid && $bind_uid){
            $where = "`uid` = {$uid} AND `bind_uid` = {$bind_uid}";
        }else if($bind_uid && is_numeric($has_bind)){
            $where = "`bind_uid` = {$bind_uid} AND `has_bind` = {$has_bind}";
        }
        if(!$where) return false;
        
        return parent::update($where, $set_data);
    }
    
    /**
     * 获取多条记录
     * @param string $where
     * @param string $filed
     * @return boolean|array
     */
    public function select($where = "",$filed = "*"){
        if(!$where) return false;
        
        $sql = "SELECT {$filed} FROM {$this->_get_table_name()} WHERE {$where}";
        $query_result = $this->query($sql);

        return $query_result ? $query_result : array();
    }
    
    public function count($where = ""){
        if(!$where) return false;
        
        $sql = "SELECT COUNT(*) AS num FROM {$this->_get_table_name()} WHERE {$where}";
        $query_result = current( $this->query($sql) );
        return $query_result ? $query_result['num'] : 0;
    }
}
