<?php
/**
 * 7654征集优秀猎人发赏金
 */
namespace Union\Manager;
use \Dao\Union\User;
use \Dao\Union\Credit_wait_confirm;
use \Dao\Channel_7654\Task;
use \Dao\Channel_7654\Manager_sgin;
use \Dao\Channel_7654\Task_user_log;
use Dao\Channel_7654\Manager_clock_in;

class Manager_task 
{
    protected static $_instance = null;
    
    /**
     * 用户模型
     */
    protected $_user_model;
    
    /**
     * 软件推广模型
     */
    protected $_credit_wait_confirm_model;
    
    /**
     * 市场经理任务模型
     */
    protected $_manager_task_model;
    
    public function __construct()
    {
        $this->_credit_wait_confirm_model = Credit_wait_confirm::get_instance();
    }
    
    public function user_log($uid)
    {
        if(!$uid)
        {
            return false;
        }
        $params = [ 'where' => 'uid = '.$uid ];
        $has_log = Task_user_log::get_instance()->find($params);
        if($has_log)
        {
            return false;
        }
        
        //记录用户日志
        $data = [
            'uid' => $uid,
            'dateline' => date('Y-m-d H:i:s')
        ];
        Task_user_log::get_instance()->add($data);
    
        return true;
    }
    
    /**
     * 市场经理任务列表
     * @param unknown $uid
     * @return boolean|\Dao\mixed
     */
    public function manager_task_list($uid)
    {
        if(!$uid)
        {
            return false;
        }
        
        $params = [
            'where' => 'uid ='.$uid.' AND task_aging = 1',
            'orderby' => 'task_start_time ASC'
        ];
        return \Dao\Channel_7654\Manager_task::get_instance()->select($params);
    }
    
    /**
     * 任务列表
     */
    public function task_list()
    {
        return Task::get_instance()->select();
    }
    
    /**
     * 开拓市场
     * 开拓新渠道：新增且资料信息完整的下属技术员
     * @param 正式市场经理邀请码   $invitecode
     * @param 任务开始                      $task_time
     */
    public function develop_new_markets($invitecode,$task)
    {
        if(!$invitecode || !$task['start_time'])
        {
            return false;
        }
        $where = "invitecode = '{$invitecode}' AND info_is_complete = 1 AND reg_dateline >= {$task['start_time']}";
        $subordinate_count = User::get_instance()->count($where);
        return (int)$subordinate_count;
    }
    
    /**
     * 夺精英
     * 下属有业绩：本月有业绩且资料完整技术员数
     * 任务要求：20个技术员
     * @param 正式市场经理    $uid
     * @param 任务开始           $task_time
     */
    public function elite_wins($invitecode,$task)
    {
        return $this->_credit_wait_confirm_model->fetch_subordinate_performance($invitecode, $task['start_time'],$task['last_uid']);
    }
    
    /**
     * 比耐心
     * 任务要求：8个技术员
     * 持之以恒：累计5天装机且资料完整技术员数
     * @param 正式市场经理    $invitecode
     * @param 任务开始           $start_time
     */
    public function perseverance($invitecode,$task)
    {
        return $this->_credit_wait_confirm_model->fetch_subordinate_promotion_day($invitecode, $task['start_time'],$task['last_uid']);
    }


    /**
     * 拼实力
     * 任务要求：200
     * 实力雄厚：指定软件（QQ管家、百度浏览器、快压线下包、快压线上包）总安装量
     * @param 正式市场经理    $uid
     * @param 任务开始           $task_time
     */
    public function spell_power($invitecode, $task)
    {
        return $this->_credit_wait_confirm_model->fetch_subordinate_promotion_specifie_software_sum($invitecode, $task['start_time']);
    }
    
    /**
     * 争持久
     * 持久装机：指定软件（QQ管家、百度浏览器、快压线下包、快压线上包）累计安装10天的技术员数
     * @param 正式市场经理    $uid
     * @param 任务开始           $task_time
     */
    public function for_persistent($invitecode, $task)
    {
        //上一次任务下属列表
        return $this->_credit_wait_confirm_model->fetch_subordinate_promotion_specifie_software_day($invitecode, $task['start_time'], $task['last_uid']);
    }
    
