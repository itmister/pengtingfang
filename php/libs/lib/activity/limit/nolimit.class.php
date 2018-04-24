<?php
namespace Activity\Limit;

/**
 * 不限制次数
 * Class Nolimit
 * @package Activity\Limit
 */
class Nolimit extends LimitBase {
    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }

    public function check_limit(\Activity\Engine $engine) {
        return true;
    }

    public function update_limit(\Activity\Engine $engine){
        return true;
    }
}