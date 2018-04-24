<?php
namespace Union\User;

/**
 * 
 * Class message_question
 * @package Union\Stat\User
 */

class Message_question {
    /**
     * 提问表模型层
     */
    protected $messageQuestionModel;
    
    /**
     * 提问回答表模型层
     */
    protected $messageContentModel;
    
    public function __construct(){
        $this->messageQuestionModel = \Dao\Union\Message_question::get_instance();
        $this->messageContentModel  = \Dao\Union\Message_content::get_instance();
    }
    
    /**
     * 获取总记录数
     * @param int $user_id
     */
    public function fetch_count_by_uid($user_id) {
       $count = $this->messageQuestionModel->fetch_count_by_uid($user_id);
       if(!isset($count['tp_count']))
           return false;
       
       return $count['tp_count'];
    }
    
    public function fetch_message_list($where = "1 = 1",$field = '*',$page_start = 0,$page_end = 10,$order = 'q_ask_time DESC'){
        $message_list = $this->messageQuestionModel->fetch_message_list($where,$field,$page_start,$page_end,$order);
        return $message_list;
    }
    
    /**
     * 获取提问类型列表
     * @return \Dao\mixed
     */
    public function message_type_ask_list(){
        $query_sql = "SELECT * FROM `message_type_ask`";
        $list = $this->messageQuestionModel->query($query_sql); 
        return $list;
    }
    
    /**
     * 问题详情
     * @param string $id
     */
    public function detail($id){
        if(!$id) return false;
        $message_info = $this->messageQuestionModel->fetch_message_by_id($id);
        return $message_info;
    }
    
    /**
     * 获取回答内容
     */
    public function fetch_content($c_id,$c_type = 1){
        if(!$c_id) return false;
        $content = $this->messageContentModel->fetch_content_by_id($c_id,$c_type);
        return $content;
    }
    
    /**
     * 添加问题
     * @param array $question_data
     * @return boolean
     */
    public function add_question($question_data){
        if(!$question_data) return false;
        $query_result = $this->messageQuestionModel->add($question_data);
        return $query_result;
    }
    
    /**
     * 添加问题内容
     * @param array $content_data
     * @return boolean
     */
    public function add_content($content_data){
        if(!$content_data) return false;
        $query_result = $this->messageContentModel->add($content_data);
        return $query_result;
    }
    
    public function get_count($uid){
        if(!$uid) return false;
        $date = date('Ymd');
        $params = [
            "where" => "q_uid = {$uid} AND q_ymd = {$date}"
        ];
        return $this->messageQuestionModel->count($params);
    }
}