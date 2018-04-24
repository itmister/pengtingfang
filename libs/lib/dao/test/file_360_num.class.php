<?php
namespace Dao\Test;
use \Dao;
class File_360_num extends Test{

    protected static $_instance = null;

    /**
     * @return Dao\Test\File_360_num
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    
}
