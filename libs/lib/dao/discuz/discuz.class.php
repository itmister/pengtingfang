<?php
/**
 * discuz
 */
namespace Dao\Discuz;
use Dao\Dao;

class Discuz extends Dao {
    protected $_connection_key = 'DB_DISCUZ';
    protected $_prefix = '';
	
	protected static $_instance = null;
	
	/**
     * @return Dao\Union\Credit_Stat
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
	
}