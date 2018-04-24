<?php
namespace Activity\Limit;

class LimitBase extends \Activity\Component {
    private static $_instace = [];
    public $limit_info = []; //限量信息，可以供 condtion 和 award 类中使用
    public static function get_instance($class = __CLASS__){
        if(isset(self::$_instace[$class])){
            return self::$_instace[$class];
        }else {
            self::$_instace[$class]=new $class();
        }
        return self::$_instace[$class];
    }

    public function check_limit(\Activity\Engine $engine) {
        $this->coverError(-1, "你必须重载此方法");
        return false;
    }

    public function update_limit(\Activity\Engine $engine){
        $this->coverError(-1, "你必须重载此方法");
        return false;
    }

    public function get_limit_info(){
        return $this->limit_info;
    }
}