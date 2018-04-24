<?php
namespace Dao\Union;
use Dao;

/**
 *  发送微信成功消息
 * Class Weixin_success_news
 * @package Dao\Union
 */
class Weixin_success_news extends Union {
    use Dao\Orm;

    /**
     * @return Dao\Union\weixin_success_news
     */
    public static function get_instance(){
        return parent::get_instance();
    }


}