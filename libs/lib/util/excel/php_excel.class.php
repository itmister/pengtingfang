<?php
namespace Util\Excel;
require(dirname(__FILE__) . '/PHPExcel.php');
//require(dirname(__FILE__) . '/PHPExcel/IOFactory.php');

class Php_excel extends \PHPExcel {
    protected static $_instance = null;

    /**
     * @return Php_excel
     */
    public static function i(){
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function cell_name( $column, $row ) {
        //16384max excel2007
        $result     = '';
        $column = intval($column);
        $row    = intval($row);
        if ( $column < 1 || $row < 1 ) return '';

        do {
            $remainder     = $column % 26;
            if ($remainder == 0 ) $remainder = 26;

            $column         = ($column - $remainder) / 26;
            $result         = chr(64 + $remainder) . $result;

        } while( $column > 0 );

        return $result . $row;
    }
}