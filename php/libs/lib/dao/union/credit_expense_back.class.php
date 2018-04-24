<?php
namespace Dao\Union;
use \Dao;
class Credit_expense_back extends Union {

    protected static $_instance = null;

    /**
     * @return Dao\Union\Credit_expense_back
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
}
