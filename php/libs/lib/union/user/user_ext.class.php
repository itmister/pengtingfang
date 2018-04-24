<?php
namespace Union\User;

/**
 * 用户扩展
 * Class User_ext
 * @package Union\User\User_ext
 */

class User_ext {
	
    protected static $_instance = null;
    
    //等级区间
    protected $grade = array(
    		'LV1' => 100001,
    		'LV2' => 1000001,
    		'LV3' => 10000001,
    		'LV4' => 30000001,
    		'LV5' => 60000001,
    		'LV6' => 100000001,
    		'LV7' => 300000001,
    		'LV8' => 900000001,
    		'LV9' => 900000001,
    );

    /**
     * @return Union\User
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
    /**
	* 经验值区间
    * @param $emp
    * @return int lv
    */
    public function get_grade_by_empirical($emp){
    	/* $config_grade = $this->grade;
    	if($emp<$config_grade['LV1']){
    		return 1;
    	}elseif($emp<$config_grade['LV2']&&$emp>=$config_grade['LV1']){
    		return 2;
    	}elseif($emp<$config_grade['LV3']&&$emp>=$config_grade['LV2']){
    		return 3;
    	}elseif($emp<$config_grade['LV4']&&$emp>=$config_grade['LV3']){
    		return 4;
    	}elseif($emp<$config_grade['LV5']&&$emp>=$config_grade['LV4']){
    		return 5;
    	}elseif($emp<$config_grade['LV6']&&$emp>=$config_grade['LV5']){
    		return 6;
    	}elseif($emp<$config_grade['LV7']&&$emp>=$config_grade['LV6']){
    		return 7;
    	}elseif($emp<$config_grade['LV8']&&$emp>=$config_grade['LV7']){
    		return 8;
    	}elseif($emp>=$config_grade['LV8']){
    		return 9;
    	} */
        
        $params = [
            "where"   => "min_emp <= {$emp} AND max_emp >= {$emp}",
        ];
        $grade_info  = \Dao\Union\Config_grade::get_instance()->find($params);
        return $grade_info;
    }
    
    /**
     * 锁定用户等级
     * @param int $uid
     * @param boolean $ignore_lock_grade //是否忽略已经存在的lock_grade
     * @return int
     */   
    public function lock_grade($uid,$ignore_lock_grade = true){
    	$where = "uid = $uid";
    	$ext = \Dao\Union\User_ext::get_instance()->select($where);
    	if($ext){
    		$ext = $ext[0];
	    	if(!$ignore_lock_grade && $ext['lock_grade']){
	    		$grade = $ext['lock_grade'];
	    	}else{
	    		$empirical = $ext['empirical'];//用户经验值
	    		$grade = $this->get_grade_by_empirical($empirical);//用户等级
	    		\Dao\Union\User_ext::get_instance()->update($where, array('lock_grade' => $grade['grade']));
	    	}
    	}
    }
}