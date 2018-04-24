<?php
namespace Dao\Union;
use Dao;

/**
 *  重点关注区域
 * Class area
 * @package Dao\Union
 */
class Point_area extends Union {
    use Dao\Orm;

    /**
     * @return Dao\Union\point_area
     */
    public static function get_instance(){
        return parent::get_instance();
    }


}