<?php
namespace Union\Stat;

/**
 * 统计-日志,记录一些中间数据便于统计分析
 * Class Log
 * @package Union\Stat
 */

class Log {

    /**
     * 注册
     */
    const type_register = 'register';


    /**
     * 增加日志
     * @param $type
     * @param $data
     */
    public function add( $type , $data ) {

        switch ( $type ) {
            case self::type_register:
                //注册

                break;
        }

    }
}