<?php
namespace Union\Performance\Platform;
use \Dao\Stat\Union\Performance_original;

/**
 * 推广平台基类
 * Class Base
 * @package Union\Performance\Platform
 */

class Base {

    /**
     * 核对平台
     * @param $data
     */
    public function platform_check( &$data ) {
        foreach ( $data as &$row ) {
            $row['union_platform_id'] = 1;//默认为7654平台
            $row['is_other'] = 0;
        }
    }

    public function platform_update( $data ) {
        Performance_original::get_instance()->add_all_duplicate_update( $data );
    }

}