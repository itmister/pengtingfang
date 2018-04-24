<?php
namespace Union\Performance\Third;

/**
 * 业绩
 * Class Factory
 * @package Union\Performance\Third
 */

class Factory {

    /**
     * @param string $software 软件名
     * @param array $option 选项
     * @return Base
     */
    public static function create( $software, $option = [] ) {
        $cls_performance = '\\Union\\Performance\\Third\\' . ucfirst(strtolower($software));
        if ( class_exists($cls_performance) ) {
            $obj_performance = new $cls_performance( $option );
            if ( method_exists($obj_performance, 'fetch') ) return $obj_performance;
        }
        return null;
    }
}