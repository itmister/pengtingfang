<?php
namespace Dao\Union;
use Dao;

/**
 * Class Empirical_name_config
 * @package Dao\Act_Badge
 */
class Empirical_name_config extends Union {
    protected static $_instance = null;

    /**
     * @return Dao\Union\Empirical_name_config
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}