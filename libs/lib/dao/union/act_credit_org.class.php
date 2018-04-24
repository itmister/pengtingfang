<?php
namespace Dao\Union;
use \Dao;
class Act_Credit_Org extends Union {

    protected static $_instance = null;

    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }

    public function add_org_data($data){
        return $this->add_all($data);
    }

    public function get_org_data($apply_id,$uids = '',$in = true){
        $where = '';
        if ($uids){
            if($in){
                $where = "and uid in(".$uids.")";
            }else{
                $where = "and uid not in(".$uids.")";
            }
        }
        $sql = "select sum(credit) as num from {$this->_realTableName} where apply_id = {$apply_id}  $where";
        $ret = $this->query($sql);
        return $ret[0]['num'];
    }


    public function get_sum($apply_id,$status = null){
        $where = '';
        if ($status != null){
            $where  = 'and status ='.$status;
        }
        $sql = "select sum(credit) as num from {$this->_realTableName} where apply_id = {$apply_id} $where ";
        $ret = $this->query($sql);
        return $ret[0]['num'];
    }

    public function update_org_data($apply_id,$uids = '',$in = true){
        $status = 0;
        $where = '';
        if ($uids){
            if($in){
                $where = "and uid in(".$uids.")";
            }else{
                $where = "and uid not in(".$uids.")";
            }
        }else{
            $status = 1;
        }
        $ret = $this->update("apply_id = {$apply_id} $where",['status'=>$status]);
        return $ret;
    }

    /**
     * 明细
     * @param $apply_id
     * @param string $uids
     * @param bool $in
     * @return mixed
     */
    public function get_org_list($apply_id,$uids = '',$in = true,$status = null,$ad_status = null){
        $where = '';
        if ($uids){
            if($in){
                $where = " and uid in(".$uids.")";
            }else{
                $where = " and uid not in(".$uids.")";
            }
        }
        if ($status !== null){
            $where .=" and status =".$status;
        }
        if ($ad_status !== null){
            $where .=" and ad_status =".$ad_status;
        }
        $sql = "select * from {$this->_realTableName} where apply_id = {$apply_id}  $where";
        $ret = $this->query($sql);
        return $ret;
    }

    public function update_org($id,$data){
        return $this->update('id='.$id,$data);
    }
}
