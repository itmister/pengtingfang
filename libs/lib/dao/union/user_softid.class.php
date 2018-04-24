<?php
namespace Dao\Union;
use Dao;

/**
 * Class User_softid
 * @package Dao\User_softid
 */
class User_softid extends Union {
    protected static $_instance = null;

    /**
     * @return Dao\Union\User_softid
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    public function promotion_id(){
        $sql = "SELECT pro_id FROM user_softid GROUP BY pro_id";
        $data = $this->query($sql);
        foreach($data as $value){
            $pro_id[] = $value['pro_id'];
        }
        return $pro_id;
    }
    /**
     * 用户软件列表
     */
    public function user_soft_list($where,$start,$num){
        $sql = "SELECT a.*,b.`name`,c.`name` AS user_name FROM user_softid AS a LEFT JOIN promotion AS b ON a.pro_id = b.id LEFT JOIN `user` AS c ON a.uid = c.id WHERE {$where} ORDER BY a.id DESC LIMIT {$start},{$num}";
        $data = $this->query($sql);
        return $data;
    }
    /**
     * user_soft_add
     */
    public function user_soft_add($uid,$softID,$dateline,$sx,$pro_id){
        $sql = "INSERT INTO {$this->_realTableName}(`uid`,`softID`,`dateline`,`sx`,`pro_id`) VALUE('{$uid}','{$softID}','{$dateline}','{$sx}','{$pro_id}') ON DUPLICATE KEY UPDATE `dateline` = {$dateline}";
        return $this->query($sql);
    }

    public function user_soft_add_cover($uid,$softID,$dateline,$sx,$pro_id){
        $sql = "INSERT INTO {$this->_realTableName}(`uid`,`softID`,`dateline`,`sx`,`pro_id`) VALUE('{$uid}','{$softID}','{$dateline}','{$sx}','{$pro_id}') ON DUPLICATE KEY UPDATE `dateline` = {$dateline} , `sx` = {$sx}";
        return $this->query($sql);
    }
}