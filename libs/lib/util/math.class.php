<?php
namespace Util;

/**
 * 参数处理
 * Class Math
 * @package Util
 */

class Math {

    const PATTERN_TYPE_PHONE = '/1[0-9]{10}/';

    protected static $_instance = null;

    /**
     * @return \Util\Math
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function rand_number( $len = 8 ) {
        $result = '';
        for ($i=0; $i<$len;) {
            $num = mt_rand(0, 9);
            if (0 == $num && empty($result)) continue;
            $result .= $num;
            $i++;
        }
        return $result;
    }
}