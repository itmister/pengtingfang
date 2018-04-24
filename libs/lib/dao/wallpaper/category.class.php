<?php
namespace Dao\Wallpaper;
use \Dao;
class Category extends Wallpaper {
    use Dao\Orm;

    /**
     * @return Category
     */
    public static function get_instance(){ return parent::get_instance(); }
}
