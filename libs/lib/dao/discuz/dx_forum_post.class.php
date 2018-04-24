<?php
namespace Dao\Discuz;
class Dx_forum_post extends  Discuz {

    protected static $_instance = null;

    /**
     * @return Dx_forum_post
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
}
