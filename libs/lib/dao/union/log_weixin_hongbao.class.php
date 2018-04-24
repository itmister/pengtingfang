<?php
namespace Dao\Union;
use Dao;

/**
 *  微信红包
 * Class Act_weixin_hongbao
 * @package Dao\Union
 */
class Log_weixin_hongbao extends Union {
    use Dao\Orm;

    /**
     * @return Dao\Union\Act_weixin_hongbao
     */
    public static function get_instance(){
        return parent::get_instance();
    }
}