<?php
namespace Dao\Union;
use \Dao;

/**
 * TN操作历史记录
 * @package Dao\Union
 */
class Tn_Op_Hist extends Union
{
    protected static $_instance = null;

    /**
     * @return Dao\Union\Tn_Op_Hist
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function  add_tn_op_hist($tn,$admin_id,$op_type,$old_info,$new_info){
        $data['tn'] = $tn;
        $data['admin_id'] = $admin_id;
        $data['old_info'] =$old_info;
        $data['new_info'] =$new_info;
        $data['op_type'] =$op_type;
        $data['dateline'] =time();
        return $this->add($data);
    }

}
