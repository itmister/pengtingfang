<?php
namespace Dao\Union;
use Dao;

/**
 *  微信红包
 * Class User_weixin_hongbao_config
 * @package Dao\Union
 */
class User_weixin_hongbao_config extends Union {
    use Dao\Orm;

    /**
     * @return Dao\Union\User_weixin_hongbao_config
     */
    public static function get_instance(){
        return parent::get_instance();
    }
}