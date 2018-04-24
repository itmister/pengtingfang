<?php
/**
 * @desc Debug 日志
 */
namespace Util;

class Debug  {

    public static function log( $msg, $title = '',$filename = 'error.log') {
        $date_time = date("Y-m-d H:i:s");
        if (is_array( $msg ) ) {
            $result = "\tarray\t" . json_encode($msg);
        }
        else if (is_object( $msg )) {
            $result = "\tobject\t" . json_encode($msg);
        }
        else {
            $result = $msg;
        }

        if (!empty($title)) $title .= "\t";

        $result = "{$date_time}\t{$title}{$result}\n";
        if ( defined('DEBUG_OUTPUT_CONSOLE') && !empty(DEBUG_OUTPUT_CONSOLE)) echo $result;
        error_log( $result, 3, PATH_RUNTIME . '/'.$filename);
    }

    public static function write_log($msg,$title = '',$logname) {
        $date_time = date("Y-m-d H:i:s");
        if (is_array( $msg ) ) {
            $result = "\tarray\t" . json_encode($msg);
        }
        else if (is_object( $msg )) {
            $result = "\tobject\t" . json_encode($msg);
        }
        else {
            $result = $msg;
        }

        if (!empty($title)) $title .= "\t";
        if (empty($logname)) $logname = date("Ymd").".log";

        $result = "{$date_time}\t{$title}{$result}\n";
        if ( defined('DEBUG_OUTPUT_CONSOLE') && !empty(DEBUG_OUTPUT_CONSOLE)) echo $result;
        error_log($result,3,PATH_RUNTIME . '/'.$logname);
    }
}