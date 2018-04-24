<?php
namespace Util;

/**
 * 参数处理
 * Class Params
 * @package Util
 */

class Params {
    const PATTERN_TYPE_PHONE = '/1[0-9]{10}/';
    const PATTERN_TYPE_EMAIL = '/^[0-9a-z]([a-z0-9]*[-_]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?$/i';

    protected static $_instance = null;

    protected $_params = [];

    /**
     * @return \Util\Params
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 取参数，不加任何过滤
     * @param $param_name
     * @param string $pattern
     * @param bool $require
     * @return null|string
     * @throws \Exception
     */
    public function get( $param_name, $pattern = '', $require = false) {
        $value = $this->_get( $param_name, $require );
        if (!empty($pattern) && !preg_match( $pattern, $value))
            throw new \Exception('param_pattern_not_match', 10001);
        return $value;
    }

    /**
     * 取电话号码
     * @param $param_name
     * @return null
     * @throws \Exception
     */
    public function get_phone( $param_name, $require = false ) {
        try {
            return $this->get( $param_name, self::PATTERN_TYPE_PHONE, $require );
        } catch ( \Exception $e ) {
            throw new \Exception('phone_pattern_error', 10002 );
        }
    }

    /**
     * 取md5值参数
     * @param $param_name
     * @param $not_empty 不允许空值
     * @return string
     */
    public function get_md5( $param_name, $not_empty = false) {
        return md5( trim($this->_get($param_name, $not_empty)) );
    }

    public function get_string($param_name, $not_empty = false, $default = null ) {
        $value = trim( $this->_get( $param_name , $not_empty, $default) );
        $value = addslashes($value);
//        mysqli_escape_string(null,$value);
        return $value;
    }

    public function string( $param_name, $not_empty = false, $default = null ) {
        return $this->get_string( $param_name, $not_empty, $default );
    }

    /**
     * 取数值，有小数
     * @param $param_name
     * @param bool $not_empty
     */
    public function get_number( $param_name, $not_empty = false, $default = null ) {
        $value = floatval( $this->_get( $param_name, $not_empty, $default ) );
        return $value;
    }

    public function int($param_name , $require = false, $default = null, $range = [] ) {
        return $this->get_int($param_name, $require, $default, $range );
    }

    /**
     * 取整型参数
     * @param $param_name
     * @return int
     */
    public function get_int( $param_name , $require = false, $default = null, $range = [] ) {
        return intval( $this->_get( $param_name, $require, $default, $range) );
    }

    public function alipay( $param_name = 'alipay', $require = false, $default = null ) {
        $value = $this->get( $param_name, null, $require );
        if (!isset($value) && isset($default) ) return $default;

        if ( !preg_match( self::PATTERN_TYPE_PHONE, $value )
            && !preg_match(self::PATTERN_TYPE_EMAIL, $value )
        ) throw new \Exception('alipay_account_incorrect', 10003);

        return $value;
    }

    /**
     * 取年月日, 20150917
     * @param $pararm_name
     * @param bool $require
     * @param null $default
     * @return int|null
     */
    public function ymd( $pararm_name, $require = false, $default = null ) {
        $value = $this->get_string($pararm_name);
        if ( empty($value) ) $value = $default;
        return intval( date('Ymd', strtotime( $value)) );
    }

    /**
     * 获取上传文件
     * @param string $param_name 参数名,对应file控件的name
     * @param string $upload_path 上传保存的路径
     * @param array $options 选项
        [
            type : image //文件类型
            image_height : //图像高,文件类型为image有效
            image_width : //图像宽,文件类型为image有效
        ]
     * @return string
     */
    public function get_file( $param_name = null, $upload_path='./uploads/', $options = [] ) {
        $result = $this->get_file_list( $upload_path );
        if ( !empty($result) && !empty($param_name) ) foreach ($result as $file_info ) if ( $file_info['key'] == $param_name ) {
            $file_path = $file_info['path'];
            //文件处理
            if ( !empty($options['type']) ) {
                switch ( $options['type']) {
                    case 'image' :
                        if ( !empty( $options['image_height'] ) && !empty( $options['image_width']) ) {
                            \Util\File::image_resize( $file_path, $file_path, $options['image_height'], $options['image_width'] );
                        }

                        break;
                }
            }

            return $file_path;
        }
        return '';
    }

    public function get_file_list( $upload_path = './uploads/' ) {
        static $upload_file_list = null;
        if (is_array($upload_file_list)) return $upload_file_list;

        $upload_file_list   = [];
        $upload             = new \Think\Upload( ['rootPath' => $upload_path] );
        foreach ( $upload->upload() as $file )  {
            $file['path'] = $upload_path . $file['savepath'] . $file['savename'];
            $upload_file_list[] = $file;
        }
        return $upload_file_list;
    }

    public function set($name, $value) {
        if (empty($name) ) return false;
        $this->_params[$name] = $value;
    }

    /**
     * 取 POST 提交的原始数据
     * @return string
     */
    public function raw_post_data() {
       return file_get_contents("php://input");
    }

    /**
     * 转换上传文件数组变量为正确的方式
     * @access private
     * @param array $files  上传的文件变量
     * @return array
     */
    private function _deal_files($files) {
        $fileArray  = array();
        $n          = 0;
        foreach ($files as $key=>$file){
            if(is_array($file['name'])) {
                $keys       =   array_keys($file);
                $count      =   count($file['name']);
                for ($i=0; $i<$count; $i++) {
                    $fileArray[$n]['key'] = $key;
                    foreach ($keys as $_key){
                        $fileArray[$n][$_key] = $file[$_key][$i];
                    }
                    $n++;
                }
            }else{
                $fileArray[$key] = $file;
            }
        }
        return $fileArray;
    }

    /**
     * @param $param_name
     * @param bool $require
     * @param null $default
     * @param array $range 指定范围，如果值不在指定的范围则取默认值
     * @return null|string
     * @throws \Exception
     */
    protected function _get($param_name, $require = false, $default = null, $range = [] ) {
        $value = null;
        if (isset($_GET[$param_name])) {
            $value = $_GET[$param_name];
        }
        elseif (isset($_POST[$param_name])) {
            $value = $_POST[$param_name];
        }
        elseif (isset($this->_params[$param_name])) {
            $value = $this->_params[$param_name];
        }

        if ( $require && !isset($value) ) throw new \Exception( $param_name . ',param_error', 10001 );
        if (!isset($value) && isset($default)) $value = $default;

        if ( !empty($range) && is_array($range) && !in_array($value, $range)) $value = $default;
        return $value;
    }


}