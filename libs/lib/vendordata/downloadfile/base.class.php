<?php
namespace VendorData\DownloadFile;
use \Io\Http;
abstract class Base {
    private static $_instace = [];

    protected  $_remote_file_path = "";//要下载的附件目录
    protected  $_save_path  = "";  //下载文件保存的地址

    public static function get_instance($class = __CLASS__){
        if(isset(self::$_instace[$class])){
            return self::$_instace[$class];
        }else {
            self::$_instace[$class]=new $class();
        }
        return self::$_instace[$class];
    }

    /**
     * 下载文件
     * @param string $url
     * @param string $save_path 保存到本地的目录
     * @return string
     */
    function get_remote_file($url = "",$save_path = ""){
        $option['file'] = $save_path;
        return Http::get($url,'',$option);
    }

    protected  function parseXlsx($filename){
        error_reporting(0);
        $e = \Util\Excel\Php_excel::i();
        $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
        $objReader->setReadDataOnly(TRUE);
        $PHPExcel = $objReader->load($filename);
        $PHPExcel->setActiveSheetIndex(0);
        $Excel = $PHPExcel->getActiveSheet();
        $Excel_rows = $Excel->getHighestRow();
        $Column = \PHPExcel_Cell::columnIndexFromString($Excel->getHighestColumn());
        $content = array();
        for ($row = 1; $row <= $Excel_rows; $row++) {
            for ($col = 0; $col < $Column; $col++){
                $content[$row][$col] = $Excel->getCellByColumnAndRow($col, $row)->getCalculatedValue();
            }
        }
        return $content;
    }
}