    /**
     * 证恒心
     * 脚踏实地，勇于坚持：打卡签到天数
     * @param 正式市场经理    $uid
     * @param 任务开始           $task_time
     */
    public function the_perseverance($uid,$task)
    {
        $end_time = date('Ymt',strtotime($task['start_time']));
        $params = [
            'where' => "uid = {$uid} AND status = 1 AND ymd BETWEEN {$task['start_time']} AND {$end_time}",
        ];
        return Manager_clock_in::get_instance()->count($params);
    }
    
    /**
     * 做任务
     * @param array $data
     * @return boolean
     */
    public function do_task($data)
    {
        if(!$data)
        {
            return false;
        }
        
        //当前任务
        $task_info = $this->task_info($data['task_id']);
        if($task_info['task_remaining_bonus'] == 0)
        {
            return -1;
        }
        //模型
        $manager_task_module = \Dao\Channel_7654\Manager_task::get_instance();
        
        //用户是否有相同的任务没有完成
        $params = [
            "where" => "uid = {$data['uid']} AND task_id = {$data['task_id']} AND task_status IN(1,2) AND task_aging = 1",
        ];
        $manager_task_info = $manager_task_module->find($params);
        if($manager_task_info)
        {
            return -2;
        }
        //单次任务奖金
        $data['task_bonus'] = $task_info['task_bonus'];
        $result = $manager_task_module->add($data);
        return $result;
    }
    
    /**
     * 领取奖金
     * @param array $data
     * @return boolean|number
     */
    public function complete_task($data)
    {
        if(!$data)
        {
            return false;
        }
        //模型
        $manager_task_module = \Dao\Channel_7654\Manager_task::get_instance();
        
        //开启事务
        $manager_task_module->begin_transaction();
        
        //当前任务
        $task_info = $this->task_info($data['task_id']);
        if($task_info['task_remaining_bonus'] == 0)
        {
            $manager_task_module->rollback();
            return -1;
        }
        
        //当前任务是否完成
        $where = "uid = {$data['uid']} AND task_id = {$data['task_id']} AND task_status = 2 AND task_aging = 1";
        $params = [
            "where" => $where,
        ];
        $manager_task_info = $manager_task_module->find($params);
        if(!$manager_task_info)
        {
            $manager_task_module->rollback();
            return -2;
        }  
        
        $result = $manager_task_module->update($where, $data);
        if(!$result)
        {
            //事务回滚
            $manager_task_module->rollback();
            return false;
        }
        
        //更新剩余奖金
        $task_where = "task_id = {$data['task_id']}";
        $task_remaining_bonus = $task_info['task_remaining_bonus'] - $task_info['task_bonus'];
        $task_set = array('task_remaining_bonus'=> $task_remaining_bonus);
        $task_result = Task::get_instance()->update($task_where, $task_set);
        if(!$task_result)
        {
            //事务回滚
            $manager_task_module->rollback();
            return false;
        }
        //提交事务
        $manager_task_module->commit();
        
        return true;
    }
    
    /**
     * 更新任务状态
     * @param integer $uid
     * @param integer $task_id
     * @return boolean
     */
    public function update_task_status($uid,$task_ids)
    {
        if(!$uid || !$task_ids)
        {
            return false;
        }
        $where = "uid = {$uid} AND task_id IN ({$task_ids}) AND task_status = 1 AND task_aging = 1";
        $result = \Dao\Channel_7654\Manager_task::get_instance()->update($where, array('task_status' => 2));
        return $result;
    }
    
    /**
     * 任务详细信息
     * @param integer $task_id
     * @return boolean|array
     */
    public function task_info($task_id)
    {
        if(!$task_id)
        {
            return false;
        }
        $sql = "SELECT * FROM task WHERE task_id = {$task_id} LIMIT 1 FOR UPDATE";
        return current(Task::get_instance()->query($sql));
    }
    
