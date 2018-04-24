<?php
/**
 * @desc 导出csv
 */
namespace io\http\response;

class csv extends \Core\Object {

    /**
     * @return csv
     */
    public static function i() { return parent::i(); }

    /**
     * @param $data
     * @param array $head
     * @param string $file_name
     */
    public function export( $data, $head = [], $file_name = "export.csv"  ) {
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename={$file_name}.csv");
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');

        if ( !empty($head) ) {
            echo implode(',', $head), "\n";
        }
        foreach ( $data as $item ) echo implode(',', $item), "\n";

    }
}