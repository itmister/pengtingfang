<?php
namespace Util;

/**
 * 处理时间日期的一些函数
 * Class Datetime
 * @package Util
 */

class Datetime {

    /**
     * 取某一个月有多少天
     * @param string $ym 年月，如201503 或 1503
     * @return int
     */
    public static function count_month_days( $ym ) {

        $len = strlen( $ym );
        if ( $len != 4  &&  $len != 6 ) return 0;
        if ( $len = 4 ) $ym = '20' . $ym;
        $ymd = $ym . '01';
        $result = ( strtotime('+1 month',  strtotime( $ymd )) - strtotime( $ymd )  ) / 86400;
        return $result;

   }

    /**
     * 将秒数转换成时间(xxxx年xxx天xx小时xx分xx秒
     * @param int $second
     * @return integer
     */
    public static function second_to_time( $second ) {
        if ( 0 == ( $second = intval($second) ) || $result = '') return '';
        foreach ( [['年',31556926], ['天',86400], ['小时', 3600], ['分', 60], ['秒', 1] ] as $item ) if ( $value = floor($second / $item[1]) ) {
            $result .= "{$value}{$item[0]}";
            $second = $second % $item[1];
        }
        return $result;
    }

    /**
     * 日期表达式取得年月日
     * @param $str_date_time
     * @param string $default
     * @param string $date_format
     * @return bool|string
     */
    public static function get_ymd( $str_date_time, $default = '', $date_format = 'Ymd'){
        $dateline = strtotime( $str_date_time );
        if ( empty($dateline) ) $dateline = strtotime( $default );
        return date($date_format, $dateline);
    }

    /**
     * 增加取星期函数 vl@20150625
     * @param $time
     * @param string $prefix
     * @return string
     */
    public static function get_weekday( $time, $prefix = '星期' ) {
        $arr_week   = ['日','一', '二', '三', '四', '五', '六'];
        $w = date('w', $time);
        if (!isset($arr_week[$w])) return '';
        return $prefix . $arr_week[$w];
    }

    public static function now() {
        return date('Y-m-d H:i:s');
    }

    public static function ymd_now() {
        return date('Ymd');
    }
}