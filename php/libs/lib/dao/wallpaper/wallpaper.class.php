<?php
/**
 * 壁纸数据库
 */
namespace Dao\Wallpaper;
use Dao\Dao;

class Wallpaper extends Dao {
    protected $_connection_key = 'DB_wallpaper';
    protected $_prefix = '';
}