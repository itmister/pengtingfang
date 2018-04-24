<?php
namespace Dao\Wallpaper;
use \Dao;
class Crawler_task extends Wallpaper {

    use \Dao\Orm;

    /**
     * @return Crawler_task
     */
    public static function get_instance(){ return parent::get_instance(); }

}
