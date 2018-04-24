<?php
namespace Dao\Union;
use Dao;

/**
 *  cdn上传
 * Class Cdn_upload
 * @package Dao\Union
 */
class Cdn_upload extends Union {
    use Dao\Orm;

    /**
     * @return Dao\Union\Cdn_upload
     */
    public static function get_instance(){
        return parent::get_instance();
    }
}