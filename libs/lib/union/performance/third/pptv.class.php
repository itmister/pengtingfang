<?php
namespace Union\Performance\Third;

/**
 * PPTV业绩处理
 * Class Pptv
 * @package Union\Performance\Third
 */
class Pptv extends Base{

    /**
     * 取业绩
     * @param string $ymd 年月日,如:20150312
     * @return array
     */
    public function fetch( $ymd = '' ) {
        $ymd = '20150309';
        $ymd = date('Y-m-d', strtotime($ymd));
        $result = array();
        $list  = $this->_gx_webservice('pplive', $ymd, $ymd);
        foreach ( $list as $item ) {
            $replace_count = 0;
            $tn =   str_ireplace('PPTV(pplive)_3.5.3.0059_forqd1195_', '', $item['ChildTN'], $replace_count )  ;
            if ( strlen( $tn ) < 5 ) $tn = str_pad( $tn, '0', 5 - strlen( $tn ), STR_PAD_LEFT );
            if ( $replace_count && $tn ) $result[] = array(
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
        $result = \Io\File::csv_to_array( $this->_path_file_verify . 'pptv/' . $ymd . '.csv', array('uid', 'num'));
        return !empty($result) ? $result : array();
    }


}