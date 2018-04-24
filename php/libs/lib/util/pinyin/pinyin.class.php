<?php
namespace Util\Pinyin;
class Pinyin {

    protected static $_data = [];

    public static function init() {
        if ( empty(self::$_data) ) self::$_data = include_once(dirname(__FILE__) . '/data.php');
    }

    /**
     * 中文转拼音
     * @param $str
     * @param string $separate 分隔符
     * @return string
     */
    public static function to_pinyin( $str, $separate = '' ) {
        $result = [];
        $str = trim( $str );
        if ( ( $len = mb_strlen($str, 'utf-8') ) <= 0 ) return '';

        self::init();
        $data = self::$_data;
        for ( $i=0; $i< $len; $i++){
            $chr = mb_substr($str, $i, 1, 'utf-8');
            if (isset( $data[$chr]) ) $result[] = $data[$chr];
        }
        return implode($separate, $result);
    }

    /**
     * 文字转拼单首字母编写
     * @param string $str
     * @return string
     */
    public static function to_pinyin_first( $str ) {
        $result = '';
        $str = trim( $str );
        if ( ( $len = mb_strlen($str, 'utf-8') ) <= 0 ) return $result;
        self::init();
        $data = self::$_data;
        for ($i=0; $i<$len; $i++){
            $chr = mb_substr($str, $i, 1, 'utf-8');
            if (isset($data[$chr])) {
                $result .= substr($data[$chr], 0, 1);
            }
        }
        return $result;
    }

    /**
     * 第一个汉子输入后面的缩写
     * @param $str
     * @return string
     */
    public static function to_pinyin_first2( $str ) {
        $result = '';
        $str = trim( $str );
        if ( ( $len = mb_strlen($str, 'utf-8') ) <= 0 ) return $result;
        self::init();
        $data = self::$_data;
        $result = $data[mb_substr($str, 0, 1, 'utf-8')];;
        for ($i=1; $i<$len; $i++){
            $chr = mb_substr($str, $i, 1, 'utf-8');
            if (isset($data[$chr])) {
                $result .= substr($data[$chr], 0, 1);
            }
        }
        return $result;
    }

    /**
     * 取拼音第一个字母
     * @param $str
     * @return string
     */
    public static function first_letter( $str ) {
        $result = '';
        $str = trim( $str );
        if ( ( $len = mb_strlen($str, 'utf-8') ) <= 0 ) return $result;
        self::init();
        $data = self::$_data;

        $world = isset( $data[mb_substr($str, 0, 1, 'utf-8')] ) ? $data[mb_substr($str, 0, 1, 'utf-8')] : '';
        if (empty($world)) return $result;
        return substr( $world, 0, 1);
    }
}