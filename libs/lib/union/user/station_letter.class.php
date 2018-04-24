<?php
namespace Union\User;

/**
 * 站内信
 * Class Station_letter
 * @package Union\Stat\User
 */

class Station_letter 
{
    /**
     * 站内信批次表模型
     */
    protected $msgBatchModel;
    
    /**
     * 站内信表模型
     */
    protected $msgDataModel;
    
    public function __construct()
    {
        $this->msgBatchModel = \Dao\Union\Msg_batch::get_instance();
        $this->msgDataModel  = \Dao\Union\Msg_data::get_instance();
    }
    
    /**
     * 添加站内信
     * @param array   $data
     * @param integer $uid
     * @param integer $bind_uid
     * @return boolean
     */
    public function add($data,$user_id,$from_user_id)
    {
        if(empty($data) || !$user_id || !$from_user_id)
        {
            return false;
        }
        
        $batch_id = $this->msgBatchModel->add($data);
        if(!$batch_id)
        {
            return false;
        }
        
        $msg_id = $this->msgDataModel->add($batch_id, $user_id, $from_user_id);
        if(!$msg_id)
        {
            //删除站内信批次记录
            $msg_batch_where = "msg_batch_id = {$batch_id}";
            $this->msgBatchModel->delete($msg_batch_where);
            
            return false;
        }
        
        return true;
    }
    
    /**
     *批量添加
     * @param aray $data
     * @param array $user_list
     * @return boolean|Ambigous <boolean, mysqli_result>
     */
    public function add_all($data,$user_list){
        if(empty($data) || empty($user_list)){
            return false;
        }
        //添加站内信批次信息
        $batch_id = $this->msgBatchModel->add($data);
        if(!$batch_id)
        {
            return false;
        }
        
        foreach ($user_list as $key => $user){
            $user_list[$key]['batch_id']  = $batch_id;
            $user_list[$key]['inputtime'] = time();
        }
        $query = $this->msgDataModel->add_all($user_list);
        return $query;
    }
    
}