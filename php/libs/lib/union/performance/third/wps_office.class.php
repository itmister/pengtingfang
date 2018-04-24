<?php
namespace Union\Performance\Third;

/**
 * PPTV业绩处理
 * Class Wps_Office
 * @package Union\Performance\Third
 */
class Wps_Office extends Base{
    /**
     * 默认执行间隔当前时间的天数,如2015-03-19执行脚本，如果值为2的时候执行程序处理的是2015-03-17的业绩
     * @var int
     */
    protected $_default_day_diff = 2;

    /**
     * 取业绩
     * @param string $ymd 年月日,如:20150312
     * @return array
     */
    public function fetch( $ymd = '' ) {
        $ymd = date('Y-m-d', strtotime($ymd));
        $result = array();
        $list  = $this->_gx_webservice( 'wps', $ymd, $ymd );
        foreach ( $list as $item ) {
            $arr = explode('.', $item['ChildTN']);
            if ( $arr[0] == 6022 && !empty( $arr[1] ) ) $result[] = array(
                'uid' => intval( $arr[1] ),
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
        $result = \Io\File::csv_to_array( $this->_path_file_verify . 'wps_office/' . $ymd . '.csv', array('uid', 'num'));
        return !empty($result) ? $result : array();
    }


}