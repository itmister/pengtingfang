<?php
namespace io;

/**
 * cookie操作
 * @Author vl
 * @Date 2015-09-15
 *
 * Class cookie
 * @package io
 */
class cookie {

    public static function get($name) {
        $value = isset($_COOKIE[$name]) ? $_COOKIE[$name] : '';
        $value = addslashes($value);
        return $value;
    }

    public static function set($name, $value) {
        $cookie_cfg = \Config::get('cookie');

        if ( empty($cookie_cfg['domain']) ) {
            $arr = array_reverse( explode('.', $_SERVER['SERVER_NAME']) );
            $cookie_domain = $arr[1] . '.' . $arr[0];
        }
        else {
            $cookie_domain =  $cookie_cfg['domain'];
        }
        if (null == $value && isset($_COOKIE[$name])) unset ($_COOKIE[$name]);
        return setcookie($name, $value, time() + 86400 * 30, '/', $cookie_domain );
    }

}