<?php
namespace Union\Performance\Platform;
use \Core\Object;
use \Dao\Stat\Union\Performance_original;
use \Dao\Stat\Union\Tn_rule;

/**
 * 推广平台基类
 * Class Base
 * @package Union\Performance\Platform
 */

class Handler extends Object{


    /**
     * @return Handler
     */
    public static function i( $option = []) { return parent::i( $option ); }

    public function analyze_all( $ymd ) {
        $result                   = [];
        $dao_performance_original = Performance_original::get_instance();
        $software_list = $dao_performance_original->fields('software')->group_by('software')->where(['ymd' => $ymd])->find();
        foreach ( $software_list as $row ) $result[$row['software']] = $this->analyze_software( $ymd, $row['software'] );
        return $result;
    }


    public function analyze_software( $ymd, $software ) {
        $dao_performance_original   = Performance_original::get_instance();
        $rule_list                  = Tn_rule::get_instance()->rule($software);
        $data_list                  = $dao_performance_original->fields('id,tn')->where( ['ymd' => $ymd, 'software'=> $software] )->find();
        $data_update                = [];
        foreach ( $rule_list as $rule ) {
            switch ( $rule['type'] ) {
                case 1://对应
                    foreach ( $data_list as $key => $data ) if ( $data['tn'] == $rule['data'] ) {
                        $data_update[] = [ 'id' => $data['id'], 'union_platform_id' => $rule['union_platform_id'], 'is_other' => 1, 'status' => 1 ];
                        unset($data_list[$key]);
                    }
                    break;
                case 2://连续范围
                    foreach ( $data_list as $key => $data ) {
                        $tn = intval( $data['tn'] );
                        if ( $tn >= $rule['data']['start'] && $tn <= $rule['data']['end'] ) {
                            $data_update[] = [ 'id' => $data['id'], 'union_platform_id' => $rule['union_platform_id'], 'is_other' => 1, 'status' => 1];
                            unset($data_list[$key]);
                        }
                    }
                    break;
                case 3://指定范围
                    foreach ( $data_list as $key => $data ) if ( isset($rule['data'][ $data['tn'] ]) ) {
                        $data_update[] = [ 'id' => $data['id'], 'union_platform_id' => $rule['union_platform_id'], 'is_other' => 1, 'status' => 1];
                        unset($data_list[$key]);
                    }
                    break;
            }
        }

        //未知分配情况的
        if ( !empty($data_list) ) foreach( $data_list as $data ) $data_update[] = [
            'id' => $data['id'], 'union_platform_id' => 0, 'is_other' => 0, 'status' => 1
        ];

        if ( !empty($data_update) ) $dao_performance_original->add_all_duplicate_update( $data_update, ['union_platform_id', 'is_other', 'status'] );

        return count($data_update);
    }
}