    /**
     * 记录
     * @param unknown $uid
     * @param unknown $uid_list
     * @param unknown $task_id
     * @return boolean
     */
    public function update_task_uid_list($uid,$uid_list,$task_id,$task_end_time)
    {
        if(!$uid || !$task_id || !$uid_list || !$task_end_time)
        {
            return false;
        }
        $uid_string = implode(',', json_decode($uid_list));
        
        $where = "uid = {$uid} AND task_id = {$task_id} AND task_status = 3 AND task_end_time = {$task_end_time}";
        $result = \Dao\Channel_7654\Manager_task::get_instance()->update($where, array('task_uid_list' => $uid_string));
    }
    
    /**
     * 统计数据
     */
    public function statistics($date)
    {
        $start_time = strtotime($date); //月初
        $end_time   = strtotime('+1 month',$start_time)-1;//月底
        
        $manager_task_list = [];
        //获取市场经理列表
        $sql = "SELECT userid,username,idcode,FROM_UNIXTIME(applytime, '%Y-%m-%d %H:%i:%s') AS reg_dateline FROM user_marketer WHERE status = 1";
        $manager_list = \Dao\Channel_7654\User_marketer::get_instance()->query($sql);
        foreach($manager_list as $key => $manager)
        {
            $task_sql = "
                SELECT COUNT(CASE WHEN task_status = 3 THEN task_id END) AS complete_num,
                SUM(CASE WHEN task_status = 3 THEN task_bonus END) AS task_bonus,
                COUNT(task_id) AS participate_num,task_id,MIN(task_start_time) AS start_time
                FROM manager_task WHERE uid = {$manager['userid']} AND 
                (task_start_time BETWEEN {$start_time} AND {$end_time} OR task_end_time BETWEEN {$start_time} AND {$end_time}) GROUP BY task_id ORDER BY task_id,task_start_time ASC
             ";
           //获取市场经理任务列表
            $task_data = \Dao\Channel_7654\Manager_task::get_instance()->query($task_sql);
            if($task_data)
            {
                foreach ($task_data as $task)
                {
                    if($task['task_id'] != 1)
                    {
                        $task['start_time'] = date('Ymd',$task['start_time']);
                    }
                    switch ($task['task_id'])
                    {
                        case 1 :
                            $complete_total = $this->develop_new_markets($manager['idcode'],$task);
                            break;
                        case 2 :
                            $complete_total = count($this->elite_wins($manager['idcode'],$task));
                            break;
                        case 3 :
                            $complete_total = count($this->perseverance($manager['idcode'],$task));
                            break;
                        case 4 :
                            $complete_total = $this->spell_power($manager['idcode'],$task);
                            break;
                        case 5 :
                            $complete_total = count($this->for_persistent($manager['idcode'],$task));
                            break;
                        case 6 :
                            $complete_total = $this->the_perseverance($manager['userid'],$task);
                            break;
                        default:
                            break;
                    }
                    
                    //市场经理任务信息（活动数据）
                    $task_info = [
                        'task_id'         => $task['task_id'],
                        'complete_total'  => (int)$complete_total,
                        'complete_num'    => (int)$task['complete_num'],
                        'participate_num' => (int)$task['participate_num'],
                        'task_bonus'      => (int)$task['task_bonus'],
                    ];
                
                    if(array_key_exists($manager['userid'], $manager_task_list))
                    {
                        $manager_task_list[$manager['userid']][$task['task_id']] = $task_info;
                    }
                    else
                    {
                        $manager_task_list[$manager['userid']] = $manager;
                        $manager_task_list[$manager['userid']][$task['task_id']] = $task_info;
                    }
                }
            }
            else
            {
                $manager_task_list[$manager['userid']] = $manager;
                
                //市场经理打卡
                $data['start_time'] = date('Ym01',strtotime($date));
                $complete_total = $this->the_perseverance($manager['userid'],$data);
                $task_info = [
                    'task_id'         => 6,
                    'complete_total'  => (int)$complete_total,
                    'complete_num'    => 0,
                    'participate_num' => 0
                ];
                $manager_task_list[$manager['userid']][6] = $task_info;
            }
        } 
        
        return $manager_task_list;
    }
    
}