<?php
namespace Activity\Condition;

class ConditionBase extends \Activity\Component {
    private static $_instace = [];

    public static function get_instance($class = __CLASS__){
        if(isset(self::$_instace[$class])){
            return self::$_instace[$class];
        }else {
            self::$_instace[$class]=new $class();
        }
        return self::$_instace[$class];
    }

    public function check_condition(\Activity\Engine $engine) {
        $this->coverError(-1, "你必须重载此方法");
        return false;
    }
}