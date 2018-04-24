<?php
namespace io;

/**
 * 文件操作
 * @Author vl
 * @Date 2015-03-11
 *
 * Class file
 * @package io
 */
class file {

    /**
     * 导出csv文件
     * @param $file_name
     * @param $array
     * @param array $arr_title
     * @param boolean $return 是否返回输出的字符
     * @return bool|int
     */
    public static function array_to_csv( $file_name, $array, $arr_title = array(), $return = false) {

        if ( empty($array) ) return false;

        $arr = array();
        foreach ($array as $item ) {
            $arr[] = implode(',', $item);
        }
        $str = (!empty($arr_title) ? (implode(',', $arr_title) . "\n") : '') . implode("\n", $arr);
        if ($return) return $str;

        if (!is_dir( $dir = dirname($file_name)) ) mkdir($dir, 0777, true);
        if (empty($file_name)) return false;
        if (substr($file_name, -4, 4) != '.csv') $file_name .= '.csv';//补充扩展名
        return file_put_contents( $file_name, $str);

    }

    /**
     * csv文件导入成二维数组返回
     * @param string $file_name 文件路径
     * @param array $arr_field 列字段数组,空则使用数值下标
     * @param integer $data_start_line 数据开始行
     * @param string $key_field
     * @param array $charset_covert
     * @return array
     */
    public static function csv_to_array( $file_name, $arr_field = array(), $data_start_row = 0, $key_field = null, $charset_covert = [] ) {

        $result = array();
        if ( !is_file($file_name) ) return $result;
        $txt = trim( file_get_contents( $file_name ) );
        if ( empty($txt) ) return $result;
        if (!empty($charset_covert) && !empty($charset_covert[0]) && !empty($charset_covert[1])) $txt = iconv($charset_covert[0], $charset_covert[1], $txt);
        $rows = explode("\n", $txt);
        if ( empty($rows) ) return $result;

        if ( !empty($arr_field) ) {
            //指定数组下标
            foreach ( $rows as $row_idx => $row ) {
                if ( !empty($data_start_row) && $row_idx < $data_start_row ) continue;
                $items = explode(',', $row);
                $_item = array();
                if (!empty($items)) foreach ( $arr_field as $idx => $field )  if (isset($items[$idx])) $_item[$field] = $items[$idx];
                if (!empty($key_field) && isset($_item[$key_field])) {
                    $result[$_item[$key_field]] = $_item;
                }
                else {
                    $result[] = $_item;
                }

            }
        }
        else {
            foreach ( $rows as $row_idx => $row ) {
                if ( !empty($data_start_row) && $row_idx < $data_start_row ) continue;
                $result[] = explode(',', $row);
            }
        }
        return $result;
    }

    public static function output( $file, $data, $auto_create_dir = false, $flags = null, $suffer = '' ) {

        if (empty($file)) return false;
        $arr_search     = [':', '*', '?', '"', '<', '>', '|'];
        $arr_replace    = array_fill(0, count($arr_search) - 1, '_');
        $file = str_replace($arr_search, $arr_replace, $file);
        if ($auto_create_dir && !is_dir(dirname($file))) mkdir( dirname($file), 0777, true );

        if (!empty($suffer)){
            if ( substr($file, -1 * strlen($suffer)) != $suffer ) $file .= $suffer;
            switch ($suffer) {
                case '.php':
                    $arr = debug_backtrace();
                    $date = date('Y/m/d H:i:s');
                    $data = "<?php \n/**\n\tfile create by \\Io\\File::output\n\t{$arr[0]['file']}\n\t@Time={$date}\n**/\nreturn " . var_export($data, true) . ";";

                    break;

                case '.js':
                    $data = json_encode($data);
                    break;
            }

        }
        file_put_contents( $file, $data , $flags);

    }

    public static function input( $file, $type = '' ) {

        $txt = is_file($file) ? file_get_contents( $file ) : '';
        switch ( $type ) {
            case 'json':
                return json_decode( $txt, true );
                break;
            default:
                return $txt;
                break;
        }

    }

    /**
     * 拷贝目录（文件)
     * @param $path 源目录
     * @param $dest 目标目录
     * @return bool
     */
    public static  function copy_r( $path, $dest ){
        if( is_dir($path) ) {
            @mkdir( $dest );
            $objects = scandir($path);
            if( sizeof($objects) > 0 ) {
                foreach( $objects as $file ) {
                    if( $file == "." || $file == ".." ){
                        continue;
                    }
                    if( is_dir( $path.DIRECTORY_SEPARATOR.$file ) ) {
                        self::copy_r( $path.DIRECTORY_SEPARATOR.$file, $dest.DIRECTORY_SEPARATOR.$file );
                    } else {
                        copy( $path.DIRECTORY_SEPARATOR.$file, $dest.DIRECTORY_SEPARATOR.$file );
                    }
                }
            }
            return true;
        } elseif( is_file($path) ) {
            return copy($path, $dest);
        }
        return false;
    }
}