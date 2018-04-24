<?php
namespace Union\Performance\Third;

/**
 * QQ浏览器
 * Class Qqbrowserv2
 * @package Union\Performance\Third
 */
class Qqbrowserv2 extends Base{
    /**
     * 默认执行间隔当前时间的天数,如2015-03-19执行脚本，如果值为2的时候执行程序处理的是2015-03-17的业绩
     * @var int
     */
    protected $_default_day_diff = 1;

    /**
     * 取业绩数据
     * @param string $ymd 年月日,如:20150312
     * @return array
        array(
            uid:渠道id
            num:装机量
        )
        ...
     */
    public function fetch( $ymd = '' ) {
        $attachment_path = '/app/www/jf7654/emailAttach/attachments/joyceguo@tencent.com/';
        $ymd = date('Y-m-d', strtotime($ymd));
        $file = $attachment_path . 'tencent qqbrowser_100241_' . $ymd . '.html';
        if ( !is_file($file) ) {
            echo 'file not exist:' . $file;
            return;
        }
        $txt        = file_get_contents( $file );
        $pattern    = '/<td.*"AC">(\d+)<.*<td.*"AC">(\d+)<.*<td.*"AR">(\d+)<\/td/Us';
        foreach( \Util\Tool::preg_to_array( $pattern, $txt, ['', 'uid', 'num'] ) as $item ) {
            if ( intval($item['num']) <= 0 ) continue;
            $arr[] = $item;
        }
        if (empty($arr)){
            $arr =  \Io\File::csv_to_array($file. '.csv', ['ymd', 'uid', 'num']);
            foreach($arr as &$v){
                unset($v['ymd']);
            }
        }
        return $arr;
//        print_r($arr);
    }

    /**
     * 读取本地校验好的业绩文件数据
     * @param integer $ymd 年月日 20150313
     * @return mixed
     */
    public function data_get( $ymd ) {
        $result = \Io\File::csv_to_array( $this->_path_file_verify . 'qqbrowser/' . $ymd . '.csv', array('uid', 'num'));
        return !empty($result) ? $result : array();
    }
}