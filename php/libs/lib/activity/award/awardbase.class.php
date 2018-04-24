<?php
namespace Activity\Award;

class AwardBase extends \Activity\Component {
    private static $_instace = [];
    public $award_info = [];  //生成的奖励的信息
    public static function get_instance($class = __CLASS__){
        if(isset(self::$_instace[$class])){
            return self::$_instace[$class];
        }else {
            self::$_instace[$class]=new $class();
        }
        return self::$_instace[$class];
    }

    public function build_award(\Activity\Engine $engine){
        $this->coverError(-1, "你必须重载此方法");
        return false;
    }

    public function send(\Activity\Engine $engine) {
        $this->coverError(-1, "你必须重载此方法");
        return false;
    }
}