<?php
namespace Util\data;

/**
 * 统计-经理人业绩
 * Class Virtual
 * @package Util\data
 */

class Virtual {

    /**
     * 虚拟表格式数据
     * @param $arr_structure
     * @param integer $line 行数
     * @return array
     */
    public static function table( $arr_structure, $line = 10 ) {
        $result = array();
        for ( $i = 0; $i <= $line; $i++ ) {
            $item = array();
            foreach ( $arr_structure as $field => $cfg ) {
                if ( isset($cfg['default']) ) {

                    $value = $cfg['default'];
                }
                else {
                    switch ($field) {
                        case 'ym' :
                            $value = date('Y-m', strtotime("-{$i} month"));
                            break;

                        case 'ymd' :
                            $value = date('Y-m-d', strtotime("-{$i} day"));
                            break;

                        default:
                            $value = mt_rand(1, 10000);
                            break;

                    }
                }

                $item[ $field ] = $value;
            }

            $result[] = $item;
        }
        return $result;
    }
}