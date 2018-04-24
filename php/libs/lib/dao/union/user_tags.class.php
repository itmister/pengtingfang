<?php
namespace Dao\Union;
use \Dao;

/**
 * @package Dao\Union
 */
class User_Tags extends Union {
    protected static $_instance = null;

    /**
     * @return \Dao\Union\User_Tags
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function add_tag($data){
        $sql = "INSERT INTO {$this->_realTableName} (uid,username,t_id,promotion_id,promotion_name,ctime) VALUES
                        ({$data['uid']},'{$data['username']}',{$data['t_id']},'{$data['promotion_id']}','{$data['promotion_name']}','{$data['ctime']}')
        ON DUPLICATE KEY UPDATE status = 1,ctime='{$data['ctime']}'";
        return $this->exec($sql);
    }

    public function getUserTags($uid){
        $sql = "select * from {$this->_realTableName} where uid = {$uid}";
        return $this->query($sql);
    }

    public function getList($conditions,$offset,$prepage = 20){
        $where = "1";
        if ($conditions['tagids']){ //带产品的标签 将和 标签做或处理
            $str1 = $this->_field_to_str($conditions['tagids']);
            if ($conditions['t_p']){
                $str2 = $this->_field_to_str($conditions['t_p']);
                $where .= " AND (a.t_id in({$str1}) OR (concat(a.t_id,'_',a.promotion_id) in ({$str2})))";
            }else{
                $where .= " AND (a.t_id in({$str1}))";
            }
        }else{
           if ( $conditions['t_p']){
               $str = $this->_field_to_str($conditions['t_p']);
               $where .= " AND (concat(a.t_id,'_',a.promotion_id) in ({$str}))";
           }
        }
        unset($conditions['t_p'],$conditions['tagids']);
        foreach ($conditions as $k => $v) {
            if($k != 'reg_ip'){
                if($k == 'uid'){
                    $where .= " AND a.{$k} in ({$v})";
                }else{
                    $where .= " AND a.{$k} = '{$v}'";
                }
            } else{
                $where .= " AND b.{$k} = '{$v}'";
            }
        }
        $sql  = "select a.*,max(a.ctime) as ctime,b.reg_ip,b.reg_location,concat(a.t_id,a.promotion_name) as t_p from {$this->_realTableName} a
                  left join user b on b.id=a.uid where {$where} group by a.uid  ORDER by ctime desc limit {$offset},{$prepage}";
        return $this->query($sql);
    }


    /**
     * 获取用户某个标签
     * @param $uid
     * @param $tagid
     * @param string $promotion_id
     * @param intger $status
     * @return array
     */
    public function userTag($uid,$tagid,$promotion_id = '',$status = 1){
        if ($promotion_id){
            $a_sql = " AND promotion_id = {$promotion_id} ";
        }
        if ($status !== null){
            $a_sql .= " AND status = {$status} ";
        }
        $sql = "select * from {$this->_realTableName} where uid = {$uid} and t_id = {$tagid} $a_sql ";
        $ret = $this->query($sql);
        if ($promotion_id){
        	return $ret[0]?$ret[0]:[];
        }else{
        	return $ret ? $ret : [];
        }
    }

    /**
     * 获取一个用户标签类型
     * @param $uid
     * @return mixed
     */
    public function get_user_tags($uid){
        $sql = "select DISTINCT t_id from {$this->_realTableName} where uid = {$uid} and status = 1 ";
        return $this->query($sql);
    }

    /**
     * 获取用户某个标签
     * @param $uid
     * @param $tagid
     * @param string $promotion_id
     * @return array
     */
    public function userTagPrometionName($uid,$tagid){
    	$sql = "select a.*,b.name as pname from {$this->_realTableName} a left join promotion b on a.promotion_id = b.id where a.uid = {$uid} and a.t_id = {$tagid} and status =1";
    	$ret = $this->query($sql);
    	return $ret ? $ret : [];
    }

    /**
     * 获取用户某个标签
     * @param $uid
     * @return array
     */
    public function getUserTagPrometion($uid){
    	$sql = "select *,group_concat(promotion_id) as promotion_ids,group_concat(promotion_name) as promotion_names from {$this->_realTableName} where uid = {$uid} and status =1 group by t_id";
        return $this->query($sql);
    }

    public function delete_tag($uid,$t_id,$promotion_id=''){
        if ($promotion_id){
            $a_sql = " AND promotion_id = {$promotion_id} ";
        }
        $where  = "uid = {$uid} and t_id = {$t_id} $a_sql ";
        $ret = $this->update($where,['status'=>0,'ctime' => date("Y-m-d H:i:s")]);
        return $ret;
    }

    public function getCount($conditions){
        $where = "1";
        if ($conditions['tagids']){ //带产品的标签 将和 标签做或处理
            $str1 = $this->_field_to_str($conditions['tagids']);
            if ($conditions['t_p']){
                $str2 = $this->_field_to_str($conditions['t_p']);
                $where .= " AND (a.t_id in({$str1}) OR (concat(a.t_id,'_',a.promotion_id) in ({$str2})))";
            }else{
                $where .= " AND (a.t_id in({$str1}))";
            }
        }else{
            if ( $conditions['t_p']){
                $str = $this->_field_to_str($conditions['t_p']);
                $where .= " AND (concat(a.t_id,'_',a.promotion_id) in ({$str}))";
            }
        }
        unset($conditions['t_p'],$conditions['tagids']);
        foreach ($conditions as $k => $v) {
            if($k != 'reg_ip'){
                if($k == 'uid'){
                    $where .= " AND a.{$k} in ({$v})";
                }else{
                    $where .= " AND a.{$k} = '{$v}'";
                }
            } else{
                $where .= " AND b.{$k} = '{$v}'";
            }
        }
        $sql  = "select count(DISTINCT  a.uid) as num from {$this->_realTableName} a left join user b on b.id=a.uid  where {$where} ";
        $ret = $this->query($sql);
        return $ret[0]['num']?$ret[0]['num']:0;
    }
    
    /**
     * 获取标签
     * @param string $where
     */
    public function getTags($where){
		$sql = "select GROUP_CONCAT(DISTINCT name) as cheat_soft_name from {$this->_realTableName} LEFT JOIN promotion as b on promotion_name=b.short_name where $where";
        return $this->query($sql);
    }
}
