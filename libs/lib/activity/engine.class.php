<?php
namespace Activity;
class Engine extends Component{
    private static $_instace  = null;
    private $_uid  = '';
    private $_activity_info  = [];
    private $_rule_info = [];
    private $_user_info = [];


    public $limit_model    = null;
    public $conditon_model = null;
    public $award_model    = null;

    /**
     * 引擎单例
     * @param $uid              用户id
     * @param $activity_info    活动信息
     * @param $rule_info        规则信息
     * @return Engine|null
     * @throws \Exception
     */
    public static function get_instance($uid,$activity_info,$rule_info){
        if(isset($_instace)){
            return self::$_instace;
        }else {
            $object =new self($uid,$activity_info,$rule_info);
           if ($object -> _init()){
                self::$_instace = $object;
            }else{
                $error = $object->getErrorInfo();
                throw new \Exception($error['error'],$error['errno']);
            }
        }
        return self::$_instace;
    }

    /**
     * 构造函数
     * @param $uid
     * @param $activity_info
     * @param $rule_info
     */
    private function __construct($uid,$activity_info,$rule_info){
        $this->_uid = $uid;
        $this->_user_info = \Dao\Union\User::get_instance()->get_user_info_by_id($uid);
        $this->_activity_info = $activity_info;
        $this->_rule_info = $rule_info;
    }

    /**
     * 初始化引擎（检查各个模型是否正常配置）
     * @return bool
     */
    private function _init(){
        $limit_model =  $this->_rule_info['limit_model'];
        if (!class_exists($limit_model)){
            $this->coverError("-2001","limit model  not exist");
            return false;
        }
        $limit_model = $limit_model::get_instance();
        if ( ! is_a($limit_model,'\\Activity\\Limit\\LimitBase')){
            $this->coverError("-2002","limit model  not extend LimitBase ");
            return false;
        }
        $this->limit_model = $limit_model;
        $conditon_model =  $this->_rule_info['condition_model'];
        if (!class_exists($conditon_model)){
            $this->coverError("-2003","conditon model  not exist");
            return false;
        }
        $conditon_model = $conditon_model::get_instance();
        if ( ! is_a($conditon_model,'\\Activity\\Condition\\ConditionBase')){
            $this->coverError("-2004","conditon model  not extend ConditionBase ");
            return false;
        }
        $this->conditon_model = $conditon_model;
        $award_model =  $this->_rule_info['award_model'];
        if (!class_exists($award_model)){
            $this->coverError("-2005","award model  not exist AwardBase");
            return false;
        }
        $award_model = $award_model::get_instance();
        if ( !is_a($award_model,'\\Activity\\Award\\AwardBase')){
            $this->coverError("-2006","limit model  not extend AwardBase ");
            return false;
        }
        $this->award_model = $award_model;
        return true;
    }

    public function get_user_info(){
        return $this->_user_info;
    }

    public function get_uid(){
        return $this->_uid;
    }

    public function get_activity_info(){
        return $this->_activity_info;
    }

    public function get_rule_info(){
        return $this->_rule_info;
    }
    
    public function get_award_info(){
    	return $this->award_model->award_info;;
    }

    /**
     * 检查限量
     */
    public function check_limit(){
        if(! $this->limit_model -> check_limit($this)){
            $error = $this->limit_model->getErrorInfo();
            $this->setErrorInfo($error);
            return false;
        }
        return true;
    }

    /**
     * 任务参与资格判断
     * @return boolean
     */
    public function check_condition(){
        if (!$this->conditon_model -> check_condition($this)){
            $error =$this->conditon_model->getErrorInfo();
            $this->setErrorInfo($error);
            return false;
        }
        return true;
    }

    /**
     * 生成奖品存到对应奖品类型的奖品array里头
     * @return boolean
     */
    public function build_award(){
        $ret = $this->award_model->build_award($this);
        if (!$ret){
            $error = $this->award_model->getErrorInfo();
            $this->setErrorInfo($error);
            return false;
        }
        return true;
    }

    /**
     * 更新限量
     */
    public function check_limit_and_update(){
        if (!$this->check_limit()){
            return false;
        }
        $ret = $this->limit_model->update_limit($this);
        if (!$ret){
            $error = $this->limit_model->getErrorInfo();
            $this->setErrorInfo($error);
        }
        return $ret;
    }

    /**
     * 发奖
     * @return boolean
     */
    public function send_award(){
        $ret = $this->award_model->send($this);
        if (!$ret){
            $error = $this->award_model->getErrorInfo();
            $this->setErrorInfo($error);
            return false;
        }
        return true;
    }

    /**
     * run
     * @return bool
     */
    public function run(){
        //检查限量
        $ret = $this->check_limit();
        if(!$ret) {
            return false;
        }
        //判断参与资格
        $ret =  $this->check_condition();
        if(!$ret) {
            return false;
        }
        //生成奖品
        $ret = $this->build_award();
        if(!$ret) {
            return false;
        }
        //启动事物
        \Dao\Union\Act_Mod_Join_Info::get_instance()->begin_transaction();
        //第二次检查限量和更新限量
        $ret1 = $this->check_limit_and_update();
        if(!$ret1) {
            \Dao\Union\Act_Mod_Join_Info::get_instance()->rollback();
            return false;
        }
        //派发奖品
        $ret2 = $this->send_award();
        if(!$ret2) {
            \Dao\Union\Act_Mod_Join_Info::get_instance()->rollback();
            return false;
        }
        \Dao\Union\Act_Mod_Join_Info::get_instance()->commit();
        return true;
    }
}