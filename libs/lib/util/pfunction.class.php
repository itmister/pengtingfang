<?php
/**
 * @desc Pfunction 公共类
 */
namespace Util;

class Pfunction {

    /**
     * @desc  参数转成时间戳 $name = '2009-02-01';
     * @return int
     */    
    public static function getParamDateToTime($name, $defaultValue = null) {
        $return = self::getParam($name, $defaultValue);
        return strtotime($return);
    }

    /**
     * @desc 转正整数
     * @param type $name
     * @param type $defaultValue
     * @return int
     */
    public static function getParamForAbsIntval($name, $defaultValue = 0) {
        $return = self::getParam($name, $defaultValue);
        return intval(abs($return));
    }

    /**
     * @desc 转html标签为可显示字符
     * @param type $name
     * @return string
     */
    public static function getParamFilter($name) {
        return htmlentities(trim(self::getParam($name)), ENT_COMPAT, 'utf-8');
    }

    /**
     * @desc 转html标签为可显示字符
     * @param string $name
     * @return string
     */
    public static function getParamStripslashes($name) {
        $result = htmlspecialchars(stripslashes(self::getParam($name)));
        return $result;
    }

    /**
     * @desc 封装post，get方法;
     * @param type $name
     * @param type $defaultValue
     * @return string
     */
    public static function getParam($name, $defaultValue = null) {
        return isset($_GET[$name]) ? $_GET[$name] : (isset($_POST[$name]) ? $_POST[$name] : $defaultValue);
    }
}