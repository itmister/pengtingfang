<?php
namespace VendorData\Attachment;
abstract class Base {
    private static $_instace = [];

    protected  $_attachment_path = "";//产品附件目录

    public static function get_instance($class = __CLASS__){
        if(isset(self::$_instace[$class])){
            return self::$_instace[$class];
        }else {
            self::$_instace[$class]=new $class();
        }
        return self::$_instace[$class];
    }

    public function get_data($date = ''){
        $date  = $date ? $date:date("Y-m-d",strtotime("-1 days"));
        $file_path = sprintf($this->_attachment_path,$date);
        if (!is_file($file_path)){
            return false;
        }
        $data = $this->parseCsv($file_path);
        $data = $this->fiter_data($data);
        return $data;
    }
    
    protected function parseCsv($filename){
        $content = [];
        $handle = fopen($filename,'r');
        while (@$data = fgetcsv($handle)) //每次读取CSV里面的一行内容
        {
            if($data)
            {
                $content[] = $data;
            }
        }
        
        //关闭
        fclose($handle);
        return $content;
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