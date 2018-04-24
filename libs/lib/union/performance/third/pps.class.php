<?php
namespace Union\Performance\Third;

/**
 * PPS业绩处理
 * Class Pps
 * @package Union\Performance\Third
 */
class Pps extends Base{

    /**
     * 取业绩
     * @param string $ymd 年月日,如:20150312
     * @return array
     */
    public function fetch( $ymd = '' ) {
        $ymd = date('Y-m-d', strtotime($ymd));
        $result = array();
        $list  = $this->_gx_webservice('pps', $ymd, $ymd);
        foreach ( $list as $item ) {
            $tn = trim( $item['ChildTN'] );
            if ( strlen( $tn ) < 6 ) $tn = str_pad( $tn, '0', 6 - strlen( $tn ), STR_PAD_LEFT );

            if ( !empty( $tn ) ) $result[] = array(
                'uid' => $tn,
                'num' => intval( $item['InstallNum'] )
            );
        }
        return $result;
    }

    /**
     * 读取本地校验好的业绩文件数据
     * @param integer $ymd 年月日 20150313
     * @return mixed
     */
    public function data_get( $ymd ) {
        $result = \Io\File::csv_to_array( $this->_path_file_verify . 'pps/' . $ymd . '.csv', array('uid', 'num'));
        return !empty($result) ? $result : array();
    }


}