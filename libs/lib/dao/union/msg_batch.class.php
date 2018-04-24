<?php
/**
 * 站内信
 */
namespace Dao\Union;
use \Dao;

class Msg_batch extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Msg_batch
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    /**
     * 新增站内信
     * (non-PHPdoc)
     * @see \Dao\Dao::add()
     */
    public function add($array) {
        if (empty($array)) return false;
        $time = time();
        $data = array(
            'type'      => $array['type'],
            'is_return' => $array['is_return'],
            'username'  => $array['username'],
            'content'   => $array['content'],
            'inputtime' => $time
        );
        $data = array_filter($data);
        return parent::add($data);
    }
}
